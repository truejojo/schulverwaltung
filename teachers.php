<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();
$teachersLinks = getTeachersLinks($isAuthenticated);

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = isset($_GET['perPage']) ? max(1, min(50, (int) $_GET['perPage'])) : 20;
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'nachname';
$dir = strtolower($_GET['dir'] ?? 'asc');

$q = trim((string) ($_GET['q'] ?? ''));
$fields = array_values(array_filter((array) ($_GET['fields'] ?? [])));
$matchAll = isset($_GET['all']) && $_GET['all'] === '1';

$result = DataSchool::getTeachersPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);

$rows = $result['items'];
$pagination = getPaginationLinks($isAuthenticated, $result);

$entity = $teachersLinks['entity'];
$columns = $teachersLinks['columns'];
$search = [
  'q' => $q,
  'all' => $matchAll,
  'fields' => $teachersLinks['fields'],
];
setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);