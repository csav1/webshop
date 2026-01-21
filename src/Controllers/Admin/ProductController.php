<?php

namespace Controllers\Admin;

use Core\View;
use Core\Session;
use Core\Auth;
use Core\Validator;
use Models\Product;
use Models\Category;

/**
 * Admin ProductController - Produktverwaltung
 */
class ProductController
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    /**
     * Alle Produkte anzeigen
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $products = Product::adminPaginate($page, 20);

        View::render('admin/products/index', [
            'seo' => ['title' => 'Produkte verwalten'],
            'products' => $products['items'],
            'pagination' => $products
        ], 'layouts/admin');
    }

    /**
     * Produkt erstellen - Formular
     */
    public function create(): void
    {
        $categories = Category::active();

        View::render('admin/products/create', [
            'seo' => ['title' => 'Neues Produkt'],
            'categories' => $categories
        ], 'layouts/admin');
    }

    /**
     * Produkt speichern
     */
    public function store(): void
    {
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            redirect('/admin/produkte/neu');
            return;
        }

        $validator = Validator::make($_POST, [
            'name' => 'required|min:2|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|decimal',
            'stock' => 'required|integer',
            'description' => 'required|min:10'
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->firstErrors());
            Session::flash('old', $_POST);
            redirect('/admin/produkte/neu');
            return;
        }

        // Bild-Upload verarbeiten
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->handleImageUpload($_FILES['image']);
        }

        $productId = Product::createProduct([
            'category_id' => (int) $_POST['category_id'],
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'short_description' => $_POST['short_description'] ?? null,
            'price' => (float) $_POST['price'],
            'sale_price' => !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : null,
            'stock' => (int) $_POST['stock'],
            'sku' => $_POST['sku'] ?? null,
            'image' => $imageName,
            'is_active' => isset($_POST['is_active']),
            'is_featured' => isset($_POST['is_featured'])
        ]);

        Session::success('Produkt wurde erstellt.');
        redirect('/admin/produkte');
    }

    /**
     * Produkt bearbeiten - Formular
     */
    public function edit(int $id): void
    {
        $product = Product::find($id);

        if (!$product) {
            Session::error('Produkt nicht gefunden.');
            redirect('/admin/produkte');
            return;
        }

        $categories = Category::active();

        View::render('admin/products/edit', [
            'seo' => ['title' => 'Produkt bearbeiten'],
            'product' => $product,
            'categories' => $categories
        ], 'layouts/admin');
    }

    /**
     * Produkt aktualisieren
     */
    public function update(int $id): void
    {
        $product = Product::find($id);

        if (!$product) {
            Session::error('Produkt nicht gefunden.');
            redirect('/admin/produkte');
            return;
        }

        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            redirect('/admin/produkte/' . $id . '/bearbeiten');
            return;
        }

        $validator = Validator::make($_POST, [
            'name' => 'required|min:2|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|decimal',
            'stock' => 'required|integer'
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->firstErrors());
            redirect('/admin/produkte/' . $id . '/bearbeiten');
            return;
        }

        $data = [
            'category_id' => (int) $_POST['category_id'],
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? '',
            'short_description' => $_POST['short_description'] ?? null,
            'price' => (float) $_POST['price'],
            'sale_price' => !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : null,
            'stock' => (int) $_POST['stock'],
            'sku' => $_POST['sku'] ?? null,
            'is_active' => isset($_POST['is_active']),
            'is_featured' => isset($_POST['is_featured'])
        ];

        // Neues Bild?
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $data['image'] = $this->handleImageUpload($_FILES['image']);
            // Altes Bild löschen
            if ($product['image']) {
                $this->deleteImage($product['image']);
            }
        }

        // Slug aktualisieren wenn Name geändert
        if ($product['name'] !== $_POST['name']) {
            $data['slug'] = Product::generateSlug($_POST['name']);
        }

        Product::update($id, $data);
        Session::success('Produkt wurde aktualisiert.');
        redirect('/admin/produkte');
    }

    /**
     * Produkt löschen
     */
    public function delete(int $id): void
    {
        $product = Product::find($id);

        if (!$product) {
            Session::error('Produkt nicht gefunden.');
            redirect('/admin/produkte');
            return;
        }

        // Bild löschen
        if ($product['image']) {
            $this->deleteImage($product['image']);
        }

        Product::delete($id);
        Session::success('Produkt wurde gelöscht.');
        redirect('/admin/produkte');
    }

    /**
     * Bild hochladen
     */
    private function handleImageUpload(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file['type'], $allowedTypes)) {
            Session::warning('Ungültiges Bildformat. Erlaubt: JPG, PNG, WebP');
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('product_') . '.' . $extension;
        $uploadPath = __DIR__ . '/../../../public/assets/images/products/';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
            return $filename;
        }

        return null;
    }

    /**
     * Bild löschen
     */
    private function deleteImage(string $filename): void
    {
        $path = __DIR__ . '/../../../public/assets/images/products/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
