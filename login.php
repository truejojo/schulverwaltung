<?php
session_start();

require_once __DIR__ . '/app/app.php';

if (is_user_authenticated()) {
  redirect('index.php');
}

$status = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

  if (!$email) { $status[] = 'Bitte eine gültige Email angeben!'; }
  if (!$password) { $status[] = 'Passwort fehlt.'; }

  if (!$status && authenticateUser($email, $password)) {
    session_regenerate_id(true);
    redirect('index.php');
  } elseif (!$status) {
    $status[] = 'Login fehlgeschlagen!';
  }
}

$viewData = [
  'title' => 'Login',
  'status' => $status,
];

view('login', $viewData);