<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = isset($_GET['perPage']) ? max(1, min(50, (int) $_GET['perPage'])) : 20;

$result = DataSchool::getLearnersPaginated($page, $perPage);

$rows = $result['items'];
$pagination = [
  'page' => $result['page'],
  'pages' => $result['pages'],
  'hasPrev' => $result['hasPrev'],
  'hasNext' => $result['hasNext'],
];

$entity = 'Schüler';
$columns = [
  ['label' => 'Vorname', 'field' => 'vorname'],
  ['label' => 'Nachname', 'field' => 'nachname'],
  ['label' => 'Klasse', 'field' => 'klasse'],
];

setView($entity, $isAuthenticated, $columns, $rows, $pagination);