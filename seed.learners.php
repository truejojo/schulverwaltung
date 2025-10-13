<?php
declare(strict_types=1);

// DSN anpassen (MAMP: user=root, pass=root)
$dsn = 'mysql:host=127.0.0.1;dbname=schulverwaltung;charset=utf8mb4';
$user = 'root';
$pass = 'root';

$pdo = new PDO($dsn, $user, $pass, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
]);

// Klassen-IDs holen (erwartet mind. 12)
$klassen = $pdo->query('SELECT id FROM klassen ORDER BY id ASC')->fetchAll(PDO::FETCH_COLUMN);
if (count($klassen) < 1) {
  exit("Keine Klassen gefunden. Bitte erst Klassen anlegen.\n");
}

// Beispiel-Daten
$vn = ['Max', 'Mia', 'Ben', 'Emma', 'Paul', 'Lea', 'Finn', 'Sofia', 'Luca', 'Lina', 'Jonas', 'Lilly', 'Luis', 'Hannah', 'Noah', 'Ida', 'Felix', 'Clara', 'Leo', 'Maya'];
$nn = ['Müller', 'Schmidt', 'Schneider', 'Fischer', 'Weber', 'Wagner', 'Becker', 'Hoffmann', 'Schäfer', 'Koch', 'Bauer', 'Richter', 'Klein', 'Wolf', 'Neumann', 'Schwarz', 'Zimmermann', 'Krüger', 'Hartmann', 'Lange'];
$streets = ['Musterstraße', 'Hauptstraße', 'Schulweg', 'Gartenweg', 'Feldstraße', 'Bergstraße', 'Seestraße', 'Bahnhofstraße', 'Dorfstraße', 'Ringstraße'];
$plzPool = [22527, 22528, 22421, 22451, 22851];

$passwordPlain = 'Schule123!';
$passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);
$roleIdLearner = 1;

// Statements
$insUser = $pdo->prepare(
  'INSERT INTO users (email, password_hash, vorname, nachname, adresse, plz, telefon, geburtstag, role_id)
   VALUES (:email, :password_hash, :vorname, :nachname, :adresse, :plz, :telefon, :geburtstag, :role_id)'
);

$insSchueler = $pdo->prepare(
  'INSERT INTO schueler (user_id, klasse_id) VALUES (:user_id, :klasse_id)'
);

// Helfer
$randDate = function (string $start, string $end): string {
  $ts = random_int(strtotime($start), strtotime($end));
  return date('Y-m-d', $ts);
};
$randTel = function (): string {
  return '040' . random_int(1000000, 9999999);
};

$pdo->beginTransaction();
try {
  $total = 0;
  foreach ($klassen as $idx => $klasseId) {
    // 18–20 Schüler pro Klasse
    $count = random_int(18, 20);
    for ($i = 0; $i < $count; $i++) {
      $v = $vn[array_rand($vn)];
      $n = $nn[array_rand($nn)];
      $plz = $plzPool[array_rand($plzPool)];
      $adress = $streets[array_rand($streets)] . ' ' . random_int(1, 99);
      $email = strtolower(
        iconv('UTF-8', 'ASCII//TRANSLIT', $v) . '.' .
        iconv('UTF-8', 'ASCII//TRANSLIT', $n)
      );
      // E-Mail eindeutig machen
      $email .= '+' . uniqid() . '@schule.local';

      $insUser->execute([
        ':email' => $email,
        ':password_hash' => $passwordHash,
        ':vorname' => $v,
        ':nachname' => $n,
        ':adresse' => $adress,        // Placeholder = :adresse
        ':plz' => $plz,
        ':telefon' => $randTel(),     // Placeholder = :telefon
        ':geburtstag' => $randDate('2014-01-01', '2017-12-31'), // Placeholder = :geburtstag
        ':role_id' => $roleIdLearner,
      ]);

      $userId = (int) $pdo->lastInsertId();
      $insSchueler->execute([
        ':user_id' => $userId,
        ':klasse_id' => (int) $klasseId,
      ]);
      $total++;
    }
  }

  $pdo->commit();
  echo "Fertig: $total Schüler angelegt.\n";
  echo "Standard-Passwort: $passwordPlain\n";
} catch (Throwable $e) {
  $pdo->rollBack();
  // Fallback: im CLI stderr, sonst error_log
  if (PHP_SAPI === 'cli') {
    $stderr = fopen('php://stderr', 'w');
    fwrite($stderr, "Fehler: " . $e->getMessage() . PHP_EOL);
    fclose($stderr);
  } else {
    error_log("Fehler: " . $e->getMessage());
    echo "Fehler: " . htmlspecialchars($e->getMessage());
  }
  exit(1);
}