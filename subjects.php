<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = isset($_GET['perPage']) ? max(1, min(50, (int) $_GET['perPage'])) : 20;
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'fach';
$dir = strtolower($_GET['dir'] ?? 'asc');

$result = DataSchool::getSubjectsPaginated($page, $perPage, $sort, $dir);

$rows = $result['items'];
$pagination = [
  'page' => $result['page'],
  'pages' => $result['pages'],
  'hasPrev' => $result['hasPrev'],
  'hasNext' => $result['hasNext'],
];

$entity = 'Fächer';
$columns = [
  ['label' => 'Fach', 'field' => 'fach'],
  ['label' => 'Lehrkräfte', 'field' => 'lehrer'],
];

setView($entity, $isAuthenticated, $columns, $rows, $pagination);