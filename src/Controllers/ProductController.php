<?php

namespace Controllers;

use Core\View;
use Core\Session;
use Models\Product;
use Models\Category;
use Models\Review;

/**
 * ProductController - Produkte und Kategorien
 */
class ProductController
{
    /**
     * Alle Produkte anzeigen (mit optionalem Kategoriefilter)
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $categorySlug = $_GET['kategorie'] ?? null;
        $search = $_GET['suche'] ?? null;

        $seoTitle = 'Alle Produkte';
        $seoDescription = 'Entdecken Sie unser komplettes Sortiment an NBA Merchandise.';

        if ($search) {
            $result = Product::search($search, $page);
            $seoTitle = "Suche: {$search}";
            $seoDescription = "Suchergebnisse für '{$search}' im NBA Shop";
        } elseif ($categorySlug) {
            $category = Category::findBySlug($categorySlug);
            if (!$category) {
                $this->notFound();
                return;
            }
            $result = Product::byCategorySlug($categorySlug, $page);
            $result['category'] = $category;
            $seoTitle = $category['name'];
            $seoDescription = $category['description'] ?? "NBA {$category['name']} im Online-Shop";
        } else {
            $result = Product::paginate($page, 100, 'is_active = 1');
        }

        // Kategorien für Filter
        $categories = Category::withProductCount();

        View::render('products/index', [
            'seo' => [
                'title' => $seoTitle,
                'description' => $seoDescription
            ],
            'products' => $result['items'],
            'pagination' => $result,
            'categories' => $categories,
            'currentCategory' => $categorySlug,
            'searchQuery' => $search
        ]);
    }

    /**
     * Einzelnes Produkt anzeigen
     */
    public function show(string $slug): void
    {
        $product = Product::findBySlug($slug);

        if (!$product || !$product['is_active']) {
            $this->notFound();
            return;
        }

        // Bewertungen laden
        $reviewSort = $_GET['sort'] ?? 'helpful';
        $reviewData = Review::findByProduct($product['id'], 1, 50, $reviewSort);
        $ratingDistribution = Review::ratingDistribution($product['id']);

        // Review Stats für die View
        $reviewStats = [
            'avg_rating' => Review::averageRating($product['id']),
            'total_reviews' => Review::countForProduct($product['id'])
        ];

        // Ähnliche Produkte
        $similarProducts = Product::similar($product['id']);

        View::render('products/show', [
            'seo' => [
                'title' => $product['name'],
                'description' => $product['short_description'] ?? substr($product['description'], 0, 160),
                'image' => url('/assets/images/products/' . $product['image']),
                'type' => 'product'
            ],
            'product' => $product,
            'reviews' => $reviewData['items'],
            'reviewStats' => $reviewStats,
            'reviewSort' => $reviewSort,
            'ratingDistribution' => $ratingDistribution,
            'relatedProducts' => $similarProducts
        ]);
    }

    /**
     * Produkte nach Kategorie
     */
    public function category(string $slug): void
    {
        $_GET['kategorie'] = $slug;
        $this->index();
    }

    /**
     * Produktsuche
     */
    public function search(): void
    {
        $query = trim($_GET['q'] ?? '');

        if (empty($query)) {
            redirect('/produkte');
        }

        $_GET['suche'] = $query;
        $this->index();
    }

    /**
     * 404 für Produkte
     */
    private function notFound(): void
    {
        http_response_code(404);
        View::render('errors/404', [
            'seo' => ['title' => 'Produkt nicht gefunden'],
            'message' => 'Das gesuchte Produkt existiert nicht oder ist nicht mehr verfügbar.'
        ]);
    }
}
