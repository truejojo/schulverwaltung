<?php
session_start();
require_once __DIR__ . '/app/app.php';

if (is_user_authenticated()) {
    logout_user();
}
redirect('index.php');