<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();
$officesLinks = getOfficesLinks($isAuthenticated);


$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = isset($_GET['perPage']) ? max(1, min(50, (int) $_GET['perPage'])) : 20;
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'nachname';
$dir = strtolower($_GET['dir'] ?? 'asc');

$q = trim((string) ($_GET['q'] ?? ''));
$fields = array_values(array_filter((array) ($_GET['fields'] ?? [])));
$matchAll = isset($_GET['all']) && $_GET['all'] === '1';

$result = DataSchool::getOfficesPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);

$rows = $result['items'];
$pagination = getPaginationLinks($isAuthenticated, $result);

$entity = $officesLinks['entity'];
$columns = $officesLinks['columns'];
$search = [
  'q' => $q,
  'all' => $matchAll,
  'fields' => $officesLinks['fields'],
];

setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);