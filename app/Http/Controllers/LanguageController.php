<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch the application locale.
     */
    public function switch(Request $request)
    {
        $locale = $request->input('locale', 'ar');
        
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }
        
        session(['locale' => $locale]);
        
        return back();
    }
}
