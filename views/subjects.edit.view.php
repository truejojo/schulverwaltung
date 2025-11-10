<?php
// Erwartet: $subject (array mit id, fach), $title bereits gesetzt
?>
<div class="max-w-xl mx-auto p-6 space-y-6">
  <div class="flex justify-between items-baseline">
    <h1 class="text-xl font-semibold"><?= htmlspecialchars($title) ?></h1>
    <a href="/grundlagen/schulverwaltung/subjects.php"
      class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Zurück</a>
  </div>

  <form method="post" action="/grundlagen/schulverwaltung/actions/subjects/edit.php"
    class="space-y-5 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-5">
    <input type="hidden" name="id" value="<?= (int)$subject['id'] ?>">
    <div>
      <label for="fach" class="block text-sm mb-1">Fach<span class="text-red-500">*</span></label>
      <input id="fach" name="fach" type="text" required value="<?= htmlspecialchars($subject['fach'] ?? '') ?>"
        class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500">
    </div>
    <div class="flex items-center gap-3">
      <button type="submit"
        class="px-4 py-2 rounded border border-green-600 text-green-600 font-semibold hover:bg-green-600 hover:text-white transition">
        Speichern
      </button>
      <a href="/grundlagen/schulverwaltung/subjects.php"
        class="px-4 py-2 rounded border border-gray-400 text-gray-600 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        Abbrechen
      </a>
    </div>
  </form>
</div>