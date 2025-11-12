<?php /** @var array $user */ ?>
<div class="max-w-2xl mx-auto p-6 space-y-6">
  <div class="flex justify-between items-baseline">
    <h1 class="text-xl font-semibold"><?= htmlspecialchars($title ?? 'Benutzer bearbeiten') ?></h1>
    <a href="users.php" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Zurück</a>
  </div>

  <form method="post" action="actions/users/edit.php"
    class="space-y-5 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-5">
    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm mb-1" for="vorname">Vorname</label>
        <input id="vorname" name="vorname" type="text" required value="<?= htmlspecialchars($user['vorname']) ?>"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="nachname">Nachname</label>
        <input id="nachname" name="nachname" type="text" required value="<?= htmlspecialchars($user['nachname']) ?>"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="email">E-Mail</label>
        <input id="email" name="email" type="email" required value="<?= htmlspecialchars($user['email']) ?>"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="geburtstag">Geburtstag</label>
        <input id="geburtstag" name="geburtstag" type="date" value="<?= htmlspecialchars($user['geburtstag']) ?>"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm mb-1" for="adresse">Adresse</label>
        <input id="adresse" name="adresse" type="text" value="<?= htmlspecialchars($user['adresse']) ?>"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="plz">PLZ</label>
        <input id="plz" name="plz" type="text" value="<?= htmlspecialchars($user['plz']) ?>"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="telefon">Telefon</label>
        <input id="telefon" name="telefon" type="text" value="<?= htmlspecialchars($user['telefon']) ?>"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <!-- <div class="md:col-span-2 text-sm text-gray-600 dark:text-gray-400">
        Rolle: <?= (int)($user['role_id'] ?? 0) ?> (Änderung über die spezifischen Seiten)
      </div> -->
    </div>

    <div class="flex items-center gap-3">
      <button type="submit"
        class="px-4 py-2 rounded border border-cyan-600 text-cyan-600 font-semibold hover:bg-cyan-800 hover:text-white transition">Speichern</button>
      <a href="users.php"
        class="px-4 py-2 rounded border border-gray-400 text-gray-600 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Abbrechen</a>
    </div>
  </form>
</div>