<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = isset($_GET['perPage']) ? max(1, min(50, (int) $_GET['perPage'])) : 20;
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'nachname';
$dir = strtolower($_GET['dir'] ?? 'asc');

$q = trim((string) ($_GET['q'] ?? ''));
$fields = array_values(array_filter((array) ($_GET['fields'] ?? [])));

$result = DataSchool::getTeachersPaginated($page, $perPage, $sort, $dir, $q, $fields);

$rows = $result['items'];
$pagination = [
  'page' => $result['page'],
  'pages' => $result['pages'],
  'hasPrev' => $result['hasPrev'],
  'hasNext' => $result['hasNext'],
];

$entity = 'Lehrer';
$columns = [
  ['label' => 'Vorname', 'field' => 'vorname', 'sortable' => true],
  ['label' => 'Nachname', 'field' => 'nachname', 'sortable' => true],
  ['label' => 'Fächer', 'field' => 'faecher', 'sortable' => false],
];

$search = [
  'q' => $q,
  'fields' => [
    ['key' => 'vorname', 'label' => 'Vorname'],
    ['key' => 'nachname', 'label' => 'Nachname'],
    ['key' => 'faecher', 'label' => 'Fächer'],
  ],
];

setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);