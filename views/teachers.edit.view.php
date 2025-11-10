<?php /** @var array $teacher */ /** @var array $subjects */ /** @var array $selectedSubjectIds */ ?>
<div class="max-w-xl mx-auto p-6 space-y-6">
  <div class="flex justify-between items-baseline">
    <h1 class="text-xl font-semibold"><?= htmlspecialchars($title) ?></h1>
    <a href="/grundlagen/schulverwaltung/teachers.php"
      class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Zurück</a>
  </div>

  <form method="post" action="/grundlagen/schulverwaltung/actions/teachers/edit.php"
    class="space-y-5 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-5">
    <input type="hidden" name="id" value="<?= (int)$teacher['id'] ?>">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm mb-1" for="vorname">Vorname</label>
        <input id="vorname" name="vorname" type="text" required
          value="<?= htmlspecialchars($teacher['vorname'] ?? '') ?>"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
      <div>
        <label class="block text-sm mb-1" for="nachname">Nachname</label>
        <input id="nachname" name="nachname" type="text" required
          value="<?= htmlspecialchars($teacher['nachname'] ?? '') ?>"
          class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900">
      </div>
    </div>

    <!-- Fächer: Dropdown-Mehrfachauswahl mit Pfeil -->
    <div id="subjects-multiselect" class="relative">
      <label class="block text-sm mb-1">Fächer (Mehrfachauswahl)</label>

      <button type="button" data-trigger
        class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 flex items-center justify-between">
        <span data-trigger-label>Fächer wählen</span>
        <span data-arrow class="ml-2 inline-block transition-transform duration-200 text-gray-500">▾</span>
      </button>

      <div data-panel
        class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow hidden">
        <ul class="max-h-60 overflow-auto divide-y divide-gray-100 dark:divide-gray-700">
          <?php foreach ($subjects as $s): ?>
          <?php
              $sid = (int)$s['id'];
              $label = htmlspecialchars($s['fach'] ?? ('Fach #' . $sid));
              $isSel = in_array($sid, $selectedSubjectIds, true);
            ?>
          <li>
            <button type="button" data-option value="<?= $sid ?>"
              class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-between">
              <span><?= $label ?></span>
              <span data-check class="text-green-600 font-semibold"
                style="<?= $isSel ? '' : 'display:none;' ?>">✓</span>
            </button>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div data-chips class="mt-2 flex flex-wrap gap-2 text-sm">
        <?php foreach ($subjects as $s): ?>
        <?php $sid = (int)$s['id']; $label = htmlspecialchars($s['fach'] ?? ('Fach #' . $sid)); ?>
        <?php if (in_array($sid, $selectedSubjectIds, true)): ?>
        <span data-chip data-id="<?= $sid ?>"
          class="px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
          <?= $label ?>
        </span>
        <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div data-hidden>
        <?php foreach ($selectedSubjectIds as $sid): ?>
        <input type="hidden" name="subject_ids[]" value="<?= (int)$sid ?>">
        <?php endforeach; ?>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit"
        class="px-4 py-2 rounded border border-green-600 text-green-600 font-semibold hover:bg-green-600 hover:text-white transition">Speichern</button>
      <a href="/grundlagen/schulverwaltung/teachers.php"
        class="px-4 py-2 rounded border border-gray-400 text-gray-600 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Abbrechen</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const root = document.getElementById('subjects-multiselect');
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
    const label = btn.querySelector('span')?.textContent?.trim() || ('Fach #' + id);
    const selected = btn.querySelector('[data-check]').style.display !== 'none';
    state.set(id, {
      label,
      selected,
      btn
    });
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
        inp.name = 'subject_ids[]';
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
        selected.push({
          id,
          label: o.label
        });
        const chip = document.createElement('span');
        chip.className =
          'px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
        chip.textContent = o.label;
        chip.setAttribute('data-id', String(id));
        chips.appendChild(chip);
      }
    });
    triggerLabel.textContent = selected.length ?
      selected.map(s => s.label).join(', ') :
      'Fächer wählen';
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