<?php
session_start();
require __DIR__ . '/app/app.php';

$isAuthenticated = is_user_authenticated();

if (!$isAuthenticated) {
    redirect('index.php');
}

$teachers = DataSchool::getTeachers();

view('teachers', [
  'title' => 'Schulverwaltung: Lehrer',
  'isAuthenticated' => $isAuthenticated,
  'teachers' => $teachers,
]);