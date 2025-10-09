<?php /** @var array $subjects */ ?>
<div class="max-w-4xl mx-auto py-8 space-y-8">
  <div class="flex justify-between items-baseline">
    <h1 class="text-3xl text-gray-700 dark:text-gray-200">Klassen</h1>
    <a href="index.php"
      class="text-lg inline-block px-3 bg-transparent text-gray-500 dark:text-gray-300 hover:underline">Zurück
      zur Übersicht</a>
  </div>

  <?php if (empty($classes)): ?>
  <div
    class="p-4 border border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
    Keine Klassen gefunden.
  </div>
  <?php else: ?>
  <ul class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
    <?php foreach ($classes as $c): ?>
    <li
      class="p-4 rounded bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200">
      <?= htmlspecialchars(is_array($c) ? ($c['klasse'] ?? '') : (string)$c) ?>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>