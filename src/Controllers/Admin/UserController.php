<?php

namespace Controllers\Admin;

use Core\View;
use Core\Session;
use Core\Auth;
use Core\Validator;
use Models\User;
use Models\Order;

/**
 * Admin UserController - Benutzerverwaltung
 */
class UserController
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    /**
     * Alle Benutzer anzeigen
     */
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $users = User::paginate($page, 20, '1=1', [], 'created_at', 'DESC');

        View::render('admin/users/index', [
            'seo' => ['title' => 'Benutzer verwalten'],
            'users' => $users['items'],
            'pagination' => $users
        ], 'layouts/admin');
    }

    /**
     * Benutzerdetails anzeigen
     */
    public function show(int $id): void
    {
        $user = User::withOrderStats($id);

        if (!$user) {
            Session::error('Benutzer nicht gefunden.');
            redirect('/admin/benutzer');
            return;
        }

        // Letzte Bestellungen des Benutzers
        $orders = Order::findByUser($id, 1, 5);

        View::render('admin/users/show', [
            'seo' => ['title' => 'Benutzer: ' . $user['name']],
            'user' => $user,
            'orders' => $orders['items']
        ], 'layouts/admin');
    }

    /**
     * Benutzer bearbeiten - Formular
     */
    public function edit(int $id): void
    {
        $user = User::find($id);

        if (!$user) {
            Session::error('Benutzer nicht gefunden.');
            redirect('/admin/benutzer');
            return;
        }

        View::render('admin/users/edit', [
            'seo' => ['title' => 'Benutzer bearbeiten'],
            'user' => $user
        ], 'layouts/admin');
    }

    /**
     * Benutzer aktualisieren
     */
    public function update(int $id): void
    {
        $user = User::find($id);

        if (!$user) {
            Session::error('Benutzer nicht gefunden.');
            redirect('/admin/benutzer');
            return;
        }

        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            redirect('/admin/benutzer/' . $id . '/bearbeiten');
            return;
        }

        $rules = [
            'name' => 'required|min:2|max:100',
            'role' => 'required|in:user,admin'
        ];

        // E-Mail nur prüfen wenn geändert
        if ($_POST['email'] !== $user['email']) {
            $rules['email'] = 'required|email|unique:users,email,' . $id;
        }

        $validator = Validator::make($_POST, $rules);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->firstErrors());
            redirect('/admin/benutzer/' . $id . '/bearbeiten');
            return;
        }

        User::update($id, [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'is_active' => (int) isset($_POST['is_active'])
        ]);

        Session::success('Benutzer wurde aktualisiert.');
        redirect('/admin/benutzer');
    }

    /**
     * Benutzer aktivieren/deaktivieren
     */
    public function toggleActive(int $id): void
    {
        $user = User::find($id);

        if (!$user) {
            Session::error('Benutzer nicht gefunden.');
            redirect('/admin/benutzer');
            return;
        }

        // Sich selbst nicht deaktivieren
        if ($id === Auth::id()) {
            Session::error('Sie können sich nicht selbst deaktivieren.');
            redirect('/admin/benutzer');
            return;
        }

        if ($user['is_active']) {
            User::deactivate($id);
            Session::success('Benutzer wurde deaktiviert.');
        } else {
            User::activate($id);
            Session::success('Benutzer wurde aktiviert.');
        }

        redirect('/admin/benutzer');
    }

    /**
     * Benutzer zum Admin befördern
     */
    public function promote(int $id): void
    {
        $user = User::find($id);

        if (!$user) {
            Session::error('Benutzer nicht gefunden.');
            redirect('/admin/benutzer');
            return;
        }

        User::promoteToAdmin($id);
        Session::success($user['name'] . ' wurde zum Admin befördert.');
        redirect('/admin/benutzer');
    }

    /**
     * Admin-Rolle entziehen
     */
    public function demote(int $id): void
    {
        $user = User::find($id);

        if (!$user) {
            Session::error('Benutzer nicht gefunden.');
            redirect('/admin/benutzer');
            return;
        }

        // Sich selbst nicht degradieren
        if ($id === Auth::id()) {
            Session::error('Sie können sich nicht selbst degradieren.');
            redirect('/admin/benutzer');
            return;
        }

        User::update($id, ['role' => 'user']);
        Session::success($user['name'] . ' ist jetzt ein normaler Benutzer.');
        redirect('/admin/benutzer');
    }
}
