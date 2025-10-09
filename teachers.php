<?php
session_start();
require __DIR__ . '/app/app.php';

$isAuthenticated = is_user_authenticated();

if (!$isAuthenticated) {
  redirect('index.php');
}

$raw = DataSchool::getTeachers();
$rows = array_map(static function (array $t): array {
  return [
    'vorname' => trim(($t['vorname'] ?? '')),
    'nachname' => trim(($t['nachname'] ?? '')),
    'email' => trim(($t['email'] ?? '')),
  ];
}, $raw);

$entity = 'Lehrer';

view('entity', [
  'title' => 'Schulverwaltung: ' . $entity,
  'headline' => $entity,
  'isAuthenticated' => $isAuthenticated,
  'columns' => [
    ['label' => 'Vorname', 'field' => 'vorname'],
    ['label' => 'Nachname', 'field' => 'nachname'],
    ['label' => 'E-Mail', 'field' => 'email'],
  ],
  'rows' => $rows,
  'emptyMessage' => 'Keine ' . $entity . ' vorhanden.',
]);