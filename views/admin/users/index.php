<?php
/**
 * Admin User Index
 * @var array $users
 */
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-users text-nba-red"></i> Benutzer verwalten
    </h1>
    <!-- Stats Bubble -->
    <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 text-sm font-medium text-gray-600">
        Gesamt: <span class="text-gray-900 font-bold"><?= count($users) ?></span>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-sm uppercase tracking-wider">
                    <th class="px-6 py-4 font-semibold">Name / Email</th>
                    <th class="px-6 py-4 font-semibold">Rolle</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold">Registriert am</th>
                    <th class="px-6 py-4 font-semibold text-right">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-nba-blue text-white flex items-center justify-center font-bold text-sm shrink-0">
                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800"><?= e($user['name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= e($user['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($user['role'] === 'admin'): ?>
                                <span class="bg-purple-100 text-purple-700 text-xs px-2 py-1 rounded-md font-bold">
                                    <i class="fas fa-shield-alt mr-1"></i> Admin
                                </span>
                            <?php else: ?>
                                <span class="text-gray-600 text-sm">Benutzer</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($user['is_active']): ?>
                                <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-semibold">Aktiv</span>
                            <?php else: ?>
                                <span class="text-red-600 bg-red-50 px-2 py-1 rounded text-xs font-semibold">Gesperrt</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= formatDateTime($user['created_at']) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="<?= url('/admin/benutzer/' . $user['id']) ?>" 
                                   class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 text-sm font-medium transition-colors">
                                    Details
                                </a>
                                
                                <form action="<?= url('/admin/benutzer/' . $user['id'] . '/status') ?>" method="post">
                                    <?= \Core\View::csrf() ?>
                                    <button type="submit" 
                                            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-800 transition-colors"
                                            title="<?= $user['is_active'] ? 'Sperren' : 'Entsperren' ?>">
                                        <i class="fas <?= $user['is_active'] ? 'fa-ban' : 'fa-check' ?>"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
