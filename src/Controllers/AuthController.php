<?php

namespace Controllers;

use Core\View;
use Core\Session;
use Core\Auth;
use Core\Validator;
use Models\User;
use Models\Cart;

/**
 * AuthController - Authentifizierung
 */
class AuthController
{
    /**
     * Login-Formular anzeigen
     */
    public function showLogin(): void
    {
        Auth::requireGuest();

        View::render('auth/login', [
            'seo' => [
                'title' => 'Anmelden',
                'description' => 'Melden Sie sich in Ihrem NBA Shop Konto an'
            ]
        ]);
    }

    /**
     * Login verarbeiten
     */
    public function login(): void
    {
        Auth::requireGuest();

        // CSRF prüfen
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage. Bitte versuchen Sie es erneut.');
            redirect('/anmelden');
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (empty($email) || empty($password)) {
            Session::error('Bitte füllen Sie alle Felder aus.');
            Session::flash('old', ['email' => $email]);
            redirect('/anmelden');
            return;
        }

        if (Auth::attempt($email, $password, $remember)) {
            Session::regenerateCsrfToken();

            // Warenkorb aus DB laden
            Cart::loadFromDatabase(Auth::id());

            Session::success('Willkommen zurück, ' . Auth::user()['name'] . '!');

            // Zur ursprünglich gewünschten Seite weiterleiten
            $intended = Session::get('intended_url', '/');
            Session::remove('intended_url');
            redirect($intended);
        } else {
            Session::error('Ungültige E-Mail oder Passwort.');
            Session::flash('old', ['email' => $email]);
            redirect('/anmelden');
        }
    }

    /**
     * Registrierungsformular anzeigen
     */
    public function showRegister(): void
    {
        Auth::requireGuest();

        View::render('auth/register', [
            'seo' => [
                'title' => 'Registrieren',
                'description' => 'Erstellen Sie ein Konto im NBA Shop'
            ]
        ]);
    }

    /**
     * Registrierung verarbeiten
     */
    public function register(): void
    {
        file_put_contents(__DIR__ . '/../../public/auth_debug.log', date('Y-m-d H:i:s') . " Register Start\n", FILE_APPEND);
        Auth::requireGuest();

        // CSRF prüfen
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            file_put_contents(__DIR__ . '/../../public/auth_debug.log', "CSRF Failed\n", FILE_APPEND);
            Session::error('Ungültige Anfrage. Bitte versuchen Sie es erneut.');
            redirect('/registrieren');
            return;
        }

