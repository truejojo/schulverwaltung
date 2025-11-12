<?php // $title = 'Klasse anlegen' 
?>
<div class="max-w-xl mx-auto p-6 space-y-6">
  <div class="flex justify-between items-baseline">
    <h1 class="text-xl font-semibold"><?= htmlspecialchars($title) ?></h1>
    <a href="classes.php" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Zurück</a>
  </div>

  <form method="post" action="actions/classes/add.php"
    class="space-y-5 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-5">
    <div>
      <label class="block text-sm mb-1" for="klasse">Klasse</label>
      <input id="klasse" name="klasse" type="text" required
        class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900"
        placeholder="z. B. 5a">
    </div>

    <div class="flex items-center gap-3">
      <button type="submit"
        class="px-4 py-2 rounded border border-green-600 text-green-600 font-semibold hover:bg-green-600 hover:text-white transition">
        Speichern
      </button>
      <a href="classes.php"
        class="px-4 py-2 rounded border border-gray-400 text-gray-600 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        Abbrechen
      </a>
    </div>
  </form>
</div>