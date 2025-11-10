<?php
session_start();
require __DIR__ . '/../../app/app.php';

$canManage =
  (function_exists('is_user_authenticated') ? is_user_authenticated() : false) &&
  (function_exists('user_has_role_id') ? user_has_role_id(3) : false) &&
  (function_exists('user_is_verwaltungs_admin') ? user_is_verwaltungs_admin() : false);
if (!$canManage) { header('Location: /grundlagen/schulverwaltung/teachers.php?err=forbidden'); exit; }

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
  $id       = (int)($_POST['id'] ?? 0);    // users.id
  $vorname  = trim((string)($_POST['vorname'] ?? ''));
  $nachname = trim((string)($_POST['nachname'] ?? ''));
  $subjectIds = array_map('intval', $_POST['subject_ids'] ?? []);
  if ($id <= 0 || $vorname === '' || $nachname === '') {
    header('Location: /grundlagen/schulverwaltung/teachers.php?err=invalid_input'); exit;
  }
  try {
    $ok1 = DataSchool::updateTeacher($id, $vorname, $nachname);
    $ok2 = DataSchool::updateTeacherSubjects($id, $subjectIds);
    header('Location: /grundlagen/schulverwaltung/teachers.php' . ($ok1 && $ok2 ? '?updated=1' : '?err=update_failed'));
  } catch (Throwable $e) {
    header('Location: /grundlagen/schulverwaltung/teachers.php?err=exception');
  }
  exit;
}

// GET
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /grundlagen/schulverwaltung/teachers.php?err=bad_id'); exit; }

$teacher = DataSchool::getTeacherByUserId($id);
if (!$teacher) { header('Location: /grundlagen/schulverwaltung/teachers.php?err=not_found'); exit; }

$subjects = DataSchool::getAllSubjects();
$selectedSubjectIds = DataSchool::getTeacherSubjectIds($id);

$title = 'Lehrer bearbeiten';
$_viewFile = APP_PATH . '/views/teachers.edit.view.php';
require APP_PATH . '/views/layout.root.php';