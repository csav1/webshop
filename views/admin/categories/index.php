<?php
/**
 * Admin Category Index
 * @var array $categories
 */
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-tags text-nba-blue"></i> Kategorien verwalten
    </h1>
    <a href="<?= url('/admin/kategorien/neu') ?>" class="bg-nba-blue hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl font-medium transition-colors flex items-center gap-2 shadow-lg shadow-blue-900/20">
        <i class="fas fa-plus"></i> Neue Kategorie
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($categories as $category): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-nba-blue flex items-center justify-center text-xl">
                    <i class="fas fa-tag"></i>
                </div>
                <!-- Status Toggle Form -->
                <form action="<?= url('/admin/kategorien/' . $category['id'] . '/status') ?>" method="post">
                    <?= \Core\View::csrf() ?>
                    <button type="submit" 
                            class="text-xs font-semibold px-2.5 py-1 rounded-full transition-colors <?= $category['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>"
                            title="Status umschalten">
                        <?= $category['is_active'] ? 'Aktiv' : 'Inaktiv' ?>
                    </button>
                </form>
            </div>
            
            <h3 class="text-lg font-bold text-gray-800 mb-1"><?= e($category['name']) ?></h3>
            <p class="text-sm text-gray-500 font-mono mb-4 text-xs bg-gray-50 inline-block px-1.5 rounded">/<?= e($category['slug']) ?></p>
            
            <p class="text-gray-600 text-sm mb-6 line-clamp-2 min-h-[2.5em]">
                <?= e($category['description'] ?? 'Keine Beschreibung.') ?>
            </p>
            
            <div class="flex items-center gap-2 pt-4 border-t border-gray-50">
                <a href="<?= url('/admin/kategorien/' . $category['id'] . '/bearbeiten') ?>" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                    Bearbeiten
                </a>
                
                <form action="<?= url('/admin/kategorien/' . $category['id'] . '/loeschen') ?>" method="post" class="contents" onsubmit="return confirm('Kategorie wirklich löschen?');">
                    <?= \Core\View::csrf() ?>
                    <button type="submit" 
                            class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-600 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
