<?php /** @var array $rollen */ ?>
<div class="max-w-xl mx-auto p-6 space-y-6">
  <div class="flex justify-between items-baseline">
    <h1 class="text-xl font-semibold"><?= htmlspecialchars($title) ?></h1>
    <a href="offices.php" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Zurück</a>
  </div>

  <form method="post" action="actions/offices/add.php"
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
    </div>

    <!-- Verwaltungsrolle: Custom Single Select -->
    <div id="verwaltungsrolle-select" class="relative">
      <label class="block text-sm mb-1">Position</label>
      <button type="button" data-trigger
        class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 flex items-center justify-between">
        <span data-trigger-label>Position wählen</span>
        <span data-arrow class="ml-2 inline-block transition-transform duration-200 text-gray-500">▾</span>
      </button>

      <div data-panel
        class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow hidden">
        <ul class="max-h-60 overflow-auto divide-y divide-gray-100 dark:divide-gray-700">
          <?php foreach ($rollen as $r): ?>
          <?php $rid = (int)$r['id']; $label = htmlspecialchars($r['label'] ?? ($r['name'] ?? ('Rolle #' . $rid))); ?>
          <li>
            <button type="button" data-option value="<?= $rid ?>"
              class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-between">
              <span><?= $label ?></span>
              <span data-check class="text-green-600 font-semibold" style="display:none;">✓</span>
            </button>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div data-chip class="mt-2 text-sm"></div>

      <div data-hidden>
        <input type="hidden" name="verwaltungs_rolle_id" value="">
      </div>

      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Klicke zum Auswählen.</p>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit"
        class="px-4 py-2 rounded border border-green-600 text-green-600 font-semibold hover:bg-green-600 hover:text-white transition">
        Speichern
      </button>
      <a href="offices.php"
        class="px-4 py-2 rounded border border-gray-400 text-gray-600 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        Abbrechen
      </a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('verwaltungsrolle-select');
  if (!root) return;
  const trigger = root.querySelector('[data-trigger]');
  const labelEl = root.querySelector('[data-trigger-label]');
  const panel = root.querySelector('[data-panel]');
  const arrow = root.querySelector('[data-arrow]');
  const hidden = root.querySelector('input[name="verwaltungs_rolle_id"]');
  const chipWrap = root.querySelector('[data-chip]');
  const options = Array.from(root.querySelectorAll('[data-option]'));

  function setOpen(open) {
    panel.classList.toggle('hidden', !open);
    arrow.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
  }

  function select(id, label) {
    hidden.value = id;
    chipWrap.innerHTML = '';
    const chip = document.createElement('span');
    chip.className =
      'px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 inline-block';
    chip.textContent = label;
    chipWrap.appendChild(chip);
    labelEl.textContent = label;
    options.forEach(btn => {
      const is = btn.getAttribute('value') === String(id);
      btn.querySelector('[data-check]').style.display = is ? '' : 'none';
    });
    setOpen(false);
  }

  trigger.addEventListener('click', () => setOpen(panel.classList.contains('hidden')));
  document.addEventListener('click', e => {
    if (!root.contains(e.target)) setOpen(false);
  });
  options.forEach(btn => btn.addEventListener('click', () => {
    select(btn.getAttribute('value'), btn.querySelector('span').textContent.trim());
  }));
});
</script>