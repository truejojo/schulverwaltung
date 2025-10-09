<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();

$raw = DataSchool::getSubjects(); 
$rows = getRows($raw, ['fach']);

$entity = 'Fächer';

$columns = [
  ['label' => 'Fach', 'field' => 'fach'],
];

setView($entity, $isAuthenticated, $columns, $rows);