<?php
session_start();
require __DIR__ . '/../../app/app.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
  header('Location: /grundlagen/schulverwaltung/users.php?err=invalid_id');
  exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $data = [
    'email'      => trim((string)($_POST['email'] ?? '')),
    'vorname'    => trim((string)($_POST['vorname'] ?? '')),
    'nachname'   => trim((string)($_POST['nachname'] ?? '')),
    'adresse'    => trim((string)($_POST['adresse'] ?? '')),
    'plz'        => trim((string)($_POST['plz'] ?? '')),
    'telefon'    => trim((string)($_POST['telefon'] ?? '')),
    'geburtstag' => trim((string)($_POST['geburtstag'] ?? '')),
    'password'   => (string)($_POST['password'] ?? ''), // optional
  ];
  $ok = DataSchool::updateUser($id, $data);
  header('Location: /grundlagen/schulverwaltung/users.php' . ($ok ? '?updated=1' : '?err=update_failed'));
  exit;
}

$user = DataSchool::getUserById($id);
if (!$user) {
  header('Location: /grundlagen/schulverwaltung/users.php?err=not_found');
  exit;
}

$title = 'Benutzer bearbeiten';
$_viewFile = APP_PATH . '/views/users.edit.view.php';
require APP_PATH . '/views/layout.root.php';