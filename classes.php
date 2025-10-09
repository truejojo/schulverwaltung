<?php
session_start();
require __DIR__ . '/app/app.php';

$isAuthenticated = is_user_authenticated();

if (!$isAuthenticated) {
    redirect('index.php');
}

$raw = DataSchool::getClasses(); 
$rows = array_map(static function(array $t): array {
  return [
    'klasse'  => trim(($t['klasse'] ?? '')),
  ];
}, $raw);

$entity = 'Klassen';

view('entity', [
  'title' => 'Schulverwaltung: ' . $entity,
  'headline' => $entity,
  'isAuthenticated' => $isAuthenticated,
   'columns' => [
    ['label' => 'Klasse',  'field' => 'klasse'],
  ],
  'rows' => $rows,
  'emptyMessage' => 'Keine ' . $entity . ' vorhanden.',
]);