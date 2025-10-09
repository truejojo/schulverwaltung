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

function validate($type, $value): ?string {
  $sanitized = filter_input(
    $type,
    $value,
    FILTER_SANITIZE_SPECIAL_CHARS
  );
  return $sanitized !== null ? $sanitized : null;
}

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            CONFIG['db_source'],
            CONFIG['db_user'],
            CONFIG['db_password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
    return $pdo;
}