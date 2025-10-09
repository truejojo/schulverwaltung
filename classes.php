<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();

$raw = DataSchool::getClasses(); 
$rows = getRows($raw, ['klasse']);

$entity = 'Klassen';

$columns = [
  ['label' => 'Klasse', 'field' => 'klasse'],
];

setView($entity, $isAuthenticated, $columns, $rows);