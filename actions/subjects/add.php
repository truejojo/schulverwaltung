<?php
session_start();
require __DIR__ . '/../../app/app.php';

$title = 'Fach anlegen';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  $fach = trim((string)($_POST['fach'] ?? ''));
  if ($fach === '') {
    header('Location: /grundlagen/schulverwaltung/subjects.php?err=invalid_input');
    exit;
  }
  $ok = DataSchool::createSubject($fach);
  header('Location: /grundlagen/schulverwaltung/subjects.php' . ($ok ? '?created=1' : '?err=create_failed'));
  exit;
}

$_viewFile = APP_PATH . '/views/subjects.add.view.php';
require APP_PATH . '/views/layout.root.php';