<?php
/**
 * Admin User Edit View
 * @var array $user
 */
?>

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= url('/admin/benutzer') ?>" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-800 transition-colors bg-white">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Benutzer bearbeiten</h1>
    </div>

    <form action="<?= url('/admin/benutzer/' . $user['id']) ?>" method="post" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
        <?= \Core\View::csrf() ?>
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Name</label>
            <input type="text" name="name" value="<?= e($user['name']) ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">E-Mail Adresse</label>
            <input type="email" name="email" value="<?= e($user['email']) ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Rolle</label>
            <select name="role" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none bg-white">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Benutzer</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
            </select>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" name="is_active" <?= $user['is_active'] ? 'checked' : '' ?> class="w-5 h-5 rounded text-nba-blue focus:ring-blue-500 border-gray-300">
                <span class="text-gray-700 font-medium group-hover:text-nba-blue transition-colors">Aktiv (Zugriff erlaubt)</span>
            </label>
        </div>

        <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
            <a href="<?= url('/admin/benutzer') ?>" class="px-6 py-2.5 rounded-xl font-bold text-gray-600 hover:bg-gray-200 transition-colors">Abbrechen</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl font-bold bg-nba-blue text-white hover:bg-blue-900 shadow-lg shadow-blue-900/20 transition-all">
                Speichern
            </button>
        </div>
    </form>
</div>
