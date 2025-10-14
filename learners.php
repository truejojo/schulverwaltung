<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = isset($_GET['perPage']) ? max(1, min(50, (int) $_GET['perPage'])) : 20;
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'nachname'; // default
$dir = strtolower($_GET['dir'] ?? 'asc');

$q = trim((string) ($_GET['q'] ?? ''));
$fields = array_values(array_filter((array) ($_GET['fields'] ?? [])));
$matchAll = isset($_GET['all']) && $_GET['all'] === '1';

$result = DataSchool::getLearnersPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);

$rows = $result['items'];
$pagination = [
  'page' => $result['page'],
  'pages' => $result['pages'],
  'hasPrev' => $result['hasPrev'],
  'hasNext' => $result['hasNext'],
];

$entity = 'Schüler';
$columns = [
  ['label' => 'Vorname', 'field' => 'vorname', 'sortable' => true],
  ['label' => 'Nachname', 'field' => 'nachname', 'sortable' => true],
  ['label' => 'Klasse', 'field' => 'klasse', 'sortable' => true],
];

$search = [
  'q' => $q,
  'all' => $matchAll,
  'fields' => [
    ['key' => 'vorname', 'label' => 'Vorname'],
    ['key' => 'nachname', 'label' => 'Nachname'],
    ['key' => 'klasse', 'label' => 'Klasse'],
  ],
];

setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);