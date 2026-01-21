<?php
/**
 * Admin Product Edit View
 * @var array $product
 * @var array $categories
 */
?>

<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= url('/admin/produkte') ?>" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-800 transition-colors bg-white">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Produkt bearbeiten: <?= e($product['name']) ?></h1>
    </div>

    <form action="<?= url('/admin/produkte/' . $product['id']) ?>" method="post" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <?= \Core\View::csrf() ?>
        
        <div class="p-8 space-y-6">
            
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Produktname</label>
                    <input type="text" name="name" value="<?= e($product['name']) ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategorie</label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none bg-white">
                        <option value="">Wählen...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $product['category_id'] === $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">SKU (Artikelnummer)</label>
                    <input type="text" name="sku" value="<?= e($product['sku'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Preis (€)</label>
                    <input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Angebotspreis (€)</label>
                    <input type="number" name="sale_price" step="0.01" value="<?= $product['sale_price'] ?? '' ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Lagerbestand</label>
                    <input type="number" name="stock" value="<?= $product['stock'] ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Kurzbeschreibung</label>
                <textarea name="short_description" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"><?= e($product['short_description'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Beschreibung</label>
                <textarea name="description" rows="6" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-nba-blue focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"><?= e($product['description'] ?? '') ?></textarea>
            </div>

            <!-- Image -->
            <div class="flex gap-6 items-start">
                <?php if (!empty($product['image'])): ?>
                    <div class="w-24 h-24 rounded-lg bg-gray-100 border border-gray-200 shrink-0 overflow-hidden">
                        <img src="<?= e(asset($product['image'])) ?>" alt="" class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>
                
                <div class="flex-grow">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Neues Bild hochladen</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                    <p class="text-xs text-gray-400 mt-2">Ersetzt das aktuelle Bild. Leer lassen um Bild zu behalten.</p>
                </div>
            </div>

            <!-- Settings -->
            <div class="flex gap-8 pt-4 border-t border-gray-100">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="is_active" <?= $product['is_active'] ? 'checked' : '' ?> class="w-5 h-5 rounded text-nba-blue focus:ring-blue-500 border-gray-300">
                    <span class="text-gray-700 font-medium group-hover:text-nba-blue transition-colors">Aktiv (Sichtbar)</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?> class="w-5 h-5 rounded text-nba-blue focus:ring-blue-500 border-gray-300">
                    <span class="text-gray-700 font-medium group-hover:text-nba-blue transition-colors">Hervorgehoben</span>
                </label>
            </div>

        </div>

        <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-end gap-3">
            <a href="<?= url('/admin/produkte') ?>" class="px-6 py-2.5 rounded-xl font-bold text-gray-600 hover:bg-gray-200 transition-colors">Abbrechen</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl font-bold bg-nba-blue text-white hover:bg-blue-900 shadow-lg shadow-blue-900/20 transition-all">
                Änderungen speichern
            </button>
        </div>
    </form>
</div>
