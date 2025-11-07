<?php
session_start();
require __DIR__ . '/../../app/app.php';

$canManage =
  (function_exists('is_user_authenticated') ? is_user_authenticated() : false) &&
  (function_exists('user_has_role_id') ? user_has_role_id(3) : false) &&
  (function_exists('user_is_verwaltungs_admin') ? user_is_verwaltungs_admin() : false);

if (!$canManage) { header('Location: ../../teachers.php?err=forbidden'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../../teachers.php'); exit; }

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { header('Location: ../../teachers.php?err=bad_id'); exit; }

try {
  $ok = DataSchool::deleteTeacher($id);
  header('Location: ../../teachers.php' . ($ok ? '' : '?err=delete_failed'));
} catch (Throwable $e) {
  header('Location: ../../teachers.php?err=exception');
}
exit;