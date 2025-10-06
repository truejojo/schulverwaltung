<?php /** @var bool $isAuthenticated */ ?>

<main class="max-w-5xl mx-auto px-4 py-10 text-center grid gap-10 lg:gap-16">
  <h1 class="text-gray-600 dark:text-gray-300 text-3xl">Willkommen in der Schulverwaltung</h1>

  <?php if (!$isAuthenticated): ?>
  <div class="rounded-lg bg-gray-100 dark:bg-gray-800 shadow">
    <a href="login.php" class="
        block w-full h-full p-5 rounded-md border-2 border-orange-400/60
        bg-white dark:bg-gray-900
        text-orange-700 dark:text-orange-300
        hover:bg-orange-50 dark:hover:bg-gray-800 hover:underline
        active:bg-orange-100 dark:active:bg-gray-700
        focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 dark:focus:ring-offset-gray-900
        font-large uppercase tracking-wider transition-colors">
      Zum Login-Bereich
    </a>
  </div>
  <?php endif; ?>

  <!-- Nur für user die eingeloggt sind!!! -->
  <?php if ($isAuthenticated): ?>
  <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <div class="p-5 rounded-lg bg-gray-100 dark:bg-gray-800 shadow">
      <h3 class="font-semibold mb-1">Schüler</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400">Verwaltung von Schülerdaten.</p>
    </div>
    <div class="p-5 rounded-lg bg-gray-100 dark:bg-gray-800 shadow">
      <h3 class="font-semibold mb-1">Lehrer</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400">Lehrerdaten organisieren.</p>
    </div>
    <div class="p-5 rounded-lg bg-gray-100 dark:bg-gray-800 shadow">
      <h3 class="font-semibold mb-1">Klassen</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400">Klassenstrukturen verwalten.</p>
    </div>
  </div>
  <?php endif; ?>
</main>