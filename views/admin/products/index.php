<?php
/**
 * Admin Products Index
 * @var array $products
 * @var array $pagination
 */
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
        <i class="fas fa-tshirt text-nba-red"></i> Produkte verwalten
    </h1>
    <a href="<?= url('/admin/produkte/neu') ?>" class="bg-nba-blue hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl font-medium transition-colors flex items-center gap-2 shadow-lg shadow-blue-900/20">
        <i class="fas fa-plus"></i> Neues Produkt
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Search/Filter could go here -->
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-sm uppercase tracking-wider">
                    <th class="px-6 py-4 font-semibold">Produkt</th>
                    <th class="px-6 py-4 font-semibold">SKU</th>
                    <th class="px-6 py-4 font-semibold">Preis</th>
                    <th class="px-6 py-4 font-semibold">Lager</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <i class="fas fa-box-open text-4xl text-gray-300"></i>
                                <span class="font-medium">Keine Produkte gefunden.</span>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 shrink-0">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?= e(asset($product['image'])) ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 line-clamp-1"><?= e($product['name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= e($product['category_name'] ?? 'Ohne Kategorie') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                <?= e($product['sku'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <?= formatPrice($product['price']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($product['stock'] <= 5): ?>
                                    <span class="text-red-600 font-bold text-sm bg-red-50 px-2 py-0.5 rounded flex items-center gap-1 w-fit">
                                        <i class="fas fa-exclamation-circle text-xs"></i> <?= $product['stock'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-600 text-sm"><?= $product['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($product['is_active']): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktiv
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inaktiv
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="<?= url('/admin/produkte/' . $product['id'] . '/loeschen') ?>" method="post" class="inline-block">
                                    <?= \Core\View::csrf() ?>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?= url('/admin/produkte/' . $product['id'] . '/bearbeiten') ?>" 
                                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                           title="Bearbeiten">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <button type="submit" 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                title="Löschen">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($pagination['totalPages'] > 1): ?>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-center">
            <div class="flex items-center gap-1">
                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors
                              <?= $i === $pagination['currentPage'] 
                                  ? 'bg-nba-blue text-white shadow-sm' 
                                  : 'text-gray-600 hover:bg-white hover:shadow-sm' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
