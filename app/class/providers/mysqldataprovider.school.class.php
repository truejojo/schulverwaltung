<?php

declare(strict_types=1);

class DatabaseConnectionException extends RuntimeException
{
}

class MySqlDataProviderSchool extends DataProviderSchool
{
  protected string $source;
  protected string $dbUser;
  protected string $dbPassword;
  private ?PDO $connection = null; // Lazy-initialisierte, wiederverwendete Verbindung
  public function __construct(string $source, string $dbUser, string $dbPassword)
  {
    $this->source = $source;
    $this->dbUser = $dbUser;
    $this->dbPassword = $dbPassword;
  }

  // getter
  // public function getSubjects(): array
  // {
  //   $sql = "SELECT
  //     f.id,
  //     f.fach,
  //     COALESCE(
  //       GROUP_CONCAT(
  //         DISTINCT CONCAT(u.vorname, ' ', u.nachname)
  //         ORDER BY u.nachname, u.vorname SEPARATOR ', '
  //       ), ''
  //     ) AS lehrer
  //   FROM faecher f
  //   LEFT JOIN lehrer_fach lf ON lf.fach_id = f.id
  //   LEFT JOIN lehrer l ON l.id = lf.lehrer_id
  //   LEFT JOIN users u ON u.id = l.user_id
  //   GROUP BY f.id, f.fach
  //   ORDER BY f.fach ASC
  // ";

  //   $schema = [
  //     'id' => ['source' => 'id', 'cast' => 'int'],
  //     'fach' => ['source' => 'fach', 'trim' => true],
  //     'lehrer' => ['source' => 'lehrer', 'default' => ''],
  //   ];

  //   return $this->fetchMapped($sql, $schema);
  // }

