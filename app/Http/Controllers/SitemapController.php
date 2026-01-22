<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        $baseUrl = config('app.url');
        $now = now()->toAtomString();
        
        // Home page
        $sitemap .= $this->url($baseUrl . '/', $now, '1.0', 'daily');
        
        // Public pages
        $sitemap .= $this->url($baseUrl . '/login', $now, '0.8', 'monthly');
        $sitemap .= $this->url($baseUrl . '/register', $now, '0.8', 'monthly');
        
        $sitemap .= '</urlset>';
        
        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }
    
    private function url(string $loc, string $lastmod, string $priority, string $changefreq): string
    {
        return "  <url>\n" .
               "    <loc>{$loc}</loc>\n" .
               "    <lastmod>{$lastmod}</lastmod>\n" .
               "    <priority>{$priority}</priority>\n" .
               "    <changefreq>{$changefreq}</changefreq>\n" .
               "  </url>\n";
    }
}
