<?php
session_start();
require __DIR__ . '/app/app.php';

$isAuthenticated = is_user_authenticated();

if (!$isAuthenticated) {
    redirect('index.php');
}

$learners = DataSchool::getLearners();

view('learners', [
  'title' => 'Schulverwaltung: Schüler',
  'isAuthenticated' => $isAuthenticated,
  'learners' => $learners,
]);