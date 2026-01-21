<?php
/**
 * Product List Page
 * @var array $products
 * @var array $categories
 * @var string|null $currentCategory
 * @var string|null $searchQuery
 */
$searchQuery = $searchQuery ?? null;
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">
            <li><a href="<?= url('/') ?>" class="hover:text-nba-red">Startseite</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800 font-medium">
                <?php if ($searchQuery): ?>
                    Suche: "
                    <?= e($searchQuery) ?>"
                <?php elseif ($currentCategory): ?>
                    <?php
                    $cat = array_filter($categories, fn($c) => $c['slug'] === $currentCategory);
                    echo e(reset($cat)['name'] ?? 'Kategorie');
                    ?>
                <?php else: ?>
                    Alle Produkte
                <?php endif; ?>
            </li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">

        <!-- Sidebar Filters -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl p-6 shadow-sm sticky top-24">
                <h3 class="font-bold text-lg mb-4">
                    <i class="fas fa-filter text-nba-blue mr-2"></i> Filter
                </h3>

                <!-- Categories -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Kategorien</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="<?= url('/produkte') ?>"
                                class="block py-2 px-3 rounded-lg transition-colors <?= !$currentCategory ? 'bg-nba-red/10 text-nba-red font-medium' : 'hover:bg-gray-100' ?>">
                                Alle Produkte
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= url('/produkte?kategorie=' . e($cat['slug'])) ?>"
                                    class="block py-2 px-3 rounded-lg transition-colors <?= $currentCategory === $cat['slug'] ? 'bg-nba-red/10 text-nba-red font-medium' : 'hover:bg-gray-100' ?>">
                                    <?= e($cat['name']) ?>
                                    <span class="text-gray-400 text-sm ml-1">(
                                        <?= $cat['product_count'] ?? 0 ?>)
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Search in sidebar -->
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Suche</h4>
                    <form action="<?= url('/produkte') ?>" method="get">
                        <input type="text" name="suche" value="<?= e($searchQuery ?? '') ?>"
                            placeholder="Produkt suchen..."
                            class="w-full bg-gray-100 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-nba-red/50">
                    </form>
                </div>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="flex-1">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    <?php if ($searchQuery): ?>
                        Suchergebnisse für "
                        <?= e($searchQuery) ?>"
                    <?php elseif ($currentCategory): ?>
                        <?php
                        $cat = array_filter($categories, fn($c) => $c['slug'] === $currentCategory);
                        echo e(reset($cat)['name'] ?? 'Kategorie');
                        ?>
                    <?php else: ?>
                        Alle Produkte
                    <?php endif; ?>
                    <span class="text-gray-400 font-normal text-lg ml-2">(
                        <?= count($products) ?>)
                    </span>
                </h1>
            </div>

            <?php if (empty($products)): ?>
                <div class="bg-white rounded-xl p-12 text-center shadow-sm">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Keine Produkte gefunden</h2>
                    <p class="text-gray-500 mb-4">Versuchen Sie eine andere Suche oder Kategorie.</p>
                    <a href="<?= url('/produkte') ?>"
                        class="inline-flex items-center gap-2 bg-nba-blue text-white px-6 py-2 rounded-full hover:bg-blue-900 transition-colors">
                        <i class="fas fa-undo"></i> Alle Produkte anzeigen
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                        <?php Core\View::partial('product-card', ['product' => $product]); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

    </div>
</div>