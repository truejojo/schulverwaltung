<?php
session_start();
require_once __DIR__ . '/app/app.php';

view('index', [
  'title' => 'Schulverwaltung',
  'isAuthenticated' => is_user_authenticated(),
]);