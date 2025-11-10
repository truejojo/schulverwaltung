<?php
$currentSort = $_GET['sort'] ?? null;
$currentDir = strtolower($_GET['dir'] ?? 'asc');
$toggleDir = fn(string $field) => ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
$makeSortUrl = function (string $field) use ($toggleDir) {
  $q = $_GET ?? [];
  $q['sort'] = $field;
  $q['dir'] = $toggleDir($field);
  $q['page'] = 1;
  return '?' . http_build_query($q);
};
$dirIcon = function (string $field) use ($currentSort, $currentDir): string {
  if ($currentSort !== $field) return '';
  return $currentDir === 'asc' ? '▲' : '▼';
};

// Sichtbarkeit für CUD: eingeloggt + role_id=3 + verwaltungs_rolle_id=3
$canManage =
  (function_exists('is_user_authenticated') ? is_user_authenticated() : false) &&
  (function_exists('user_has_role_id') ? user_has_role_id(3) : false) &&
  (function_exists('user_is_verwaltungs_admin') ? user_is_verwaltungs_admin() : false);

$slug = strtolower(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_FILENAME));
$deleteAction = 'actions/' . $slug . '/delete.php';
$editAction   = 'actions/' . $slug . '/edit.php';
?>

<table class="min-w-full text-left text-sm">
  <thead class="text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wide dark:bg-gray-900">
    <tr>
      <th class="px-4 py-3 font-semibold">Nr</th>
      <?php foreach ($columns as $col): ?>
      <?php
          $field = $col['field'];
          $sortable = $col['sortable'] ?? true;
        ?>
      <th class="px-4 py-3 font-semibold border-l border-2-gray-300 dark:border-gray-600">
        <?php if ($sortable): ?>
        <a href="<?= $makeSortUrl($field) ?>" class="inline-flex items-center gap-1 hover:underline">
          <?= htmlspecialchars($col['label']) ?>
          <span class="opacity-70"><?= $dirIcon($field) ?></span>
        </a>
        <?php else: ?>
        <span class="inline-flex items-center gap-1">
          <?= htmlspecialchars($col['label']) ?>
        </span>
        <?php endif; ?>
      </th>
      <?php endforeach; ?>
      <?php if ($canManage): ?>
      <th class="px-4 py-3 font-semibold text-green-400 border-l border-2-gray-300 dark:border-gray-600 text-center">
        Bearbeiten</th>
      <th class="px-4 py-3 font-semibold text-red-400 border-l border-2-gray-300 dark:border-gray-600 text-center">
        Löschen</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody class="">
    <?php foreach ($rows as $i => $r): ?>
    <tr
      class="transition <?= $i % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700/50 hover:dark:bg-gray-500/50' : 'bg-gray-150 dark:bg-gray-800/50 hover:dark:bg-gray-500/50' ?>">
      <td class="px-4 py-2 text-gray-700 dark:text-gray-200 w-[20px]">
        <?= htmlspecialchars((string) ($i + 1)) ?>
      </td>
      <?php foreach ($columns as $col): ?>
      <td class="px-4 py-2 text-gray-700 dark:text-gray-200">
        <?= htmlspecialchars((string) ($r[$col['field']] ?? '')) ?>
      </td>
      <?php endforeach; ?>

      <?php if ($canManage): ?>
      <td
        class="px-4 py-2 text-green-400 font-bold w-[20px] text-center hover:bg-green-400 hover:text-white transition cursor-pointer"
        title="Bearbeiten">
        <a href="<?= htmlspecialchars($editAction . '?id=' . (int)($r['id'] ?? 0)) ?>"
          class="block w-full h-full text-inherit">✓</a>
      </td>
      <td
        class="px-4 py-2 text-red-400 font-bold w-[20px] text-center hover:bg-red-400 hover:text-white transition cursor-pointer"
        title="Löschen">
        <form method="post" action="<?= htmlspecialchars($deleteAction) ?>"
          onsubmit="return confirm('Diesen Eintrag wirklich löschen?');">
          <input type="hidden" name="id" value="<?= (int)($r['id'] ?? 0) ?>">
          <button type="submit" class="w-full h-full block bg-transparent text-inherit font-bold cursor-pointer">
            x
          </button>
        </form>
      </td>
      <?php endif; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>