<?php
session_start();
require __DIR__ . '/../../app/app.php';

$canManage =
  (function_exists('is_user_authenticated') ? is_user_authenticated() : false) &&
  (function_exists('user_has_role_id') ? user_has_role_id(3) : false) &&
  (function_exists('user_is_verwaltungs_admin') ? user_is_verwaltungs_admin() : false);
if (!$canManage) { header('Location: /grundlagen/schulverwaltung/learners.php?err=forbidden'); exit; }

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
  $id       = (int)($_POST['id'] ?? 0); // users.id
  $vorname  = trim((string)($_POST['vorname'] ?? ''));
  $nachname = trim((string)($_POST['nachname'] ?? ''));
  $klasseId = (int)($_POST['klasse_id'] ?? 0);
  if ($id <= 0 || $vorname === '' || $nachname === '' || $klasseId <= 0) {
    header('Location: /grundlagen/schulverwaltung/learners.php?err=invalid_input'); exit;
  }
  try {
    $ok = DataSchool::updateLearner($id, $vorname, $nachname, $klasseId);
    header('Location: /grundlagen/schulverwaltung/learners.php' . ($ok ? '?updated=1' : '?err=update_failed'));
  } catch (Throwable $e) {
    header('Location: /grundlagen/schulverwaltung/learners.php?err=exception');
  }
  exit;
}

// GET
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /grundlagen/schulverwaltung/learners.php?err=bad_id'); exit; }

$learner = DataSchool::getLearnerByUserId($id);
if (!$learner) { header('Location: /grundlagen/schulverwaltung/learners.php?err=not_found'); exit; }

$classes = DataSchool::getAllClasses(); // [ [id, klasse], ... ]

$title = 'Schüler/-in bearbeiten';
$_viewFile = APP_PATH . '/views/learners.edit.view.php';
require APP_PATH . '/views/layout.root.php';