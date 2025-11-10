<?php /** @var array $class */ /** @var array $teachersForAssign */ /** @var array $selectedTeacherIds */ ?>
<div class="max-w-xl mx-auto p-6 space-y-6">
  <div class="flex justify-between items-baseline">
    <h1 class="text-xl font-semibold"><?= htmlspecialchars($title) ?></h1>
    <a href="/grundlagen/schulverwaltung/classes.php"
      class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Zurück</a>
  </div>

  <form method="post" action="/grundlagen/schulverwaltung/actions/classes/edit.php"
    class="space-y-5 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-5">
    <input type="hidden" name="id" value="<?= (int)$class['id'] ?>">

    <div>
      <label class="block text-sm mb-1" for="klasse">Klasse</label>
      <input id="klasse" name="klasse" type="text" required
        value="<?= htmlspecialchars($class['klasse'] ?? '') ?>"
        class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
    </div>

    <!-- Lehrer: Dropdown-Mehrfachauswahl mit Pfeil -->
    <div id="class-teachers-multiselect" class="relative">
      <label class="block text-sm mb-1">Lehrer (Mehrfachauswahl)</label>

      <button type="button" data-trigger
        class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 flex items-center justify-between">
        <span data-trigger-label>Lehrer wählen</span>
        <span data-arrow class="ml-2 inline-block transition-transform duration-200 text-gray-500">▾</span>
      </button>

      <div data-panel
           class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow hidden">
        <ul class="max-h-60 overflow-auto divide-y divide-gray-100 dark:divide-gray-700">
          <?php foreach ($teachersForAssign as $t): ?>
            <?php
              $lid = (int)$t['id']; // lehrer.id
              $label = htmlspecialchars($t['name'] ?? ('Lehrer #' . $lid));
              $isSel = in_array($lid, $selectedTeacherIds, true);
            ?>
            <li>
              <button type="button" data-option value="<?= $lid ?>"
                class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-between">
                <span><?= $label ?></span>
                <span data-check class="text-green-600 font-semibold" style="<?= $isSel ? '' : 'display:none;' ?>">✓</span>
              </button>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div data-chips class="mt-2 flex flex-wrap gap-2 text-sm">
        <?php foreach ($teachersForAssign as $t): ?>
          <?php $lid = (int)$t['id']; $label = htmlspecialchars($t['name'] ?? ('Lehrer #' . $lid)); ?>
          <?php if (in_array($lid, $selectedTeacherIds, true)): ?>
            <span data-chip data-id="<?= $lid ?>"
                  class="px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
              <?= $label ?>
            </span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div data-hidden>
        <?php foreach ($selectedTeacherIds as $lid): ?>
          <input type="hidden" name="teacher_ids[]" value="<?= (int)$lid ?>">
        <?php endforeach; ?>
      </div>

      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Klicke zum Öffnen, wähle mehrere Lehrer aus. Nochmal klicken zum Schließen.</p>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit"
        class="px-4 py-2 rounded border border-green-600 text-green-600 font-semibold hover:bg-green-600 hover:text-white transition">Speichern</button>
      <a href="/grundlagen/schulverwaltung/classes.php"
        class="px-4 py-2 rounded border border-gray-400 text-gray-600 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Abbrechen</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const root = document.getElementById('class-teachers-multiselect');
  if (!root) return;

  const trigger = root.querySelector('[data-trigger]');
  const triggerLabel = root.querySelector('[data-trigger-label]');
  const panel = root.querySelector('[data-panel]');
  const chips = root.querySelector('[data-chips]');
  const hidden = root.querySelector('[data-hidden]');
  const arrow = root.querySelector('[data-arrow]');
  const options = Array.from(root.querySelectorAll('[data-option]'));

  const state = new Map(); // id -> { label, selected, btn }

  options.forEach(btn => {
    const id = parseInt(btn.getAttribute('value') || '0', 10);
    const label = btn.querySelector('span')?.textContent?.trim() || ('Lehrer #' + id);
    const selected = btn.querySelector('[data-check]').style.display !== 'none';
    state.set(id, { label, selected, btn });
  });

  function setOpen(open) {
    panel.classList.toggle('hidden', !open);
    arrow.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
    trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  function renderHidden() {
    hidden.innerHTML = '';
    state.forEach((o, id) => {
      if (o.selected) {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'teacher_ids[]';
        inp.value = String(id);
        hidden.appendChild(inp);
      }
    });
  }

  function renderChips() {
    chips.innerHTML = '';
    const selected = [];
    state.forEach((o, id) => {
      if (o.selected) {
        selected.push({ id, label: o.label });
        const chip = document.createElement('span');
        chip.className = 'px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
        chip.textContent = o.label;
        chip.setAttribute('data-id', String(id));
        chips.appendChild(chip);
      }
    });
    triggerLabel.textContent = selected.length
      ? selected.map(s => s.label).join(', ')
      : 'Lehrer wählen';
  }

  function toggle(id) {
    const o = state.get(id);
    if (!o) return;
    o.selected = !o.selected;
    const check = o.btn.querySelector('[data-check]');
    check.style.display = o.selected ? '' : 'none';
    renderHidden();
    renderChips();
  }

  options.forEach(btn => {
    btn.addEventListener('click', () => toggle(parseInt(btn.getAttribute('value') || '0', 10)));
  });

  trigger.addEventListener('click', () => setOpen(panel.classList.contains('hidden')));

  document.addEventListener('click', (e) => {
    if (!root.contains(e.target)) setOpen(false);
  });

  // Initial render
  renderHidden();
  renderChips();
});
</script>