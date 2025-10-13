<?php
/** Erwartet:
 * @var string   $headline
 * @var array    $columns
 * @var array    $rows
 * @var string   $emptyMessage
 * @var array|null $pagination
 */
$emptyMessage = $emptyMessage ?? 'Keine Einträge gefunden.';
?>
<div class="max-w-5xl mx-auto py-8 px-4 space-y-6">
  <div class="flex justify-between items-baseline">
    <?php if (!empty($headline)): ?>
      <?php require APP_PATH . '/components/headline-1.view.php'; ?>
    <?php endif; ?>

    <?php goHomeLink('Zurück zur Übersicht'); ?>
  </div>

  <?php if (empty($rows)): ?>
    <div
      class="p-4 rounded bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
      <?= htmlspecialchars($emptyMessage) ?>
    </div>
  <?php else: ?>
    <div class="rounded border border-gray-200 dark:border-gray-700">
      <?php require APP_PATH . '/components/table.view.php'; ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($pagination) && ($pagination['pages'] ?? 1) > 1): ?>
    <div class="mt-4 ">
      <?php require APP_PATH . '/components/pagination.view.php'; ?>
    </div>
  <?php endif; ?>
</div>

<!-- overflow-x-auto -->