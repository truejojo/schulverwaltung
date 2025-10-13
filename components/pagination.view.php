<?php if (!empty($pagination) && ($pagination['pages'] ?? 1) > 1): ?>
  <?php
  $page = (int) $pagination['page'];
  $pages = (int) $pagination['pages'];
  $hasPrev = (bool) $pagination['hasPrev'];
  $hasNext = (bool) $pagination['hasNext'];
  $q = $_GET ?? [];
  $makeUrl = function (int $p) use ($q): string {
    $q['page'] = $p;
    return '?' . http_build_query($q);
  };
  $start = max(1, $page - 2);
  $end = min($pages, $page + 2);
  ?>
  <nav class="flex justify-center gap-2 text-sm select-none">
    <a href="<?= $hasPrev ? $makeUrl(1) : '#' ?>"
      class="px-2 py-1 rounded border <?= $hasPrev ? 'hover:bg-gray-100 dark:hover:bg-gray-700' : 'opacity-40 cursor-default' ?>">«</a>
    <a href="<?= $hasPrev ? $makeUrl($page - 1) : '#' ?>"
      class="mr-3 px-2 py-1 rounded border <?= $hasPrev ? 'hover:bg-gray-100 dark:hover:bg-gray-700' : 'opacity-40 cursor-default' ?>">‹</a>

    <?php for ($i = $start; $i <= $end; $i++): ?>
      <a href="<?= $makeUrl($i) ?>"
        class="px-2 py-1 rounded border <?= $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>

    <a href="<?= $hasNext ? $makeUrl($page + 1) : '#' ?>"
      class="ml-3 px-2 py-1 rounded border <?= $hasNext ? 'hover:bg-gray-100 dark:hover:bg-gray-700' : 'opacity-40 cursor-default' ?>">›</a>
    <a href="<?= $hasNext ? $makeUrl($pages) : '#' ?>"
      class="px-2 py-1 rounded border <?= $hasNext ? 'hover:bg-gray-100 dark:hover:bg-gray-700' : 'opacity-40 cursor-default' ?>">»</a>
  </nav>
<?php endif; ?>