  public function getSubjectsPaginated(int $page, int $perPage, string $sort = 'fach', string $dir = 'asc'): array
  {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));

    $db = $this->dbConnect();
    $total = (int) $db->query('SELECT COUNT(*) FROM faecher')->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
      $page = $pages;
    }
    $offset = ($page - 1) * $perPage;

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'fach' => 'f.fach %s',
      'lehrer' => 'lehrer %s, f.fach ASC',
    ], 'f.fach ASC');

    $sql = "SELECT
      f.id,
      f.fach,
      COALESCE(
        GROUP_CONCAT(
          DISTINCT CONCAT(u.nachname, ', ', u.vorname)
          ORDER BY u.nachname, u.vorname SEPARATOR ', '
        ), ''
      ) AS lehrer
    FROM faecher f
    LEFT JOIN lehrer_fach lf ON lf.fach_id = f.id
    LEFT JOIN lehrer l ON l.id = lf.lehrer_id
    LEFT JOIN users u ON u.id = l.user_id
    GROUP BY f.id, f.fach
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
      'items' => array_map(static fn($r) => [
        'id' => (int) $r['id'],
        'fach' => trim($r['fach'] ?? ''),
        'lehrer' => $r['lehrer'] ?? '',
      ], $rows),
      'total' => $total,
      'page' => $page,
      'perPage' => $perPage,
      'pages' => $pages,
      'hasPrev' => $page > 1,
      'hasNext' => $page < $pages,
    ];
  }

  // public function getClasses(): array
  // {
  //   $sql = "SELECT
  //     k.id,
  //     k.klasse,
  //     COALESCE(
  //       GROUP_CONCAT(
  //         DISTINCT CONCAT(u.vorname, ' ', u.nachname)
  //         ORDER BY u.vorname, u.nachname SEPARATOR ', '
  //       ), ''
  //     ) AS klassenlehrer
  //   FROM klassen k
  //   LEFT JOIN klassen_lehrer kl ON kl.klasse_id = k.id
  //   LEFT JOIN lehrer l ON l.id = kl.lehrer_id
  //   LEFT JOIN users u ON u.id = l.user_id
  //   GROUP BY k.id, k.klasse
  //   ORDER BY k.klasse ASC
  // ";
  //   $schema = [
  //     'id' => ['source' => 'id', 'cast' => 'int'],
  //     'klasse' => ['source' => 'klasse', 'trim' => true],
  //     'klassenlehrer' => ['source' => 'klassenlehrer', 'default' => ''],
  //   ];

  //   return $this->fetchMapped($sql, $schema);
  // }

  public function getClassesPaginated(int $page, int $perPage, string $sort = 'klasse', string $dir = 'asc'): array
  {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));

    $db = $this->dbConnect();
    $total = (int) $db->query('SELECT COUNT(*) FROM klassen')->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
      $page = $pages;
    }
    $offset = ($page - 1) * $perPage;

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'klasse' => 'k.klasse %s',
      'klassenlehrer' => 'klassenlehrer %s, k.klasse ASC',
    ], 'k.klasse ASC');

    $sql = "SELECT
      k.id,
      k.klasse,
      COALESCE(
        GROUP_CONCAT(
          DISTINCT CONCAT(u.nachname, ', ', u.vorname)
          ORDER BY u.nachname, u.vorname SEPARATOR ', '
        ), ''
      ) AS klassenlehrer
    FROM klassen k
    LEFT JOIN klassen_lehrer kl ON kl.klasse_id = k.id
    LEFT JOIN lehrer l ON l.id = kl.lehrer_id
    LEFT JOIN users u ON u.id = l.user_id
    GROUP BY k.id, k.klasse
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
      'items' => array_map(static fn($r) => [
        'id' => (int) $r['id'],
        'klasse' => trim($r['klasse'] ?? ''),
        'klassenlehrer' => $r['klassenlehrer'] ?? '',
      ], $rows),
      'total' => $total,
      'page' => $page,
      'perPage' => $perPage,
      'pages' => $pages,
      'hasPrev' => $page > 1,
      'hasNext' => $page < $pages,
    ];
  }
  public function getPLZ(): array
  {
    return [];
  }

  public function getCity(): array
  {
    return [];
  }

  // public function getTeachers(): array
  // {
  //   $sql = "SELECT
  //     u.id,
  //     u.vorname,
  //     u.nachname,
  //     COALESCE(GROUP_CONCAT(DISTINCT f.fach ORDER BY f.fach SEPARATOR ' '), '') AS faecher
  //   FROM lehrer l
  //   JOIN users u ON u.id = l.user_id
  //   LEFT JOIN lehrer_fach lf ON lf.lehrer_id = l.id
  //   LEFT JOIN faecher f ON f.id = lf.fach_id
  //   GROUP BY u.id, u.vorname, u.nachname
  //   ORDER BY u.nachname ASC, u.vorname ASC
  // ";

  //   $schema = [
  //     'id' => ['source' => 'id', 'cast' => 'int'],
  //     'vorname' => ['source' => 'vorname', 'trim' => true],
  //     'nachname' => ['source' => 'nachname', 'trim' => true],
  //     'faecher' => ['source' => 'faecher', 'default' => ''],
  //   ];

  //   return $this->fetchMapped($sql, $schema);
  // }

  public function getTeachersPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc'): array
  {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));

    $db = $this->dbConnect();
    $total = (int) $db->query('SELECT COUNT(*) FROM lehrer l JOIN users u ON u.id = l.user_id')->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
      $page = $pages;
    }
    $offset = ($page - 1) * $perPage;

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'vorname' => 'u.vorname %s, u.nachname ASC',
      'nachname' => 'u.nachname %s, u.vorname ASC',
      'faecher' => 'faecher %s, u.nachname ASC, u.vorname ASC',
    ], 'u.nachname ASC, u.vorname ASC');

    $sql = "SELECT
      u.id,
      u.vorname,
      u.nachname,
      COALESCE(GROUP_CONCAT(DISTINCT f.fach ORDER BY f.fach SEPARATOR ', '), '') AS faecher
    FROM lehrer l
    JOIN users u ON u.id = l.user_id
    LEFT JOIN lehrer_fach lf ON lf.lehrer_id = l.id
    LEFT JOIN faecher f ON f.id = lf.fach_id
    GROUP BY u.id, u.vorname, u.nachname
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
      'items' => array_map(static fn($r) => [
        'id' => (int) $r['id'],
        'vorname' => trim($r['vorname'] ?? ''),
        'nachname' => trim($r['nachname'] ?? ''),
        'faecher' => $r['faecher'] ?? '',
      ], $rows),
      'total' => $total,
      'page' => $page,
      'perPage' => $perPage,
      'pages' => $pages,
      'hasPrev' => $page > 1,
      'hasNext' => $page < $pages,
    ];
  }
  // public function getLearners(): array
  // {
  //   $sql = 'SELECT u.id, u.vorname, u.nachname, k.klasse AS klasse
  //         FROM schueler s
  //         JOIN users u      ON s.user_id = u.id
  //         LEFT JOIN klassen k ON s.klasse_id = k.id
  //         ORDER BY u.nachname ASC, u.vorname ASC';
  //   $schema = [
  //     'id' => ['source' => 'id', 'cast' => 'int'],
  //     'vorname' => ['source' => 'vorname', 'trim' => true],
  //     'nachname' => ['source' => 'nachname', 'trim' => true],
  //     'klasse' => ['source' => 'klasse', 'trim' => true],
  //   ];

  //   return $this->fetchMapped($sql, $schema);
  // }

  public function getLearnersPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc'): array
  {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));

    // Total
    $total = (int) $this->dbConnect()
      ->query('SELECT COUNT(*) FROM schueler s JOIN users u ON s.user_id = u.id')
      ->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
      $page = $pages;
    }
    $offset = ($page - 1) * $perPage;

    // sichere ORDER-BY-Whitelist
    $orderBy = $this->buildOrderBy($sort, $dir, [
      'vorname' => 'u.vorname %s, u.nachname ASC',
      'nachname' => 'u.nachname %s, u.vorname ASC',
      'klasse' => 'k.klasse %s, u.nachname ASC, u.vorname ASC',
    ], 'u.nachname ASC, u.vorname ASC');

    $sql = "SELECT u.id, u.vorname, u.nachname, k.klasse AS klasse
          FROM schueler s
          JOIN users u      ON s.user_id = u.id
          LEFT JOIN klassen k ON s.klasse_id = k.id
          ORDER BY $orderBy
          LIMIT :limit OFFSET :offset";

    $db = $this->dbConnect();
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = array_map(static fn(array $r): array => [
      'id' => (int) ($r['id'] ?? 0),
      'vorname' => trim($r['vorname'] ?? ''),
      'nachname' => trim($r['nachname'] ?? ''),
      'klasse' => trim($r['klasse'] ?? ''),
    ], $rows);

    return [
      'items' => $items,
      'total' => $total,
      'page' => $page,
      'perPage' => $perPage,
      'pages' => $pages,
      'hasPrev' => $page > 1,
      'hasNext' => $page < $pages,
    ];
  }

  // public function getOffices(): array
  // {
  //   $sql = 'SELECT u.id, u.vorname, u.nachname, u.email
  //         FROM verwaltung v
  //         JOIN users u ON v.user_id = u.id
  //         ORDER BY u.nachname ASC, u.vorname ASC';
  //   $schema = [
  //     'id' => ['source' => 'id', 'cast' => 'int'],
  //     'vorname' => ['source' => 'vorname', 'trim' => true],
  //     'nachname' => ['source' => 'nachname', 'trim' => true],
  //     'email' => ['source' => 'email'],
  //   ];

  //   return $this->fetchMapped($sql, $schema);
  // }

  public function getOfficesPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc'): array
  {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));

    $db = $this->dbConnect();
    $total = (int) $db->query('SELECT COUNT(*) FROM verwaltung v JOIN users u ON u.id = v.user_id')->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
      $page = $pages;
    }
    $offset = ($page - 1) * $perPage;

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'vorname' => 'u.vorname %s, u.nachname ASC',
      'nachname' => 'u.nachname %s, u.vorname ASC',
      'email' => 'u.email %s, u.nachname ASC',
    ], 'u.nachname ASC, u.vorname ASC');

    $sql = "SELECT u.id, u.vorname, u.nachname, u.email
    FROM verwaltung v
    JOIN users u ON u.id = v.user_id
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
      'items' => array_map(static fn($r) => [
        'id' => (int) $r['id'],
        'vorname' => trim($r['vorname'] ?? ''),
        'nachname' => trim($r['nachname'] ?? ''),
        'email' => trim($r['email'] ?? ''),
      ], $rows),
      'total' => $total,
      'page' => $page,
      'perPage' => $perPage,
      'pages' => $pages,
      'hasPrev' => $page > 1,
      'hasNext' => $page < $pages,
    ];
  }

  // setter
  public function setSubjects(string $newSubject): bool
  {
    return false;
  }
  public function setClasses(string $newClass): bool
  {
    return false;
  }
  public function setPLZ(int $newPLZ): bool
  {
    return false;
  }
  public function setCity(string $newCity): bool
  {
    return false;
  }

  // private helper
  private function buildOrderBy(string $sort, string $dir, array $map, string $default): string
  {
    $dir = strtolower($dir) === 'desc' ? 'DESC' : 'ASC';
    if (isset($map[$sort])) {
      return sprintf($map[$sort], $dir);
    }
    return $default;
  }

  private function fetchMapped(string $sql, array $schema, array $params = []): array
  {
    $rows = $this->querySQL($sql, $params);

    return array_map(function (array $r) use ($schema): array {
      $out = [];
      foreach ($schema as $field => $def) {
        if (is_string($def)) {
          $val = $r[$def] ?? null;
        } elseif (is_callable($def)) {
          $val = $def($r);
        } elseif (is_array($def)) {
          $src = $def['source'] ?? $field;
          $val = $r[$src] ?? ($def['default'] ?? null);
          if (($def['trim'] ?? false) && is_string($val)) {
            $val = trim($val);
          }
          if (isset($def['cast'])) {
            if ($def['cast'] === 'int')
              $val = (int) $val;
            if ($def['cast'] === 'string')
              $val = (string) $val;
          }
        } else {
          $val = null;
        }
        $out[$field] = $val;
      }
      return $out;
    }, $rows);
  }

  private function querySQL(string $sql, array $params = []): array
  {
    $db = $this->dbConnect();

    if ($params) {
      $stmt = $db->prepare($sql);
      $stmt->execute($params);
    } else {
      $stmt = $db->query($sql);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  private function dbConnect(): PDO
  {
    if ($this->connection instanceof PDO) {
      return $this->connection;
    }
    try {
      $this->connection = new PDO($this->source, $this->dbUser, $this->dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ]);
      if (!($this->connection instanceof PDO)) {
        throw new DatabaseConnectionException('Verbindungsfehler zur Datenbank');
      }
      return $this->connection;
    } catch (PDOException $e) {
      error_log('[DB] Verbindung fehlgeschlagen: ' . $e->getMessage());
      throw new DatabaseConnectionException('Verbindungsfehler zur Datenbank');
    }
  }
}