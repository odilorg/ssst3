<?php

namespace App\Http\Controllers;

use App\Models\BlogPostTranslation;
use Illuminate\View\View;

/**
 * LocalizedBlogController
 *
 * Handles blog post detail pages using localized slugs from blog_post_translations table.
 * Used when multilang.phases.blog_translations is enabled.
 */
class LocalizedBlogController extends Controller
{
    /**
     * Display blog post detail page using localized slug.
     *
     * @param string $locale The locale from route parameter (set by middleware)
     * @param string $slug The localized blog post slug
     * @return View
     */
    public function show(string $locale, string $slug): View
    {
        // Find translation by locale and slug, with blog post relationship
        $translation = BlogPostTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->with(['blogPost' => function ($query) {
                $query->with([
                    'category',
                    'tags',
                    'city',
                ]);
            }])
            ->firstOrFail();

        $post = $translation->blogPost;

        // Increment view count
        $post->incrementViews();

        // Prepare SEO data - prefer translation fields, fallback to post
        $pageTitle = $translation->seo_title ?? $translation->title ?? $post->meta_title ?? $post->title;
        $metaDescription = $translation->seo_description ?? $translation->excerpt ?? $post->meta_description;

        // Use featured image from post or fallback
        $ogImage = $post->featured_image_url ?? asset('images/og-default.jpg');

        // Canonical URL points to localized version
        $canonicalUrl = url("/{$locale}/blog/{$slug}");

        // Get related posts
        $relatedPosts = $post->category
            ? $post->category->posts()
                ->published()
                ->where('id', '!=', $post->id)
                ->limit(3)
                ->get()
            : collect();

        // Get related tours
        $relatedTours = $post->getRelatedTours(3);

        return view('pages.blog-post', compact(
            'post',
            'translation',
            'pageTitle',
            'metaDescription',
            'ogImage',
            'canonicalUrl',
            'relatedPosts',
            'relatedTours'
        ));
    }
}
