<?php
$currentSort = $_GET['sort'] ?? null;
$currentDir = strtolower($_GET['dir'] ?? 'asc');
$toggleDir = fn(string $field) => ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
$makeSortUrl = function (string $field) use ($toggleDir) {
  $q = $_GET ?? [];
  $q['sort'] = $field;
  $q['dir'] = $toggleDir($field);
  $q['page'] = 1; // bei neuer Sortierung auf Seite 1
  return '?' . http_build_query($q);
};
$dirIcon = function (string $field) use ($currentSort, $currentDir): string {
  if ($currentSort !== $field)
    return '';
  return $currentDir === 'asc' ? '▲' : '▼';
};
?>

<table class="min-w-full text-left text-sm">
  <thead class="text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wide dark:bg-gray-900">
    <tr>
      <th class="px-4 py-3 font-semibold">Nr</th>
      <?php foreach ($columns as $col): ?>
        <?php $field = $col['field']; ?>
        <th class="px-4 py-3 font-semibold border-l border-2-gray-300 dark:border-gray-600">
          <a href="<?= $makeSortUrl($field) ?>" class="inline-flex items-center gap-1 hover:underline">
            <?= htmlspecialchars($col['label']) ?>
            <span class="opacity-70"><?= $dirIcon($field) ?></span>
          </a>
        </th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody class="">
    <?php foreach ($rows as $i => $r): ?>
      <tr class="transition <?= $i % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700/50 hover:dark:bg-gray-500/50' : 'bg-gray-150 dark:bg-gray-800/50 
       hover:dark:bg-gray-500/50' ?>">
        <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200 w-[20px]">
          <?= htmlspecialchars((string) ($i + 1)) ?>
        </td>
        <?php foreach ($columns as $col): ?>
          <td class="px-4 py-2 whitespace-nowrap text-gray-700 dark:text-gray-200">
            <?= htmlspecialchars((string) ($r[$col['field']] ?? '')) ?>
          </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>