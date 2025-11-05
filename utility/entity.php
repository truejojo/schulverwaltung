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

function setView($entity, $isAuthenticated, $columns, $rows, $pagination = null, $search = null): void
{
  view('entity', [
    'title' => 'Schulverwaltung: ' . $entity,
    'headline' => $entity,
    'isAuthenticated' => $isAuthenticated,
    'columns' => $columns,
    'rows' => $rows,
    'emptyMessage' => 'Keine ' . $entity . ' vorhanden.',
    'pagination' => $pagination,
    'search' => $search,
  ]);
}

/**
 * Liest und normalisiert die Standard-Listen-Query-Parameter.
 * Optionen:
 *  - sortDefault (string)
 *  - perPageDefault (int, default 20)
 *  - perPageMax (int, default 50)
 */
function parseListQuery(array $options = []): array {
    $perPageDefault = (int)($options['perPageDefault'] ?? 20);
    $perPageMax     = (int)($options['perPageMax'] ?? 50);
    $sortDefault    = (string)($options['sortDefault'] ?? 'id');
    $dirDefault     = 'asc';

    $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = isset($_GET['perPage']) ? max(1, min($perPageMax, (int)$_GET['perPage'])) : $perPageDefault;
    $sort    = isset($_GET['sort']) ? (string)$_GET['sort'] : $sortDefault;
    $dir     = strtolower($_GET['dir'] ?? $dirDefault);
    $dir     = $dir === 'desc' ? 'desc' : 'asc';

    $q       = trim((string)($_GET['q'] ?? ''));
    $fields  = array_values(array_filter((array)($_GET['fields'] ?? [])));
    $matchAll = isset($_GET['all']) && $_GET['all'] === '1';

    return compact('page','perPage','sort','dir','q','fields','matchAll');
}

/** Baut das Search-Array für die View. */
function buildSearchContext(string $q, bool $matchAll, array $fieldsConfig): array {
    return [
        'q' => $q,
        'all' => $matchAll,
        'fields' => $fieldsConfig,
    ];
}