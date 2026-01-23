<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PageController extends Controller
{
    public function documentation()
    {
        return Inertia::render('Pages/Documentation');
    }

    public function apiReference()
    {
        return Inertia::render('Pages/ApiReference');
    }

    public function helpCenter()
    {
        return Inertia::render('Pages/HelpCenter');
    }

    public function blog()
    {
        return Inertia::render('Pages/Blog');
    }

    public function privacyPolicy()
    {
        return Inertia::render('Pages/PrivacyPolicy');
    }

    public function termsOfService()
    {
        return Inertia::render('Pages/TermsOfService');
    }

    public function cookiePolicy()
    {
        return Inertia::render('Pages/CookiePolicy');
    }

    public function gdpr()
    {
        return Inertia::render('Pages/Gdpr');
    }

    public function support()
    {
        return Inertia::render('Pages/Support');
    }
}
