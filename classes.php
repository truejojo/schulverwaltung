<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();

$raw = DataSchool::getClasses();
$rows = getRows($raw, ['klasse', 'klassenlehrer']);

$entity = 'Klassen';

$columns = [
  ['label' => 'Klasse', 'field' => 'klasse'],
  ['label' => 'Klassenlehrer', 'field' => 'klassenlehrer'],
];

setView($entity, $isAuthenticated, $columns, $rows);