        $validator = Validator::make($_POST, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|password',
            'password_confirmation' => 'required|confirmed:password'
        ], [
            'password_confirmation.confirmed' => 'Die Passwörter stimmen nicht überein.'
        ]);

        if (!$validator->validate()) {
            file_put_contents(__DIR__ . '/../../public/auth_debug.log', "Validation Failed: " . print_r($validator->firstErrors(), true) . "\n", FILE_APPEND);
            Session::flash('errors', $validator->firstErrors());
            Session::flash('old', ['name' => $_POST['name'], 'email' => $_POST['email']]);
            redirect('/registrieren');
            return;
        }

        try {
            $userId = User::register([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password']
            ]);
            file_put_contents(__DIR__ . '/../../public/auth_debug.log', "User Created: $userId\n", FILE_APPEND);

            $user = User::find($userId);
            Auth::login($user);
            file_put_contents(__DIR__ . '/../../public/auth_debug.log', "User Logged In\n", FILE_APPEND);

            Session::success('Willkommen im NBA Shop, ' . $user['name'] . '!');
            redirect('/');

        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/../../public/auth_debug.log', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            Session::error('Fehler bei der Registrierung. Bitte versuchen Sie es erneut.');
            Session::flash('old', ['name' => $_POST['name'], 'email' => $_POST['email']]);
            redirect('/registrieren');
        }
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        Auth::logout();
        Session::success('Sie wurden erfolgreich abgemeldet.');
        redirect('/');
    }

    /**
     * Profil anzeigen
     */
    public function profile(): void
    {
        Auth::requireAuth();

        View::render('auth/profile', [
            'seo' => [
                'title' => 'Mein Profil',
                'description' => 'Verwalten Sie Ihr NBA Shop Profil'
            ],
            'user' => Auth::user()
        ]);
    }

    /**
     * Profil aktualisieren
     */
    public function updateProfile(): void
    {
        Auth::requireAuth();

        // CSRF prüfen
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            redirect('/profil');
            return;
        }

        $userId = Auth::id();
        $currentUser = Auth::user();

        $rules = [
            'name' => 'required|min:2|max:100'
        ];

        // E-Mail nur prüfen wenn geändert
        if ($_POST['email'] !== $currentUser['email']) {
            $rules['email'] = 'required|email|unique:users,email,' . $userId;
        }

        $validator = Validator::make($_POST, $rules);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->firstErrors());
            redirect('/profil');
            return;
        }

        User::update($userId, [
            'name' => $_POST['name'],
            'email' => $_POST['email']
        ]);

        Session::success('Profil wurde aktualisiert.');
        redirect('/profil');
    }

    /**
     * Passwort ändern
     */
    public function changePassword(): void
    {
        Auth::requireAuth();

        // CSRF prüfen
        if (!Session::validateCsrfToken($_POST['_csrf_token'] ?? null)) {
            Session::error('Ungültige Anfrage.');
            redirect('/profil');
            return;
        }

        $user = Auth::user();

        // Aktuelles Passwort prüfen
        if (!Auth::verifyPassword($_POST['current_password'], $user['password_hash'])) {
            Session::error('Das aktuelle Passwort ist falsch.');
            redirect('/profil');
            return;
        }

        $validator = Validator::make($_POST, [
            'new_password' => 'required|min:8|password',
            'new_password_confirmation' => 'required'
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->firstErrors());
            redirect('/profil');
            return;
        }

        if ($_POST['new_password'] !== $_POST['new_password_confirmation']) {
            Session::error('Die Passwörter stimmen nicht überein.');
            redirect('/profil');
            return;
        }

        User::updatePassword(Auth::id(), $_POST['new_password']);
        Session::success('Passwort wurde geändert.');
        redirect('/profil');
    }

    // ================================
    // Google OAuth (Platzhalter)
    // ================================

    // ================================
    // Google OAuth
    // ================================

    /**
     * Google OAuth starten
     */
    public function googleRedirect(): void
    {
        try {
            $googleService = new \Services\GoogleAuthService();
            $authUrl = $googleService->getAuthUrl();
            file_put_contents(__DIR__ . '/../../public/auth_debug.log', date('Y-m-d H:i:s') . " [AuthController] Redirecting to Google: " . $authUrl . "\n", FILE_APPEND);
            redirect($authUrl);
        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/../../public/auth_debug.log', date('Y-m-d H:i:s') . " [AuthController] Error in googleRedirect: " . $e->getMessage() . "\n", FILE_APPEND);
            Session::error('Fehler beim Starten der Google-Anmeldung: ' . $e->getMessage());
            redirect('/anmelden');
        }
    }

    /**
     * Google OAuth Callback
     */
    public function googleCallback(): void
    {
        $code = $_GET['code'] ?? null;
        file_put_contents(__DIR__ . '/../../public/auth_debug.log', date('Y-m-d H:i:s') . " [AuthController] Callback received. Code present: " . ($code ? 'YES' : 'NO') . "\n", FILE_APPEND);

        if (!$code) {
            if (isset($_GET['error'])) {
                file_put_contents(__DIR__ . '/../../public/auth_debug.log', date('Y-m-d H:i:s') . " [AuthController] Google Error: " . $_GET['error'] . "\n", FILE_APPEND);
                Session::error('Google-Anmeldung abgebrochen.');
            } else {
                Session::error('Ungültige Anfrage.');
            }
            redirect('/anmelden');
            return;
        }

        try {
            $googleService = new \Services\GoogleAuthService();

            // 1. Access Token holen
            $accessToken = $googleService->getAccessToken($code);

            // 2. Benutzerdaten holen
            $googleUser = $googleService->getUserInfo($accessToken);

            // 3. Benutzerdaten mappen
            $userData = [
                'id' => $googleUser['sub'],
                'email' => $googleUser['email'],
                'name' => $googleUser['name'],
                'picture' => $googleUser['picture'] ?? null
            ];

            // 4. Benutzer erstellen oder aktualisieren
            $user = User::createOrUpdateFromGoogle($userData);

            // 5. Einloggen
            Auth::login($user);

            // Warenkorb laden
            Cart::loadFromDatabase($user['id']);

            Session::success('Erfolgreich mit Google angemeldet!');
            redirect('/');

        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/../../public/auth_debug.log', date('Y-m-d H:i:s') . " [AuthController] Exception in Callback: " . $e->getMessage() . "\n", FILE_APPEND);
            Session::error('Fehler bei der Google-Anmeldung: ' . $e->getMessage());
            redirect('/anmelden');
        }
    }
}
