<?php
session_start();
require __DIR__ . '/app/app.php';

$isAuthenticated = is_user_authenticated();

if (!$isAuthenticated) {
    redirect('index.php');
}

$classes = DataSchool::getClasses(); // falls du statt DataSchool -> Data nutzt: Data::getClasses()

view('classes', [
  'title' => 'Schulverwaltung: Klassen',
  'isAuthenticated' => $isAuthenticated,
  'classes' => $classes,
]);