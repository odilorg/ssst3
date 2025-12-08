<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, string $locale)
    {
        // Validate locale against active languages
        $language = Language::findByCode($locale);

        if (!$language || !$language->is_active) {
            return redirect()->back()->with('error', 'Invalid language selected');
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Redirect back to previous page
        return redirect()->back()->with('success', "Language switched to {$language->native_name}");
    }

    /**
     * Get available languages (API endpoint)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $languages = Language::getActive();

        return response()->json([
            'languages' => $languages,
            'current' => app()->getLocale(),
            'default' => Language::getDefault()?->code ?? 'en',
        ]);
    }
}
