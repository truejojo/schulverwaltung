<?php
session_start();
require __DIR__ . '/app/app.php';

$isAuthenticated = is_user_authenticated();

if (!$isAuthenticated) {
    redirect('index.php');
}

$offices = DataSchool::getOffices();

view('offices', [
  'title' => 'Schulverwaltung: Büro Assistenten',
  'isAuthenticated' => $isAuthenticated,
  'offices' => $offices,
]);