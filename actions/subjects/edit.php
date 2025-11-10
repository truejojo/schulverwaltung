<?php
session_start();
require __DIR__ . '/../../app/app.php';

$canManage =
  (function_exists('is_user_authenticated') ? is_user_authenticated() : false) &&
  (function_exists('user_has_role_id') ? user_has_role_id(3) : false) &&
  (function_exists('user_is_verwaltungs_admin') ? user_is_verwaltungs_admin() : false);

if (!$canManage) {
  header('Location: ../../subjects.php?err=forbidden');
  exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
  $id   = (int)($_POST['id'] ?? 0);
  $fach = trim((string)($_POST['fach'] ?? ''));

  if ($id <= 0 || $fach === '') {
    header('Location: ../../subjects.php?err=invalid_input');
    exit;
  }

  try {
    $ok = DataSchool::updateSubject($id, $fach);
    header('Location: ../../subjects.php' . ($ok ? '?updated=1' : '?err=update_failed'));
  } catch (Throwable $e) {
    header('Location: ../../subjects.php?err=exception');
  }
  exit;
}

// GET → Daten laden und View rendern
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  header('Location: ../../subjects.php?err=bad_id');
  exit;
}

$subject = DataSchool::getSubjectById($id);
if (!$subject) {
  header('Location: ../../subjects.php?err=not_found');
  exit;
}

// Variablen fürs Layout / View
$title = 'Fach bearbeiten';
$_viewFile = APP_PATH . '/views/subjects.edit.view.php';

require APP_PATH . '/views/layout.root.php';