<?php
$q = (string) ($_GET['q'] ?? ($search['q'] ?? ''));
$selected = (array) ($_GET['fields'] ?? []);
$sort = $_GET['sort'] ?? null;
$dir = $_GET['dir'] ?? null;
$perPage = $_GET['perPage'] ?? null;
$matchAll = isset($_GET['all']) ? ($_GET['all'] === '1') : (bool) ($search['all'] ?? false);
?>
<form method="get" class="flex flex-col gap-4">
  <div class="flex items-center gap-3">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Suchen …"
      class="flex-1 px-3 py-2 rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" />
    <?php if ($sort): ?><input type="hidden" name="sort" value="<?= htmlspecialchars((string) $sort) ?>"><?php endif; ?>
    <?php if ($dir): ?><input type="hidden" name="dir" value="<?= htmlspecialchars((string) $dir) ?>"><?php endif; ?>
    <?php if ($perPage): ?><input type="hidden" name="perPage"
      value="<?= htmlspecialchars((string) $perPage) ?>"><?php endif; ?>

    <label class="inline-flex items-center gap-2 text-sm cursor-pointer">
      <input type="checkbox" name="all" value="1" class="peer" <?= $matchAll ? 'checked' : '' ?>>
      <span
        class="px-2 py-1 rounded border text-xs
                   <?= $matchAll ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700' ?>">
        Alle Wörter müssen vorkommen
      </span>
    </label>

    <button type="submit" class="px-3 py-2 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">
      Suchen
    </button>
    <a href="?" class="px-3 py-2 rounded border text-sm hover:bg-gray-100 dark:hover:bg-gray-700">Zurücksetzen</a>
  </div>

  <?php if (!empty($search['fields'])): ?>
  <div class="flex flex-wrap items-center gap-3">
    <?php foreach ($search['fields'] as $f): ?>
    <?php $key = $f['key'];
        $label = $f['label'];
        $active = in_array($key, $selected, true); ?>
    <label class="cursor-pointer inline-flex">
      <input type="checkbox" name="fields[]" value="<?= htmlspecialchars($key) ?>" class="peer sr-only"
        <?= $active ? 'checked' : '' ?>>
      <span class="px-3 py-1 rounded-full text-xs border transition
                       bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300
                       border-gray-300 dark:border-gray-700
                       peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600">
        <?= htmlspecialchars($label) ?>
      </span>
    </label>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</form>