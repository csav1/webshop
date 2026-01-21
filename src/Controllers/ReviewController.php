<?php

namespace Controllers;

use Core\View;
use Core\Session;
use Core\Auth;
use Core\Validator;
use Models\Review;
use Models\Product;

/**
 * ReviewController - Produktbewertungen
 */
class ReviewController
{
    /**
     * Bewertung erstellen
     */
    public function store(): void
    {
        Auth::requireAuth();

        // CSRF prüfen
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            $this->redirectBack();
            return;
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $product = Product::find($productId);

        if (!$product) {
            Session::error('Produkt nicht gefunden.');
            redirect('/produkte');
            return;
        }

        // Prüfen ob Benutzer bereits bewertet hat
        if (Review::hasUserReviewed(Auth::id(), $productId)) {
            Session::error('Sie haben dieses Produkt bereits bewertet.');
            redirect('/produkt/' . $product['slug']);
            return;
        }

        $validator = Validator::make($_POST, [
            'rating' => 'required|integer|in:1,2,3,4,5',
            'title' => 'max:255',
            'content' => 'max:2000'
        ], [
            'rating.required' => 'Bitte wählen Sie eine Bewertung.',
            'rating.in' => 'Ungültige Bewertung.'
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->firstErrors());
            redirect('/produkt/' . $product['slug']);
            return;
        }

        Review::createReview($productId, Auth::id(), [
            'rating' => (int) $_POST['rating'],
            'title' => trim($_POST['title'] ?? ''),
            'content' => trim($_POST['content'] ?? '')
        ]);

        Session::success('Vielen Dank für Ihre Bewertung!');
        redirect('/produkt/' . $product['slug']);
    }

    /**
     * Als hilfreich markieren
     */
    public function helpful(): void
    {
        Auth::requireAuth();

        $reviewId = (int) ($_POST['review_id'] ?? 0);

        if ($reviewId <= 0) {
            if ($this->isAjax()) {
                $this->jsonResponse(['success' => false, 'message' => 'Ungültige Anfrage.']);
            }
            Session::error('Ungültige Anfrage.');
            $this->redirectBack();
            return;
        }

        $review = Review::find($reviewId);
        if (!$review) {
            if ($this->isAjax()) {
                $this->jsonResponse(['success' => false, 'message' => 'Bewertung nicht gefunden.']);
            }
            Session::error('Bewertung nicht gefunden.');
            $this->redirectBack();
            return;
        }

        // Eigene Bewertung nicht als hilfreich markieren
        if ($review['user_id'] === Auth::id()) {
            if ($this->isAjax()) {
                $this->jsonResponse(['success' => false, 'message' => 'Sie können Ihre eigene Bewertung nicht als hilfreich markieren.']);
            }
            Session::error('Sie können Ihre eigene Bewertung nicht als hilfreich markieren.');
            $this->redirectBack();
            return;
        }

        if (Review::markHelpful($reviewId, Auth::id())) {
            $updatedReview = Review::find($reviewId);

            if ($this->isAjax()) {
                $this->jsonResponse([
                    'success' => true,
                    'helpfulCount' => $updatedReview['helpful_count']
                ]);
            }
            Session::success('Danke für Ihr Feedback!');
        } else {
            if ($this->isAjax()) {
                $this->jsonResponse(['success' => false, 'message' => 'Sie haben diese Bewertung bereits als hilfreich markiert.']);
            }
            Session::info('Sie haben diese Bewertung bereits als hilfreich markiert.');
        }

        $this->redirectBack();
    }

    /**
     * Bewertung löschen (nur eigene oder Admin)
     */
    public function delete(): void
    {
        Auth::requireAuth();

        $reviewId = (int) ($_POST['review_id'] ?? 0);
        $review = Review::find($reviewId);

        if (!$review) {
            Session::error('Bewertung nicht gefunden.');
            $this->redirectBack();
            return;
        }

        // Nur eigene Bewertungen oder Admin
        if ($review['user_id'] !== Auth::id() && !Auth::isAdmin()) {
            Session::error('Keine Berechtigung.');
            $this->redirectBack();
            return;
        }

        Review::delete($reviewId);
        Session::success('Bewertung wurde gelöscht.');
        $this->redirectBack();
    }

    /**
     * AJAX Request prüfen
     */
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * JSON Response
     */
    private function jsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Zum Referrer zurückleiten
     */
    private function redirectBack(): void
    {
        $referrer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referrer);
    }
}
