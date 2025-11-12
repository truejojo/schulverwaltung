<?php
session_start();
require __DIR__ . '/../../app/app.php';

$title = 'Benutzer anlegen';

$prefillRole = (int)($_GET['role'] ?? 0);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $data = [
    'email'      => trim((string)($_POST['email'] ?? '')),
    'password'   => (string)($_POST['password'] ?? ''),
    'vorname'    => trim((string)($_POST['vorname'] ?? '')),
    'nachname'   => trim((string)($_POST['nachname'] ?? '')),
    'adresse'    => trim((string)($_POST['adresse'] ?? '')),
    'plz'        => trim((string)($_POST['plz'] ?? '')),
    'telefon'    => trim((string)($_POST['telefon'] ?? '')),
    'geburtstag' => trim((string)($_POST['geburtstag'] ?? '')),
    'role_id'    => trim((string)($_POST['role_id'] ?? '')),
    'klasse_id'  => (int)($_POST['klasse_id'] ?? 0),
    'verwaltungs_rolle_id' => (int)($_POST['verwaltungs_rolle_id'] ?? 0),
  ];

  $ok = DataSchool::createUser($data);
  header('Location: /grundlagen/schulverwaltung/users.php' . ($ok ? '?created=1' : '?err=create_failed'));
  exit;
}

// GET: Listen für Auswahl
$klassen = DataSchool::getAllClasses();
$verwaltungsRollen = DataSchool::getAllVerwaltungsRollen();

$_viewFile = APP_PATH . '/views/users.add.view.php';
require APP_PATH . '/views/layout.root.php';