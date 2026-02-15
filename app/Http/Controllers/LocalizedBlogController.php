<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * LocalizedBlogController
 *
 * Handles blog post detail pages using localized slugs from blog_post_translations table.
 * Used when multilang.phases.blog_translations is enabled.
 *
 * SSR approach: All content sections are server-side rendered for SEO crawlability,
 * instead of relying on HTMX lazy-loading which is invisible to crawlers.
 */
class LocalizedBlogController extends Controller
{
    /**
     * Display blog post detail page using localized slug.
     *
     * @param string $locale The locale from route parameter (set by middleware)
     * @param string $slug The localized blog post slug
     * @return View|RedirectResponse
     */
    public function show(string $locale, string $slug): View|RedirectResponse
    {
        // Find translation by locale and slug, with blog post + all relations needed for SSR
        $translation = BlogPostTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->with(['blogPost' => function ($query) {
                $query->with([
                    'category',
                    'tags',
                    'city',
                ]);
            }])
            ->first();

        // Try 2: Slug belongs to another locale — find the post, then get translation for requested locale
        if (!$translation) {
            $anyTranslation = BlogPostTranslation::where('slug', $slug)
                ->with(['blogPost' => fn($q) => $q->with(['category', 'tags', 'city'])])
                ->first();

            if ($anyTranslation) {
                $translation = BlogPostTranslation::where('blog_post_id', $anyTranslation->blog_post_id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $translation->load(['blogPost' => fn($q) => $q->with(['category', 'tags', 'city'])]);
                }
            }
        }

        // Try 3: Slug matches blog_posts.slug directly
        if (!$translation) {
            $post = BlogPost::where('slug', $slug)
                ->where('is_published', true)
                ->with(['category', 'tags', 'city'])
                ->first();

            if ($post) {
                $translation = $post->translations()->where('locale', $locale)->first();
                if ($translation) {
                    $translation->setRelation('blogPost', $post);
                }
            }
        }

        // Fallback: requested locale missing → redirect to English version
        if (!$translation || !$translation->blogPost) {
            if ($locale !== 'en') {
                $enTranslation = $this->findEnglishTranslation($slug);
                if ($enTranslation) {
                    return redirect("/en/blog/{$enTranslation->slug}", 301);
                }
            }
            abort(404);
        }

        $post = $translation->blogPost;

        // Increment view count
        BlogPost::where('id', $post->id)->increment('view_count');

        // Prepare SEO data - prefer translation fields, fallback to post
        $pageTitle = $translation->seo_title ?? $translation->title ?? $post->meta_title ?? $post->title;
        $metaDescription = $translation->seo_description ?? $translation->excerpt ?? $post->meta_description
            ?? \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160);

        // Use featured image from post or fallback
        $ogImage = $post->featured_image_url ?? asset('images/og-default.jpg');

        // Canonical URL points to localized version
        $canonicalUrl = url("/{$locale}/blog/{$slug}");

        // Get related posts (same category, fill with recent if needed)
        $relatedPosts = collect();
        if ($post->category) {
            $relatedPosts = $post->category->posts()
                ->published()
                ->where('id', '!=', $post->id)
                ->latest('published_at')
                ->limit(3)
                ->get();
        }
        if ($relatedPosts->count() < 3) {
            $additionalPosts = BlogPost::published()
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->latest('published_at')
                ->limit(3 - $relatedPosts->count())
                ->get();
            $relatedPosts = $relatedPosts->merge($additionalPosts);
        }

        // Get related tours
        $relatedTours = $post->getRelatedTours(3);

        // Get recent posts for sidebar (excluding current)
        $recentPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('blog.article', compact(
            'post',
            'translation',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'relatedPosts',
            'relatedTours',
            'recentPosts'
        ));
    }

    /**
     * Find the English translation for a blog post identified by slug.
     */
    private function findEnglishTranslation(string $slug): ?BlogPostTranslation
    {
        $enTranslation = BlogPostTranslation::where('locale', 'en')
            ->where('slug', $slug)
            ->first();

        if ($enTranslation) {
            return $enTranslation;
        }

        $anyTranslation = BlogPostTranslation::where('slug', $slug)->first();
        if ($anyTranslation) {
            return BlogPostTranslation::where('blog_post_id', $anyTranslation->blog_post_id)
                ->where('locale', 'en')
                ->first();
        }

        $post = BlogPost::where('slug', $slug)->first();
        if ($post) {
            return $post->translations()->where('locale', 'en')->first();
        }

        return null;
    }
}
