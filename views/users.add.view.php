<?php // $title = 'Benutzer anlegen'; /** @var array $klassen @var array $verwaltungsRollen */ ?>
<div class="max-w-2xl mx-auto p-6 space-y-6">
  <div class="flex justify-between items-baseline">
    <h1 class="text-xl font-semibold"><?= htmlspecialchars($title ?? 'Benutzer anlegen') ?></h1>
    <a href="users.php" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Zurück</a>
  </div>

  <form method="post" action="actions/users/add.php"
    class="space-y-5 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-5">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm mb-1" for="vorname">Vorname</label>
        <input id="vorname" name="vorname" type="text" required
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="nachname">Nachname</label>
        <input id="nachname" name="nachname" type="text" required
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="email">E-Mail</label>
        <input id="email" name="email" type="email" required
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="password">Passwort</label>
        <input id="password" name="password" type="password" required
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm mb-1" for="adresse">Adresse</label>
        <input id="adresse" name="adresse" type="text"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="plz">PLZ</label>
        <input id="plz" name="plz" type="text"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="telefon">Telefon</label>
        <input id="telefon" name="telefon" type="text"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="geburtstag">Geburtstag</label>
        <input id="geburtstag" name="geburtstag" type="date"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>

      <div>
        <label class="block text-sm mb-1" for="role_id">Rolle</label>
        <select id="role_id" name="role_id"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
          <option value="">(keine)</option>
          <option value="1">Schüler</option>
          <option value="2">Lehrer</option>
          <option value="3">Verwaltung</option>
        </select>
      </div>

      <!-- Schüler: Klasse -->
      <div data-role-extra="1" style="display:none;">
        <label class="block text-sm mb-1" for="klasse_id">Klasse</label>
        <select id="klasse_id" name="klasse_id"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
          <option value="">(keine)</option>
          <?php foreach (($klassen ?? []) as $k): ?>
          <option value="<?= (int)$k['id'] ?>"><?= htmlspecialchars($k['klasse'] ?? ('Klasse ' . (int)$k['id'])) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Verwaltung: Position -->
      <div data-role-extra="3" style="display:none;">
        <label class="block text-sm mb-1" for="verwaltungs_rolle_id">Position</label>
        <select id="verwaltungs_rolle_id" name="verwaltungs_rolle_id"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
          <option value="">(keine)</option>
          <?php foreach (($verwaltungsRollen ?? []) as $r): ?>
          <option value="<?= (int)$r['id'] ?>">
            <?= htmlspecialchars((string)($r['label'] ?? ('Rolle ' . (int)$r['id']))) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit"
        class="px-4 py-2 rounded border border-cyan-600 text-cyan-600 font-semibold hover:bg-cyan-800 hover:text-white transition">Speichern</button>
      <a href="users.php"
        class="px-4 py-2 rounded border border-gray-400 text-gray-600 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Abbrechen</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const role = document.getElementById('role_id');
  const extras = document.querySelectorAll('[data-role-extra]');
  const update = () => {
    const v = role.value;
    extras.forEach(el => el.style.display = el.getAttribute('data-role-extra') === v ? '' : 'none');
  };
  role.addEventListener('change', update);
  update();
});
</script>