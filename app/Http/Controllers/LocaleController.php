<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Change the application locale
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change(string $locale)
    {
        // Get available locales
        $availableLocales = array_keys(config('locales.available'));
        
        // Validate locale
        if (!in_array($locale, $availableLocales)) {
            abort(404);
        }
        
        // Store locale in session
        session(['locale' => $locale]);
        
        // Set app locale
        app()->setLocale($locale);
        
        // Redirect back with success message
        return redirect()->back()->with('success', __('Language changed successfully'));
    }
}
