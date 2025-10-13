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
  public function getSubjectsPaginated(
    int $page,
    int $perPage,
    string $sort = 'fach',
    string $dir = 'asc',
    string $q = '',
    array $fields = []
  ): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $db = $this->dbConnect();

    // WHERE mit eindeutigen Param-Namen
    $where = '';
    $params = [];
    $allowed = ['fach', 'lehrer'];
    if ($q !== '') {
      $fields = array_values(array_intersect($fields ?: $allowed, $allowed));
      $parts = [];
      if (in_array('fach', $fields, true)) {
        $parts[] = 'f.fach LIKE :q_fach';
        $params[':q_fach'] = '%' . $q . '%';
      }
      if (in_array('lehrer', $fields, true)) {
        // EXISTS korrekt verwenden (mit eindeutigen Param-Namen)
        $parts[] =
          "EXISTS (
             SELECT 1
             FROM lehrer_fach lf2
             JOIN lehrer l2 ON l2.id = lf2.lehrer_id
             JOIN users u2  ON u2.id = l2.user_id
             WHERE lf2.fach_id = f.id
               AND (
                 u2.vorname LIKE :q_lehrer_vn OR
                 u2.nachname LIKE :q_lehrer_nn OR
                 CONCAT(u2.vorname,' ',u2.nachname) LIKE :q_lehrer_full
               )
           )";
        $params[':q_lehrer_vn'] = '%' . $q . '%';
        $params[':q_lehrer_nn'] = '%' . $q . '%';
        $params[':q_lehrer_full'] = '%' . $q . '%';
      }
      if ($parts) {
        $where = 'WHERE (' . implode(' OR ', $parts) . ')';
      }
    }

    $totalSql = "SELECT COUNT(*) FROM faecher f $where";
    $stmtTotal = $db->prepare($totalSql);
    foreach ($params as $k => $v) {
      $stmtTotal->bindValue($k, $v, PDO::PARAM_STR);
    }
    $stmtTotal->execute();
    $total = (int) $stmtTotal->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
      $page = $pages;
    }
    $offset = ($page - 1) * $perPage;

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'fach' => 'f.fach %s',
      // 'lehrer' absichtlich nicht sortierbar
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
    $where
    GROUP BY f.id, f.fach
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $k => $v) {
      $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
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

  public function getClassesPaginated(
    int $page,
    int $perPage,
    string $sort = 'klasse',
    string $dir = 'asc',
    string $q = '',
    array $fields = []
  ): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $db = $this->dbConnect();

    // WHERE: Suche in Klasse und Klassenlehrer(n)
    $where = '';
    $params = [];
    $allowed = ['klasse', 'klassenlehrer'];
    if ($q !== '') {
      $fields = array_values(array_intersect($fields ?: $allowed, $allowed));
      $parts = [];
      if (in_array('klasse', $fields, true)) {
        $parts[] = 'k.klasse LIKE :q_klasse';
        $params[':q_klasse'] = '%' . $q . '%';
      }
      if (in_array('klassenlehrer', $fields, true)) {
        $parts[] =
          "EXISTS (
             SELECT 1
             FROM klassen_lehrer kl2
             JOIN lehrer l2 ON l2.id = kl2.lehrer_id
             JOIN users u2  ON u2.id = l2.user_id
             WHERE kl2.klasse_id = k.id
               AND (
                 u2.vorname LIKE :q_kl_vn OR
                 u2.nachname LIKE :q_kl_nn OR
                 CONCAT(u2.vorname,' ',u2.nachname) LIKE :q_kl_full
               )
           )";
        $params[':q_kl_vn'] = '%' . $q . '%';
        $params[':q_kl_nn'] = '%' . $q . '%';
        $params[':q_kl_full'] = '%' . $q . '%';
      }
      if ($parts) {
        $where = 'WHERE (' . implode(' OR ', $parts) . ')';
      }
    }

    // Total (gefiltert)
    $totalSql = "SELECT COUNT(DISTINCT k.id) FROM klassen k $where";
    $stmtTotal = $db->prepare($totalSql);
    foreach ($params as $kParam => $vParam) {
      $stmtTotal->bindValue($kParam, $vParam, PDO::PARAM_STR);
    }
    $stmtTotal->execute();
    $total = (int) $stmtTotal->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages)
      $page = $pages;
    $offset = ($page - 1) * $perPage;

    // Aggregat nicht sortierbar
    $orderBy = $this->buildOrderBy($sort, $dir, [
      'klasse' => 'k.klasse %s',
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
    $where
    GROUP BY k.id, k.klasse
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $kParam => $vParam) {
      $stmt->bindValue($kParam, $vParam, PDO::PARAM_STR);
    }
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

  public function getTeachersPaginated(
    int $page,
    int $perPage,
    string $sort = 'nachname',
    string $dir = 'asc',
    string $q = '',
    array $fields = []
  ): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));

    $db = $this->dbConnect();

    // WHERE mit eindeutigen Param-Namen
    $where = '';
    $params = [];
    $allowed = ['vorname', 'nachname', 'faecher'];
    if ($q !== '') {
      $fields = array_values(array_intersect($fields ?: $allowed, $allowed));
      $parts = [];
      if (in_array('vorname', $fields, true)) {
        $parts[] = 'u.vorname LIKE :q_vorname';
        $params[':q_vorname'] = '%' . $q . '%';
      }
      if (in_array('nachname', $fields, true)) {
        $parts[] = 'u.nachname LIKE :q_nachname';
        $params[':q_nachname'] = '%' . $q . '%';
      }
      if (in_array('faecher', $fields, true)) {
        $parts[] =
          'EXISTS (SELECT 1 FROM lehrer_fach lf2 JOIN faecher f2 ON f2.id = lf2.fach_id
                 WHERE lf2.lehrer_id = l.id AND f2.fach LIKE :q_fach)';
        $params[':q_fach'] = '%' . $q . '%';
      }
      if ($parts) {
        $where = 'WHERE (' . implode(' OR ', $parts) . ')';
      }
    }

    // Total gefiltert
    $totalSql = "SELECT COUNT(DISTINCT l.id)
               FROM lehrer l
               JOIN users u ON u.id = l.user_id
               $where";
    $stmtTotal = $db->prepare($totalSql);
    foreach ($params as $k => $v) {
      $stmtTotal->bindValue($k, $v, PDO::PARAM_STR);
    }
    $stmtTotal->execute();
    $total = (int) $stmtTotal->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) {
      $page = $pages;
    }
    $offset = ($page - 1) * $perPage;

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'vorname' => 'u.vorname %s, u.nachname ASC',
      'nachname' => 'u.nachname %s, u.vorname ASC',
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
    $where
    GROUP BY u.id, u.vorname, u.nachname
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $k => $v) {
      $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
      'items' => array_map(static fn(array $r): array => [
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

  public function getLearnersPaginated(
    int $page,
    int $perPage,
    string $sort = 'nachname',
    string $dir = 'asc',
    string $q = '',
    array $fields = []
  ): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $db = $this->dbConnect();

    // WHERE: Vorname/Nachname/Klasse
    $where = '';
    $params = [];
    $allowed = ['vorname', 'nachname', 'klasse'];
    if ($q !== '') {
      $fields = array_values(array_intersect($fields ?: $allowed, $allowed));
      $parts = [];
      if (in_array('vorname', $fields, true)) {
        $parts[] = 'u.vorname LIKE :q_vn';
        $params[':q_vn'] = '%' . $q . '%';
      }
      if (in_array('nachname', $fields, true)) {
        $parts[] = 'u.nachname LIKE :q_nn';
        $params[':q_nn'] = '%' . $q . '%';
      }
      if (in_array('klasse', $fields, true)) {
        $parts[] = 'k.klasse LIKE :q_klasse';
        $params[':q_klasse'] = '%' . $q . '%';
      }
      if ($parts) {
        $where = 'WHERE (' . implode(' OR ', $parts) . ')';
      }
    }

    // Total gefiltert
    $totalSql = "SELECT COUNT(*) 
                 FROM schueler s
                 JOIN users u ON s.user_id = u.id
                 LEFT JOIN klassen k ON s.klasse_id = k.id
                 $where";
    $stmtTotal = $db->prepare($totalSql);
    foreach ($params as $kParam => $vParam) {
      $stmtTotal->bindValue($kParam, $vParam, PDO::PARAM_STR);
    }
    $stmtTotal->execute();
    $total = (int) $stmtTotal->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages)
      $page = $pages;
    $offset = ($page - 1) * $perPage;

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'vorname' => 'u.vorname %s, u.nachname ASC',
      'nachname' => 'u.nachname %s, u.vorname ASC',
      'klasse' => 'k.klasse %s, u.nachname ASC, u.vorname ASC',
    ], 'u.nachname ASC, u.vorname ASC');

    $sql = "SELECT u.id, u.vorname, u.nachname, k.klasse AS klasse
            FROM schueler s
            JOIN users u      ON s.user_id = u.id
            LEFT JOIN klassen k ON s.klasse_id = k.id
            $where
            ORDER BY $orderBy
            LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $kParam => $vParam) {
      $stmt->bindValue($kParam, $vParam, PDO::PARAM_STR);
    }
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

  public function getOfficesPaginated(
    int $page,
    int $perPage,
    string $sort = 'nachname',
    string $dir = 'asc',
    string $q = '',
    array $fields = []
  ): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));

    $db = $this->dbConnect();

    // WHERE: Vorname/Nachname/Email
    $where = '';
    $params = [];
    $allowed = ['vorname', 'nachname', 'email'];
    if ($q !== '') {
      $fields = array_values(array_intersect($fields ?: $allowed, $allowed));
      $parts = [];
      if (in_array('vorname', $fields, true)) {
        $parts[] = 'u.vorname LIKE :q_vn';
        $params[':q_vn'] = '%' . $q . '%';
      }
      if (in_array('nachname', $fields, true)) {
        $parts[] = 'u.nachname LIKE :q_nn';
        $params[':q_nn'] = '%' . $q . '%';
      }
      if (in_array('email', $fields, true)) {
        $parts[] = 'u.email LIKE :q_email';
        $params[':q_email'] = '%' . $q . '%';
      }
      if ($parts) {
        $where = 'WHERE (' . implode(' OR ', $parts) . ')';
      }
    }

    // Total gefiltert
    $totalSql = "SELECT COUNT(*) 
                 FROM verwaltung v
                 JOIN users u ON u.id = v.user_id
                 $where";
    $stmtTotal = $db->prepare($totalSql);
    foreach ($params as $kParam => $vParam) {
      $stmtTotal->bindValue($kParam, $vParam, PDO::PARAM_STR);
    }
    $stmtTotal->execute();
    $total = (int) $stmtTotal->fetchColumn();

    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages)
      $page = $pages;
    $offset = ($page - 1) * $perPage;

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'vorname' => 'u.vorname %s, u.nachname ASC',
      'nachname' => 'u.nachname %s, u.vorname ASC',
      'email' => 'u.email %s, u.nachname ASC',
    ], 'u.nachname ASC, u.vorname ASC');

    $sql = "SELECT u.id, u.vorname, u.nachname, u.email
            FROM verwaltung v
            JOIN users u ON u.id = v.user_id
            $where
            ORDER BY $orderBy
            LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $kParam => $vParam) {
      $stmt->bindValue($kParam, $vParam, PDO::PARAM_STR);
    }
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
    return isset($map[$sort]) ? sprintf($map[$sort], $dir) : $default;
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