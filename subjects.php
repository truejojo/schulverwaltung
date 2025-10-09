<?php
session_start();
require __DIR__ . '/app/app.php';

$isAuthenticated = is_user_authenticated();

if (!$isAuthenticated) {
    redirect('index.php');
}

$raw = DataSchool::getSubjects(); 
$rows = array_map(static function(array $t): array {
  return [
    'fach'  => trim(($t['fach'] ?? '')),
  ];
}, $raw);

$entity = 'Fächer';

view('entity', [
  'title' => 'Schulverwaltung: ' . $entity,
  'headline' => $entity,
  'isAuthenticated' => $isAuthenticated,
   'columns' => [
    ['label' => 'Fach',  'field' => 'fach'],
  ],
  'rows' => $rows,
  'emptyMessage' => 'Keine ' . $entity . ' vorhanden.',
]);