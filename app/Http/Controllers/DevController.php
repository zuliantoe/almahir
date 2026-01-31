<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * DevController
 * 
 * Controller for development-only routes such as the UI Style Guide.
 * These routes should only be accessible in debug/development mode.
 * 
 * @author SIAKAD Development Team
 */
class DevController extends Controller
{
    /**
     * Display the UI Style Guide.
     * 
     * This page shows live demos of all standardized UI components
     * with copy-paste code snippets for the development team.
     * 
     * @return \Illuminate\View\View
     */
    public function uiGuide(): View
    {
        // Abort if not in debug mode (production safety)
        if (!config('app.debug')) {
            abort(404);
        }

        return view('dev.ui-guide', [
            'title' => 'UI Style Guide',
            'breadcrumb' => 'Developer / UI Guide',
        ]);
    }
}
