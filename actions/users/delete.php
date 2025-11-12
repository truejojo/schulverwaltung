<?php
session_start();
require __DIR__ . '/../../app/app.php';

$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
if ($id <= 0) {
  header('Location: /grundlagen/schulverwaltung/users.php?err=invalid_id');
  exit;
}

$ok = DataSchool::deleteUser($id);
header('Location: /grundlagen/schulverwaltung/users.php' . ($ok ? '?deleted=1' : '?err=delete_failed'));
exit;