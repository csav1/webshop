<?php

namespace Controllers\Admin;

use Core\View;
use Core\Session;
use Core\Auth;
use Core\Validator;
use Models\Category;

/**
 * Admin CategoryController - Kategorienverwaltung
 */
class CategoryController
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    /**
     * Alle Kategorien anzeigen
     */
    public function index(): void
    {
        $categories = Category::withProductCount();

        View::render('admin/categories/index', [
            'seo' => ['title' => 'Kategorien verwalten'],
            'categories' => $categories
        ], 'layouts/admin');
    }

    /**
     * Kategorie erstellen - Formular
     */
    public function create(): void
    {
        View::render('admin/categories/create', [
            'seo' => ['title' => 'Neue Kategorie']
        ], 'layouts/admin');
    }

    /**
     * Kategorie speichern
     */
    public function store(): void
    {
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            redirect('/admin/kategorien/neu');
            return;
        }

        $validator = Validator::make($_POST, [
            'name' => 'required|min:2|max:100',
            'description' => 'max:1000'
        ], [
            'name.required' => 'Bitte geben Sie einen Kategorienamen ein.',
            'name.min' => 'Der Name muss mindestens 2 Zeichen lang sein.'
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->firstErrors());
            Session::flash('old', $_POST);
            redirect('/admin/kategorien/neu');
            return;
        }

        // Bild-Upload verarbeiten
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->handleImageUpload($_FILES['image']);
        }

        Category::createCategory([
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? null,
            'image' => $imageName,
            'is_active' => isset($_POST['is_active'])
        ]);

        Session::success('Kategorie wurde erstellt.');
        redirect('/admin/kategorien');
    }

    /**
     * Kategorie bearbeiten - Formular
     */
    public function edit(int $id): void
    {
        $category = Category::find($id);

        if (!$category) {
            Session::error('Kategorie nicht gefunden.');
            redirect('/admin/kategorien');
            return;
        }

        View::render('admin/categories/edit', [
            'seo' => ['title' => 'Kategorie bearbeiten'],
            'category' => $category
        ], 'layouts/admin');
    }

    /**
     * Kategorie aktualisieren
     */
    public function update(int $id): void
    {
        $category = Category::find($id);

        if (!$category) {
            Session::error('Kategorie nicht gefunden.');
            redirect('/admin/kategorien');
            return;
        }

        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            redirect('/admin/kategorien/' . $id . '/bearbeiten');
            return;
        }

        $validator = Validator::make($_POST, [
            'name' => 'required|min:2|max:100',
            'description' => 'max:1000'
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->firstErrors());
            redirect('/admin/kategorien/' . $id . '/bearbeiten');
            return;
        }

        $data = [
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? null,
            'is_active' => isset($_POST['is_active'])
        ];

        // Neues Bild?
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $data['image'] = $this->handleImageUpload($_FILES['image']);
            // Altes Bild löschen
            if ($category['image']) {
                $this->deleteImage($category['image']);
            }
        }

        Category::updateCategory($id, $data);
        Session::success('Kategorie wurde aktualisiert.');
        redirect('/admin/kategorien');
    }

    /**
     * Kategorie löschen
     */
    public function delete(int $id): void
    {
        $category = Category::find($id);

        if (!$category) {
            Session::error('Kategorie nicht gefunden.');
            redirect('/admin/kategorien');
            return;
        }

        // Prüfen ob Kategorie Produkte hat
        $productCount = \Models\Product::count('category_id = ?', [$id]);
        if ($productCount > 0) {
            Session::error("Kategorie kann nicht gelöscht werden. Es sind noch {$productCount} Produkte zugeordnet.");
            redirect('/admin/kategorien');
            return;
        }

        // Bild löschen
        if ($category['image']) {
            $this->deleteImage($category['image']);
        }

        Category::delete($id);
        Session::success('Kategorie wurde gelöscht.');
        redirect('/admin/kategorien');
    }

    /**
     * Kategorie aktivieren/deaktivieren
     */
    public function toggleActive(int $id): void
    {
        $category = Category::find($id);

        if (!$category) {
            Session::error('Kategorie nicht gefunden.');
            redirect('/admin/kategorien');
            return;
        }

        Category::update($id, ['is_active' => !$category['is_active']]);

        $status = $category['is_active'] ? 'deaktiviert' : 'aktiviert';
        Session::success("Kategorie wurde {$status}.");
        redirect('/admin/kategorien');
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
        $filename = uniqid('category_') . '.' . $extension;
        $uploadPath = __DIR__ . '/../../../public/assets/images/categories/';

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
        $path = __DIR__ . '/../../../public/assets/images/categories/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
