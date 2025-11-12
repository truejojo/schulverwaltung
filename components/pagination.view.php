<?php
// filepath: c:\MAMP\htdocs\grundlagen\schulverwaltung\components\pagination.view.php
if (empty($pagination) || (($pagination['pages'] ?? 1) <= 1)) return;

$currentScript = basename($_SERVER['PHP_SELF'] ?? 'index.php'); // z.B. users.php
$baseQuery = $_GET ?? [];
unset($baseQuery['page']);

$build = function (int $p) use ($currentScript, $baseQuery): string {
  $qs = http_build_query($baseQuery + ['page' => $p]);
  return $currentScript . '?' . $qs;
};

$page    = (int)($pagination['page'] ?? 1);
$pages   = (int)($pagination['pages'] ?? 1);
$hasPrev = (bool)($pagination['hasPrev'] ?? ($page > 1));
$hasNext = (bool)($pagination['hasNext'] ?? ($page < $pages));

// Fenster rund um die aktuelle Seite (z. B. 2 links, 2 rechts)
$window = 2;
$start = max(1, $page - $window);
$end   = min($pages, $page + $window);
?>
<nav class="flex justify-center gap-2 text-sm select-none mt-4">
  <a href="<?= $hasPrev ? htmlspecialchars($build(1)) : '#' ?>"
     class="px-2 py-1 rounded border <?= $hasPrev ? 'hover:bg-gray-100 dark:hover:bg-gray-700' : 'opacity-40 cursor-default' ?>">«</a>

  <a href="<?= $hasPrev ? htmlspecialchars($build($page - 1)) : '#' ?>"
     class="mr-3 px-2 py-1 rounded border <?= $hasPrev ? 'hover:bg-gray-100 dark:hover:bg-gray-700' : 'opacity-40 cursor-default' ?>">‹</a>

  <?php if ($start > 1): ?>
    <a href="<?= htmlspecialchars($build(1)) ?>"
       class="px-2 py-1 rounded border hover:bg-gray-100 dark:hover:bg-gray-700">1</a>
    <?php if ($start > 2): ?>
      <span class="px-2 py-1 text-gray-500">…</span>
    <?php endif; ?>
  <?php endif; ?>

  <?php for ($i = $start; $i <= $end; $i++): ?>
    <a href="<?= htmlspecialchars($build($i)) ?>"
       class="px-2 py-1 rounded border <?= $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>

  <?php if ($end < $pages): ?>
    <?php if ($end < $pages - 1): ?>
      <span class="px-2 py-1 text-gray-500">…</span>
    <?php endif; ?>
    <a href="<?= htmlspecialchars($build($pages)) ?>"
       class="px-2 py-1 rounded border hover:bg-gray-100 dark:hover:bg-gray-700"><?= $pages ?></a>
  <?php endif; ?>

  <a href="<?= $hasNext ? htmlspecialchars($build($page + 1)) : '#' ?>"
     class="ml-3 px-2 py-1 rounded border <?= $hasNext ? 'hover:bg-gray-100 dark:hover:bg-gray-700' : 'opacity-40 cursor-default' ?>">›</a>

  <a href="<?= $hasNext ? htmlspecialchars($build($pages)) : '#' ?>"
     class="px-2 py-1 rounded border <?= $hasNext ? 'hover:bg-gray-100 dark:hover:bg-gray-700' : 'opacity-40 cursor-default' ?>">»</a>
</nav>