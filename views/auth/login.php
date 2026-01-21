<?php
/**
 * Login Page
 */

use Core\Session;
?>

<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <span class="text-5xl">🏀</span>
            <h1 class="text-3xl font-bold gradient-text mt-4">Anmelden</h1>
            <p class="text-gray-500 mt-2">Willkommen zurück beim Fake NBA Store</p>
        </div>

        <form action="<?= url('/anmelden') ?>" method="post" class="space-y-6">
            <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-envelope mr-1 text-gray-400"></i> E-Mail
                </label>
                <input type="email" name="email" required
                    value="<?= htmlspecialchars(Session::getFlash('old')['email'] ?? '') ?>"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none transition-all"
                    placeholder="ihre@email.de">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-lock mr-1 text-gray-400"></i> Passwort
                </label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none transition-all"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full bg-nba-red hover:bg-red-700 text-white py-4 rounded-lg font-bold text-lg transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i> Anmelden
            </button>
        </form>

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">oder</span>
                </div>
            </div>

            <a href="<?= url('/auth/google') ?>"
                class="mt-4 w-full flex items-center justify-center gap-3 border border-gray-300 py-3 rounded-lg hover:bg-gray-50 transition-colors">
                <img src="https://www.google.com/favicon.ico" class="w-5 h-5" alt="Google">
                <span class="font-medium">Mit Google anmelden</span>
            </a>
        </div>

        <p class="mt-8 text-center text-gray-600">
            Noch kein Konto?
            <a href="<?= url('/registrieren') ?>" class="text-nba-red font-medium hover:underline">Jetzt registrieren</a>
        </p>
    </div>
</div>