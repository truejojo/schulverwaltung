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

// Klassen-IDs
$klassen = $pdo->query('SELECT id FROM klassen ORDER BY id ASC')->fetchAll(PDO::FETCH_COLUMN);
if (count($klassen) < 1) {
  exit("Keine Klassen gefunden. Bitte erst Klassen anlegen.\n");
}

// Beispieldaten
$vn = ['Anna', 'Thomas', 'Julia', 'Michael', 'Katrin', 'Stefan', 'Sabine', 'Andreas', 'Petra', 'Martin', 'Ute', 'Jörg', 'Heike', 'Christian', 'Daniela', 'Frank', 'Claudia', 'Markus', 'Birgit', 'Holger'];
$nn = ['Meyer', 'Schmidt', 'Neumann', 'Fischer', 'Weiss', 'Krause', 'Vogel', 'Schulz', 'Brandt', 'Peters', 'König', 'Franke', 'Hansen', 'Arnold', 'Ludwig', 'Pohl', 'Berg', 'Sommer', 'Busch', 'Ott'];
$streets = ['Lehrerweg', 'Schulstraße', 'Campusallee', 'Holstenweg', 'Alsterchaussee', 'Mittelweg', 'Eimsbütteler Str.', 'Rathausmarkt', 'Elbchaussee', 'Hafenstraße'];
$plzPool = [22527, 22528, 22421, 22451, 22851];

$passwordPlain = 'Schule123!';
$passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);
$roleIdTeacher = 2;

// Statements
$insUser = $pdo->prepare(
  'INSERT INTO users (email, password_hash, vorname, nachname, adresse, plz, telefon, geburtstag, role_id)
   VALUES (:email, :password_hash, :vorname, :nachname, :adresse, :plz, :telefon, :geburtstag, :role_id)'
);

$insLehrer = $pdo->prepare(
  'INSERT INTO lehrer (user_id) VALUES (:user_id)'
);

$insKlassenLehrer = $pdo->prepare(
  'INSERT INTO klassen_lehrer (klasse_id, lehrer_id) VALUES (:klasse_id, :lehrer_id)'
);

// Helfer
$randDate = fn(string $start, string $end): string =>
  date('Y-m-d', random_int(strtotime($start), strtotime($end)));
$randTel = fn(): string => '040' . random_int(1000000, 9999999);

// 1) 12 Lehrer anlegen
$pdo->beginTransaction();
try {
  $lehrerIds = []; // Liste der lehrer.id
  for ($i = 0; $i < 12; $i++) {
    $v = $vn[array_rand($vn)];
    $n = $nn[array_rand($nn)];
    $plz = $plzPool[array_rand($plzPool)];
    $adresse = $streets[array_rand($streets)] . ' ' . random_int(1, 199);

    $email = strtolower(
      iconv('UTF-8', 'ASCII//TRANSLIT', $v) . '.' .
      iconv('UTF-8', 'ASCII//TRANSLIT', $n)
    ) . '+' . uniqid() . '@schule.local';

    $insUser->execute([
      ':email' => $email,
      ':password_hash' => $passwordHash, // wichtig: password_hash
      ':vorname' => $v,
      ':nachname' => $n,
      ':adresse' => $adresse,
      ':plz' => $plz,
      ':telefon' => $randTel(),
      ':geburtstag' => $randDate('1975-01-01', '1995-12-31'),
      ':role_id' => $roleIdTeacher,
    ]);

    $userId = (int) $pdo->lastInsertId();

    // lehrer-Zeile verknüpfen
    $insLehrer->execute([':user_id' => $userId]);
    $lehrerId = (int) $pdo->lastInsertId();
    $lehrerIds[] = $lehrerId;
  }

  // 2) Klassenlehrer-Zuordnung: pro Klasse 1–2 Lehrer, Lehrer max. 2 Klassen
  $teacherLoad = array_fill_keys($lehrerIds, 0);
  shuffle($klassen);

  foreach ($klassen as $klasseId) {
    $needed = random_int(1, 2);

    // Kandidaten mit geringer Last bevorzugen
    $candidates = $lehrerIds;
    usort($candidates, function ($a, $b) use ($teacherLoad) {
      if ($teacherLoad[$a] === $teacherLoad[$b])
        return 0;
      return $teacherLoad[$a] < $teacherLoad[$b] ? -1 : 1;
    });

    $picked = [];
    foreach ($candidates as $lid) {
      if ($teacherLoad[$lid] >= 2)
        continue; // max 2 Klassen pro Lehrer
      $picked[] = $lid;
      if (count($picked) === $needed)
        break;
    }

    // Fallback, falls zu wenig mit <2 gefunden (unwahrscheinlich bei 12/12)
    if (count($picked) < $needed) {
      $more = array_diff($lehrerIds, $picked);
      shuffle($more);
      $picked = array_merge($picked, array_slice($more, 0, $needed - count($picked)));
    }

    foreach ($picked as $lid) {
      $insKlassenLehrer->execute([
        ':klasse_id' => (int) $klasseId,
        ':lehrer_id' => (int) $lid,
      ]);
      $teacherLoad[$lid]++;
    }
  }

  $pdo->commit();
  echo "Fertig: 12 Lehrer angelegt und Klassenlehrer verteilt.\n";
  echo "Standard-Passwort: $passwordPlain\n";
} catch (Throwable $e) {
  $pdo->rollBack();
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