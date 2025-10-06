<?php
declare(strict_types=1);

function view(string $template, array $data = []): void{
    $viewFile = APP_PATH . '/views/' . $template . '.view.php';
    if (!is_file($viewFile)) {
        throw new RuntimeException("View not found: $viewFile");
    }
    $data['_viewFile'] = $viewFile;
    extract($data, EXTR_SKIP);
    require APP_PATH . '/views/layout.root.php';
}

function redirect($url): void {
  header("Location: $url");
  exit;
}