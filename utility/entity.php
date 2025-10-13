<?php
function isAuthenticated(): bool
{
  $isAuthenticated = is_user_authenticated();

  if (!$isAuthenticated) {
    redirect('index.php');
  }
  return $isAuthenticated;
}

function getRows(array $raw, array $fields): array
{
  return array_map(static function (array $t) use ($fields): array {
    $row = [];
    foreach ($fields as $field) {
      $row[$field] = trim(($t[$field] ?? ''));
    }
    return $row;
  }, $raw);
}

function setView($entity, $isAuthenticated, $columns, $rows, $pagination = null): void
{
  view('entity', [
    'title' => 'Schulverwaltung: ' . $entity,
    'headline' => $entity,
    'isAuthenticated' => $isAuthenticated,
    'columns' => $columns,
    'rows' => $rows,
    'emptyMessage' => 'Keine ' . $entity . ' vorhanden.',
    'pagination' => $pagination,
  ]);
}