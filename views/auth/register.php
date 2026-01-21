<?php
/**
 * Register Page
 */

use Core\Session;
?>

<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-8">
            <span class="text-5xl">🏀</span>
            <h1 class="text-3xl font-bold gradient-text mt-4">Registrieren</h1>
            <p class="text-gray-500 mt-2">Werden Sie Teil der Fake NBA Store Community</p>
        </div>

        <?php 
        $errors = Session::getFlash('errors', []);
        $old = Session::getFlash('old', []);
        ?>

        <form action="<?= url('/registrieren') ?>" method="post" class="space-y-5">
            <input type="hidden" name="_csrf_token" value="<?= Session::generateCsrfToken() ?>">

            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-user mr-1 text-gray-400"></i> Name
                </label>
                <input type="text" name="name" required minlength="2"
                    value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                    class="w-full px-4 py-3 rounded-lg border <?= isset($errors['name']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none transition-all"
                    placeholder="Ihr Name">
                <?php if (isset($errors['name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['name'] ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-envelope mr-1 text-gray-400"></i> E-Mail
                </label>
                <input type="email" name="email" required
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    class="w-full px-4 py-3 rounded-lg border <?= isset($errors['email']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none transition-all"
                    placeholder="ihre@email.de">
                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['email'] ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-lock mr-1 text-gray-400"></i> Passwort
                </label>
                <input type="password" name="password" required minlength="8"
                    class="w-full px-4 py-3 rounded-lg border <?= isset($errors['password']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none transition-all"
                    placeholder="Mindestens 8 Zeichen">
                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['password'] ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-lock mr-1 text-gray-400"></i> Passwort bestätigen
                </label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 rounded-lg border <?= isset($errors['password_confirmation']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> focus:border-nba-red focus:ring-2 focus:ring-nba-red/20 outline-none transition-all"
                    placeholder="Passwort wiederholen">
                <?php if (isset($errors['password_confirmation'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['password_confirmation'] ?></p>
                <?php endif; ?>
            </div>

            <button type="submit"
                class="w-full bg-nba-red hover:bg-red-700 text-white py-4 rounded-lg font-bold text-lg transition-colors">
                <i class="fas fa-user-plus mr-2"></i> Konto erstellen
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
                <span class="font-medium">Mit Google registrieren</span>
            </a>
        </div>

        <p class="mt-8 text-center text-gray-600">
            Bereits ein Konto?
            <a href="<?= url('/anmelden') ?>" class="text-nba-red font-medium hover:underline">Jetzt anmelden</a>
        </p>
    </div>
</div>