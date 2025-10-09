<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();

$raw = DataSchool::getOffices();
$rows = getRows($raw, ['vorname', 'nachname', 'email']);

$entity = 'Büro Assistenten';

$columns = [
  ['label' => 'Vorname', 'field' => 'vorname'],
  ['label' => 'Nachname', 'field' => 'nachname'],
  ['label' => 'E-Mail', 'field' => 'email'],
];

setView($entity, $isAuthenticated, $columns, $rows);