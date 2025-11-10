<?php
// filepath: c:\MAMP\htdocs\grundlagen\schulverwaltung\actions\offices\edit.php
session_start();
require __DIR__ . '/../../app/app.php';

$canManage =
  (function_exists('is_user_authenticated') ? is_user_authenticated() : false) &&
  (function_exists('user_has_role_id') ? user_has_role_id(3) : false) &&
  (function_exists('user_is_verwaltungs_admin') ? user_is_verwaltungs_admin() : false);
if (!$canManage) { header('Location: /grundlagen/schulverwaltung/offices.php?err=forbidden'); exit; }

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
  $id       = (int)($_POST['id'] ?? 0); // users.id
  $vorname  = trim((string)($_POST['vorname'] ?? ''));
  $nachname = trim((string)($_POST['nachname'] ?? ''));
  $rolleId  = (int)($_POST['verwaltungs_rolle_id'] ?? 0);
  if ($id <= 0 || $vorname === '' || $nachname === '' || $rolleId <= 0) {
    header('Location: /grundlagen/schulverwaltung/offices.php?err=invalid_input'); exit;
  }
  try {
    $ok = DataSchool::updateOffice($id, $vorname, $nachname, $rolleId);
    header('Location: /grundlagen/schulverwaltung/offices.php' . ($ok ? '?updated=1' : '?err=update_failed'));
  } catch (Throwable $e) {
    header('Location: /grundlagen/schulverwaltung/offices.php?err=exception');
  }
  exit;
}

// GET
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /grundlagen/schulverwaltung/offices.php?err=bad_id'); exit; }

$office = DataSchool::getOfficeByUserId($id);
if (!$office) { header('Location: /grundlagen/schulverwaltung/offices.php?err=not_found'); exit; }

$rollen = DataSchool::getAllVerwaltungsRollen();

$title = 'Büro Assistent/-in bearbeiten';
$_viewFile = APP_PATH . '/views/offices.edit.view.php';
require APP_PATH . '/views/layout.root.php';