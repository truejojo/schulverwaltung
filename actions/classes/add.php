<?php
session_start();
require __DIR__ . '/../../app/app.php';

$title = 'Klasse anlegen';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $klasse = trim((string)($_POST['klasse'] ?? ''));
  if ($klasse === '') {
    header('Location: /grundlagen/schulverwaltung/classes.php?err=invalid_input');
    exit;
  }
  $ok = DataSchool::createClass($klasse);
  header('Location: /grundlagen/schulverwaltung/classes.php' . ($ok ? '?created=1' : '?err=create_failed'));
  exit;
}

// GET: Formular anzeigen
$_viewFile = APP_PATH . '/views/classes.add.view.php';
require APP_PATH . '/views/layout.root.php';
