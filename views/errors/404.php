<?php
/**
 * 404 Error Page
 */
?>

<div class="max-w-2xl mx-auto px-4 py-20 text-center">
    <div class="text-8xl mb-6">🏀</div>
    <h1 class="text-4xl font-bold text-gray-800 mb-4">Seite nicht gefunden</h1>
    <p class="text-xl text-gray-500 mb-8">
        Ups! Diese Seite scheint nicht zu existieren. Vielleicht wurde sie verschoben oder gelöscht.
    </p>
    <div class="flex flex-wrap justify-center gap-4">
        <a href="<?= url('/') ?>"
            class="inline-flex items-center gap-2 bg-nba-red text-white px-8 py-3 rounded-full font-bold hover:bg-red-700 transition-colors">
            <i class="fas fa-home"></i> Zur Startseite
        </a>
        <a href="<?= url('/produkte') ?>"
            class="inline-flex items-center gap-2 bg-nba-blue text-white px-8 py-3 rounded-full font-bold hover:bg-blue-900 transition-colors">
            <i class="fas fa-basketball"></i> Zu den Produkten
        </a>
    </div>
</div>