<?php

namespace Controllers;

use Core\View;
use Core\Auth;
use Models\Product;
use Models\Category;
use Models\Review;

/**
 * HomeController - Startseite
 */
class HomeController
{
    public function index(): void
    {
        // Featured Produkte
        $featuredProducts = Product::featured(8);

        // Kategorien mit Produktanzahl
        $categories = Category::withProductCount();

        // Neueste Bewertungen
        $latestReviews = Review::latest(3);

        View::render('home/index', [
            'seo' => [
                'title' => 'NBA Shop - Offizielle Merchandise',
                'description' => 'Ihr Online-Shop für offizielle NBA Trikots, Sneaker, Caps und mehr. Schneller Versand, Top-Qualität!'
            ],
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'latestReviews' => $latestReviews
        ]);
    }

    public function about(): void
    {
        View::render('pages/about', [
            'seo' => [
                'title' => 'Über uns',
                'description' => 'Erfahren Sie mehr über den NBA Shop und unsere Mission.'
            ]
        ]);
    }

    public function contact(): void
    {
        View::render('pages/contact', [
            'seo' => [
                'title' => 'Kontakt',
                'description' => 'Kontaktieren Sie uns bei Fragen zu Ihrer Bestellung oder unseren Produkten.'
            ]
        ]);
    }
}
