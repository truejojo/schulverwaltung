<?php
session_start();
require_once __DIR__ . '/app/app.php';

$isAuthenticated = is_user_authenticated();
$entities = getEntities($isAuthenticated);
$title = 'Willkommen in der Schulverwaltung';

view('index', [
  'title' => $title,
  'isAuthenticated' => $isAuthenticated,
  'entities' => $entities,
]);