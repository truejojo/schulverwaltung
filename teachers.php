<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();

$raw = DataSchool::getTeachers();
$rows = getRows($raw, ['vorname', 'nachname', 'faecher']);

$entity = 'Lehrer';

$columns = [
  ['label' => 'Vorname', 'field' => 'vorname'],
  ['label' => 'Nachname', 'field' => 'nachname'],
  ['label' => 'Fächer', 'field' => 'faecher'],
];

setView($entity, $isAuthenticated, $columns, $rows);