<?php
session_start();
require __DIR__ . '/app/app.php';

$isAuthenticated = is_user_authenticated();

if (!$isAuthenticated) {
    redirect('index.php');
}

$subjects = DataSchool::getSubjects(); // falls du statt DataSchool -> Data nutzt: Data::getSubjects()

view('subjects', [
  'title' => 'Schulverwaltung: Fächer',
  'isAuthenticated' => $isAuthenticated,
  'subjects' => $subjects,
]);