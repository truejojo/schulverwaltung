<?php

declare(strict_types=1);

class DatabaseConnectionException extends RuntimeException {}

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
    array $fields = [],
    bool $matchAll = false
  ): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $db = $this->dbConnect();

    $where = '';
    $params = [];
    $allowed = ['fach', 'lehrer'];

    if ($q !== '') {
      $selectedFields = array_values(array_intersect($fields ?: $allowed, $allowed));
      if (empty($selectedFields))
        $selectedFields = $allowed;

      $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      $tokenGroups = [];
      foreach ($tokens as $ti => $token) {
        $or = [];
        if (in_array('fach', $selectedFields, true)) {
          $or[] = "f.fach LIKE :t{$ti}_fach";
          $params[":t{$ti}_fach"] = '%' . $token . '%';
        }
        if (in_array('lehrer', $selectedFields, true)) {
          $or[] =
            "EXISTS (
             SELECT 1
             FROM lehrer_fach lf2
             JOIN lehrer l2 ON l2.id = lf2.lehrer_id
             JOIN users u2  ON u2.id = l2.user_id
             WHERE lf2.fach_id = f.id
               AND (
                 u2.vorname LIKE :t{$ti}_lvn OR
                 u2.nachname LIKE :t{$ti}_lnn OR
                 CONCAT(u2.vorname,' ',u2.nachname) LIKE :t{$ti}_lfull
               )
           )";
          $params[":t{$ti}_lvn"] = '%' . $token . '%';
          $params[":t{$ti}_lnn"] = '%' . $token . '%';
          $params[":t{$ti}_lfull"] = '%' . $token . '%';
        }
        if (!empty($or))
          $tokenGroups[] = '(' . implode(' OR ', $or) . ')';
      }
      if (!empty($tokenGroups)) {
        $where = 'WHERE ' . implode($matchAll ? ' AND ' : ' OR ', $tokenGroups);
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
    if ($page > $pages)
      $page = $pages;
    $offset = ($page - 1) * $perPage;

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'fach' => 'f.fach %s',
    ], 'f.fach ASC');

    $sql = "SELECT
      f.id,
      f.fach,
      COALESCE(
        GROUP_CONCAT(
          DISTINCT CONCAT(u.vorname, ' ', u.nachname)
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
    array $fields = [],
    bool $matchAll = false
  ): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $db = $this->dbConnect();

    $where = '';
    $params = [];
    $allowed = ['klasse', 'klassenlehrer'];

    if ($q !== '') {
      $selectedFields = array_values(array_intersect($fields ?: $allowed, $allowed));
      if (empty($selectedFields))
        $selectedFields = $allowed;

      $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      $tokenGroups = [];
      foreach ($tokens as $ti => $token) {
        $or = [];
        if (in_array('klasse', $selectedFields, true)) {
          $or[] = "k.klasse LIKE :t{$ti}_klasse";
          $params[":t{$ti}_klasse"] = '%' . $token . '%';
        }
        if (in_array('klassenlehrer', $selectedFields, true)) {
          $or[] =
            "EXISTS (
             SELECT 1
             FROM klassen_lehrer kl2
             JOIN lehrer l2 ON l2.id = kl2.lehrer_id
             JOIN users u2  ON u2.id = l2.user_id
             WHERE kl2.klasse_id = k.id
               AND (
                 u2.vorname LIKE :t{$ti}_kl_vn OR
                 u2.nachname LIKE :t{$ti}_kl_nn OR
                 CONCAT(u2.vorname,' ',u2.nachname) LIKE :t{$ti}_kl_full
               )
           )";
          $params[":t{$ti}_kl_vn"] = '%' . $token . '%';
          $params[":t{$ti}_kl_nn"] = '%' . $token . '%';
          $params[":t{$ti}_kl_full"] = '%' . $token . '%';
        }
        if (!empty($or))
          $tokenGroups[] = '(' . implode(' OR ', $or) . ')';
      }
      if (!empty($tokenGroups)) {
        $where = 'WHERE ' . implode($matchAll ? ' AND ' : ' OR ', $tokenGroups);
      }
    }

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

    $orderBy = $this->buildOrderBy($sort, $dir, [
      'klasse' => 'k.klasse %s',
    ], 'k.klasse ASC');

    $sql = "SELECT
      k.id,
      k.klasse,
      COALESCE(
        GROUP_CONCAT(
          DISTINCT CONCAT(u.vorname, ' ', u.nachname)
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
    array $fields = [],
    bool $matchAll = false
  ): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $db = $this->dbConnect();

    $where = '';
    $params = [];
    $allowed = ['vorname', 'nachname', 'faecher'];

    if ($q !== '') {
      $selectedFields = array_values(array_intersect($fields ?: $allowed, $allowed));
      if (empty($selectedFields))
        $selectedFields = $allowed;

      $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      $tokenGroups = [];
      foreach ($tokens as $ti => $token) {
        $or = [];
        if (in_array('vorname', $selectedFields, true)) {
          $or[] = "u.vorname LIKE :t{$ti}_vn";
          $params[":t{$ti}_vn"] = '%' . $token . '%';
        }
        if (in_array('nachname', $selectedFields, true)) {
          $or[] = "u.nachname LIKE :t{$ti}_nn";
          $params[":t{$ti}_nn"] = '%' . $token . '%';
        }
        if (in_array('faecher', $selectedFields, true)) {
          $or[] =
            "EXISTS (
             SELECT 1
             FROM lehrer_fach lf2
             JOIN faecher f2 ON f2.id = lf2.fach_id
             WHERE lf2.lehrer_id = l.id
               AND f2.fach LIKE :t{$ti}_fach
           )";
          $params[":t{$ti}_fach"] = '%' . $token . '%';
        }
        if (!empty($or))
          $tokenGroups[] = '(' . implode(' OR ', $or) . ')';
      }
      if (!empty($tokenGroups)) {
        $where = 'WHERE ' . implode($matchAll ? ' AND ' : ' OR ', $tokenGroups);
      }
    }

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
    if ($page > $pages)
      $page = $pages;
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
    array $fields = [],
    bool $matchAll = false
  ): array {
    $page = max(1, $page);
    $perPage = max(1, min(100, $perPage));
    $db = $this->dbConnect();

    $where = '';
    $params = [];
    $allowed = ['vorname', 'nachname', 'klasse'];

    if ($q !== '') {
      // Felder: wenn nichts gewählt → alle erlaubten
      $selectedFields = array_values(array_intersect($fields ?: $allowed, $allowed));
      if (empty($selectedFields)) {
        $selectedFields = $allowed;
      }

      // Tokens (whitespace-separiert)
      $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      $tokenGroups = [];

      foreach ($tokens as $ti => $token) {
        $or = [];
        if (in_array('vorname', $selectedFields, true)) {
          $or[] = "u.vorname LIKE :t{$ti}_vn";
          $params[":t{$ti}_vn"] = '%' . $token . '%';
        }
        if (in_array('nachname', $selectedFields, true)) {
          $or[] = "u.nachname LIKE :t{$ti}_nn";
          $params[":t{$ti}_nn"] = '%' . $token . '%';
        }
        if (in_array('klasse', $selectedFields, true)) {
          $or[] = "k.klasse LIKE :t{$ti}_kl";
          $params[":t{$ti}_kl"] = '%' . $token . '%';
        }
        if (!empty($or)) {
          $tokenGroups[] = '(' . implode(' OR ', $or) . ')';
        }
      }

      if (!empty($tokenGroups)) {
        $where = 'WHERE ' . implode($matchAll ? ' AND ' : ' OR ', $tokenGroups);
      }
    }

    // Total (gefiltert)
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
          JOIN users u        ON s.user_id = u.id
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

  // public function getOfficesPaginated(
  //   int $page,
  //   int $perPage,
  //   string $sort = 'nachname',
  //   string $dir = 'asc',
  //   string $q = '',
  //   array $fields = [],
  //   bool $matchAll = false
  // ): array {
  //   $page = max(1, $page);
  //   $perPage = max(1, min(100, $perPage));
  //   $db = $this->dbConnect();

  //   $where = '';
  //   $params = [];
  //   $allowed = ['vorname', 'nachname', 'email'];

  //   if ($q !== '') {
  //     $selectedFields = array_values(array_intersect($fields ?: $allowed, $allowed));
  //     if (empty($selectedFields))
  //       $selectedFields = $allowed;

  //     $tokens = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY) ?: [];
  //     $tokenGroups = [];
  //     foreach ($tokens as $ti => $token) {
  //       $or = [];
  //       if (in_array('vorname', $selectedFields, true)) {
  //         $or[] = "u.vorname LIKE :t{$ti}_vn";
  //         $params[":t{$ti}_vn"] = '%' . $token . '%';
  //       }
  //       if (in_array('nachname', $selectedFields, true)) {
  //         $or[] = "u.nachname LIKE :t{$ti}_nn";
  //         $params[":t{$ti}_nn"] = '%' . $token . '%';
  //       }
  //       if (in_array('email', $selectedFields, true)) {
  //         $or[] = "u.email LIKE :t{$ti}_em";
  //         $params[":t{$ti}_em"] = '%' . $token . '%';
  //       }
  //       if (!empty($or))
  //         $tokenGroups[] = '(' . implode(' OR ', $or) . ')';
  //     }
  //     if (!empty($tokenGroups)) {
  //       $where = 'WHERE ' . implode($matchAll ? ' AND ' : ' OR ', $tokenGroups);
  //     }
  //   }

  //   $totalSql = "SELECT COUNT(*)
  //              FROM verwaltung v
  //              JOIN users u ON u.id = v.user_id
  //              $where";
  //   $stmtTotal = $db->prepare($totalSql);
  //   foreach ($params as $k => $v) {
  //     $stmtTotal->bindValue($k, $v, PDO::PARAM_STR);
  //   }
  //   $stmtTotal->execute();
  //   $total = (int) $stmtTotal->fetchColumn();

  //   $pages = max(1, (int) ceil($total / $perPage));
  //   if ($page > $pages)
  //     $page = $pages;
  //   $offset = ($page - 1) * $perPage;

  //   $orderBy = $this->buildOrderBy($sort, $dir, [
  //     'vorname' => 'u.vorname %s, u.nachname ASC',
  //     'nachname' => 'u.nachname %s, u.vorname ASC',
  //     'email' => 'u.email %s, u.nachname ASC',
  //   ], 'u.nachname ASC, u.vorname ASC');

  //   $sql = "SELECT u.id, u.vorname, u.nachname, u.email
  //         FROM verwaltung v
  //         JOIN users u ON u.id = v.user_id
  //         $where
  //         ORDER BY $orderBy
  //         LIMIT :limit OFFSET :offset";

  //   $stmt = $db->prepare($sql);
  //   foreach ($params as $k => $v) {
  //     $stmt->bindValue($k, $v, PDO::PARAM_STR);
  //   }
  //   $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
  //   $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  //   $stmt->execute();
  //   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //   return [
  //     'items' => array_map(static fn($r) => [
  //       'id' => (int) $r['id'],
  //       'vorname' => trim($r['vorname'] ?? ''),
  //       'nachname' => trim($r['nachname'] ?? ''),
  //       'email' => trim($r['email'] ?? ''),
  //     ], $rows),
  //     'total' => $total,
  //     'page' => $page,
  //     'perPage' => $perPage,
  //     'pages' => $pages,
  //     'hasPrev' => $page > 1,
  //     'hasNext' => $page < $pages,
  //   ];
  // }

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

  // Mutations
  public function deleteSubject(int $id): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      $stmt = $db->prepare('DELETE FROM lehrer_fach WHERE fach_id = :id');
      $stmt->execute([':id' => $id]);

      $stmt = $db->prepare('DELETE FROM faecher WHERE id = :id');
      $stmt->execute([':id' => $id]);

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  public function deleteClass(int $id): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      // Lehrer-Zuordnungen zur Klasse entfernen
      $stmt = $db->prepare('DELETE FROM klassen_lehrer WHERE klasse_id = :id');
      $stmt->execute([':id' => $id]);

      // Schüler aus Klasse lösen (auf NULL setzen, falls Spalte NULL erlaubt)
      try {
        $stmt = $db->prepare('UPDATE schueler SET klasse_id = NULL WHERE klasse_id = :id');
        $stmt->execute([':id' => $id]);
      } catch (Throwable $e) {
        // Falls NOT NULL → ignore oder vorher Schema anpassen
      }

      // Klasse löschen
      $stmt = $db->prepare('DELETE FROM klassen WHERE id = :id');
      $stmt->execute([':id' => $id]);

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  public function deleteTeacher(int $userId): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      // Lehrer-ID ermitteln
      $stmt = $db->prepare('SELECT id FROM lehrer WHERE user_id = :uid LIMIT 1');
      $stmt->execute([':uid' => $userId]);
      $lehrerId = (int)($stmt->fetchColumn() ?: 0);
      if ($lehrerId > 0) {
        // Zuordnungen entfernen
        $stmt = $db->prepare('DELETE FROM lehrer_fach WHERE lehrer_id = :lid');
        $stmt->execute([':lid' => $lehrerId]);

        $stmt = $db->prepare('DELETE FROM klassen_lehrer WHERE lehrer_id = :lid');
        $stmt->execute([':lid' => $lehrerId]);

        // Lehrer-Datensatz löschen
        $stmt = $db->prepare('DELETE FROM lehrer WHERE id = :lid');
        $stmt->execute([':lid' => $lehrerId]);
      }

      // Optional: User selbst NICHT löschen (falls Login erhalten bleiben soll)
      // Wenn doch entfernen:
      // $stmt = $db->prepare('DELETE FROM users WHERE id = :uid');
      // $stmt->execute([':uid' => $userId]);

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  public function deleteLearner(int $userId): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      // Schüler-Datensatz löschen
      $stmt = $db->prepare('DELETE FROM schueler WHERE user_id = :uid');
      $stmt->execute([':uid' => $userId]);

      // Optional: User nicht löschen (analog Lehrer)
      // $stmt = $db->prepare('DELETE FROM users WHERE id = :uid');
      // $stmt->execute([':uid' => $userId]);

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  public function deleteOffice(int $userId): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      // Verwaltungseintrag löschen
      $stmt = $db->prepare('DELETE FROM verwaltung WHERE user_id = :uid');
      $stmt->execute([':uid' => $userId]);

      // Optional: User nicht löschen
      // $stmt = $db->prepare('DELETE FROM users WHERE id = :uid');
      // $stmt->execute([':uid' => $userId]);

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  public function getSubjectById(int $id): ?array
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('SELECT id, fach FROM faecher WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return null;
    return [
      'id' => (int)$row['id'],
      'fach' => trim((string)($row['fach'] ?? '')),
    ];
  }

  public function updateSubject(int $id, string $fach): bool
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('UPDATE faecher SET fach = :fach WHERE id = :id');
    return $stmt->execute([':fach' => $fach, ':id' => $id]);
  }

  public function getTeacherByUserId(int $userId): ?array
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('SELECT u.id, u.vorname, u.nachname
                              FROM users u
                              INNER JOIN lehrer l ON l.user_id = u.id
                              WHERE u.id = :uid
                              LIMIT 1');
    $stmt->execute([':uid' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? ['id' => (int)$row['id'], 'vorname' => (string)$row['vorname'], 'nachname' => (string)$row['nachname']] : null;
  }

  public function updateTeacher(int $userId, string $vorname, string $nachname): bool
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('UPDATE users SET vorname = :vn, nachname = :nn WHERE id = :uid');
    return $stmt->execute([':vn' => $vorname, ':nn' => $nachname, ':uid' => $userId]);
  }

  // public function getOfficeByUserId(int $userId): ?array
  // {
  //   $db = $this->dbConnect();
  //   $stmt = $db->prepare('SELECT u.id, u.vorname, u.nachname, u.email
  //                             FROM users u
  //                             INNER JOIN verwaltung v ON v.user_id = u.id
  //                             WHERE u.id = :uid
  //                             LIMIT 1');
  //   $stmt->execute([':uid' => $userId]);
  //   $row = $stmt->fetch(PDO::FETCH_ASSOC);
  //   return $row ? ['id' => (int)$row['id'], 'vorname' => (string)$row['vorname'], 'nachname' => (string)$row['nachname'], 'email' => (string)$row['email']] : null;
  // }

  // public function updateOffice(int $userId, string $vorname, string $nachname, string $email): bool
  // {
  //   $db = $this->dbConnect();
  //   $stmt = $db->prepare('UPDATE users SET vorname = :vn, nachname = :nn, email = :em WHERE id = :uid');
  //   return $stmt->execute([':vn' => $vorname, ':nn' => $nachname, ':em' => $email, ':uid' => $userId]);
  // }

  public function getLearnerByUserId(int $userId): ?array
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('SELECT u.id, u.vorname, u.nachname, s.klasse_id
                              FROM users u
                              INNER JOIN schueler s ON s.user_id = u.id
                              WHERE u.id = :uid
                              LIMIT 1');
    $stmt->execute([':uid' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? [
      'id' => (int)$row['id'],
      'vorname' => (string)$row['vorname'],
      'nachname' => (string)$row['nachname'],
      'klasse_id' => isset($row['klasse_id']) ? (int)$row['klasse_id'] : null,
    ] : null;
  }

  public function updateLearner(int $userId, string $vorname, string $nachname, int $klasseId): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      $stmt = $db->prepare('UPDATE users SET vorname = :vn, nachname = :nn WHERE id = :uid');
      $stmt->execute([':vn' => $vorname, ':nn' => $nachname, ':uid' => $userId]);

      $stmt = $db->prepare('UPDATE schueler SET klasse_id = :kid WHERE user_id = :uid');
      $stmt->execute([':kid' => $klasseId, ':uid' => $userId]);

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  public function getClassById(int $id): ?array
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('SELECT id, klasse FROM klassen WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? ['id' => (int)$row['id'], 'klasse' => (string)$row['klasse']] : null;
  }

  public function updateClass(int $id, string $klasse): bool
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('UPDATE klassen SET klasse = :klasse WHERE id = :id');
    return $stmt->execute([':klasse' => $klasse, ':id' => $id]);
  }

  public function getAllClasses(): array
  {
    $db = $this->dbConnect();
    $stmt = $db->query('SELECT id, klasse FROM klassen ORDER BY klasse ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  // Subjects
  public function getAllSubjects(): array
  {
    $db = $this->dbConnect();
    $stmt = $db->query('SELECT id, fach FROM faecher ORDER BY fach ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function getTeacherSubjectIds(int $userId): array
  {
    $db = $this->dbConnect();
    // lehrer_id ermitteln
    $stmt = $db->prepare('SELECT id FROM lehrer WHERE user_id = :uid LIMIT 1');
    $stmt->execute([':uid' => $userId]);
    $lehrerId = (int)($stmt->fetchColumn() ?: 0);
    if ($lehrerId <= 0) return [];
    $stmt = $db->prepare('SELECT fach_id FROM lehrer_fach WHERE lehrer_id = :lid');
    $stmt->execute([':lid' => $lehrerId]);
    return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], 'fach_id'));
  }

  public function updateTeacherSubjects(int $userId, array $subjectIds): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      // lehrer_id
      $stmt = $db->prepare('SELECT id FROM lehrer WHERE user_id = :uid LIMIT 1');
      $stmt->execute([':uid' => $userId]);
      $lehrerId = (int)($stmt->fetchColumn() ?: 0);
      if ($lehrerId <= 0) throw new RuntimeException('Lehrer nicht gefunden');

      // Bestehende löschen
      $stmt = $db->prepare('DELETE FROM lehrer_fach WHERE lehrer_id = :lid');
      $stmt->execute([':lid' => $lehrerId]);

      // Neue Zuordnungen
      if (!empty($subjectIds)) {
        $stmt = $db->prepare('INSERT INTO lehrer_fach (lehrer_id, fach_id) VALUES (:lid, :fid)');
        foreach ($subjectIds as $sid) {
          $sid = (int)$sid;
          if ($sid > 0) $stmt->execute([':lid' => $lehrerId, ':fid' => $sid]);
        }
      }

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  // Verwaltungsrollen
  // public function getAllVerwaltungsRollen(): array
  // {
  //   $db = $this->dbConnect();
  //   $stmt = $db->query('SELECT * FROM verwaltungs_rollen ORDER BY id ASC');
  //   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
  //   // Label-Feld tolerant bestimmen
  //   foreach ($rows as &$r) {
  //     $label = $r['bezeichnung'] ?? ($r['name'] ?? ($r['rolle'] ?? null));
  //     $r['label'] = $label ?? ('Rolle #' . (int)$r['id']);
  //   }
  //   return $rows;
  // }

  // public function getVerwaltungsrollenMap(): array
  // {
  //   $map = [];
  //   foreach ($this->getAllVerwaltungsRollen() as $r) {
  //     $map[(int)$r['id']] = (string)$r['label'];
  //   }
  //   return $map;
  // }

  // Offices
  public function getOfficeByUserId(int $userId): ?array
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('SELECT u.id, u.vorname, u.nachname, v.verwaltungs_rolle_id
                              FROM users u
                              INNER JOIN verwaltung v ON v.user_id = u.id
                              WHERE u.id = :uid LIMIT 1');
    $stmt->execute([':uid' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return null;
    return [
      'id' => (int)$row['id'],
      'vorname' => (string)$row['vorname'],
      'nachname' => (string)$row['nachname'],
      'verwaltungs_rolle_id' => (int)($row['verwaltungs_rolle_id'] ?? 0),
    ];
  }

  public function updateOffice(int $userId, string $vorname, string $nachname, int $verwaltungsRolleId): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      $stmt = $db->prepare('UPDATE users SET vorname = :vn, nachname = :nn WHERE id = :uid');
      $stmt->execute([':vn' => $vorname, ':nn' => $nachname, ':uid' => $userId]);

      $stmt = $db->prepare('UPDATE verwaltung SET verwaltungs_rolle_id = :rid WHERE user_id = :uid');
      $stmt->execute([':rid' => $verwaltungsRolleId, ':uid' => $userId]);

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  // Classes – Lehrer-Zuordnung
  public function getAllTeachersForAssign(): array
  {
    $db = $this->dbConnect();
    $stmt = $db->query('SELECT l.id, u.vorname, u.nachname
                            FROM lehrer l
                            INNER JOIN users u ON u.id = l.user_id
                            ORDER BY u.nachname, u.vorname');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    return array_map(fn($r) => [
      'id' => (int)$r['id'], // lehrer.id
      'name' => trim(($r['nachname'] ?? '') . ', ' . ($r['vorname'] ?? '')),
    ], $rows);
  }

  public function getTeacherIdsByClassId(int $classId): array
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('SELECT lehrer_id FROM klassen_lehrer WHERE klasse_id = :cid');
    $stmt->execute([':cid' => $classId]);
    return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [], 'lehrer_id'));
  }

  public function updateClassTeachers(int $classId, array $teacherIds): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      $stmt = $db->prepare('DELETE FROM klassen_lehrer WHERE klasse_id = :cid');
      $stmt->execute([':cid' => $classId]);

      if (!empty($teacherIds)) {
        $stmt = $db->prepare('INSERT INTO klassen_lehrer (klasse_id, lehrer_id) VALUES (:cid, :lid)');
        foreach ($teacherIds as $lid) {
          $lid = (int)$lid;
          if ($lid > 0) $stmt->execute([':cid' => $classId, ':lid' => $lid]);
        }
      }

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  public function getAllOfficesWithRoles(): array
  {
    $db = $this->dbConnect();
    $stmt = $db->query(
      'SELECT u.id, u.vorname, u.nachname, v.verwaltungs_rolle_id
         FROM verwaltung v
         INNER JOIN users u ON u.id = v.user_id
         ORDER BY u.nachname, u.vorname'
    );
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    // Rollen-Map holen
    $rollenMap = $this->getVerwaltungsrollenMap();
    foreach ($rows as &$r) {
      $rid = (int)($r['verwaltungs_rolle_id'] ?? 0);
      $r['position'] = $rollenMap[$rid] ?? 'Unbekannt';
    }
    return $rows;
  }

  public function getOfficesPaginated(
    int $page,
    int $perPage,
    ?string $sort,
    string $dir,
    ?string $q,
    array $fields,
    bool $matchAll
  ): array {
    $db = $this->dbConnect();
    $stmt = $db->query('
        SELECT u.id, u.vorname, u.nachname, v.verwaltungs_rolle_id
        FROM verwaltung v
        INNER JOIN users u ON u.id = v.user_id
    ');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $rollenMap = $this->getVerwaltungsrollenMap();
    foreach ($rows as &$r) {
      $rid = (int)($r['verwaltungs_rolle_id'] ?? 0);
      $r['position'] = $rollenMap[$rid] ?? 'Unbekannt';
    }

    $searchFields = [];
    foreach ($fields as $f) {
      $searchFields[] = is_array($f) ? (string)($f['key'] ?? '') : (string)$f;
    }
    $searchFields = array_filter($searchFields) ?: ['vorname', 'nachname', 'position'];

    if ($q) {
      $qLower = mb_strtolower($q);
      $terms = preg_split('/\s+/', $qLower, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      $rows = array_values(array_filter($rows, function ($r) use ($terms, $searchFields, $matchAll) {
        $hay = [];
        foreach ($searchFields as $f) $hay[] = mb_strtolower((string)($r[$f] ?? ''));
        $text = implode(' ', $hay);
        if ($matchAll) {
          foreach ($terms as $t) if (mb_strpos($text, $t) === false) return false;
          return true;
        }
        foreach ($terms as $t) if (mb_strpos($text, $t) !== false) return true;
        return empty($terms);
      }));
    }

    $sort = $sort ?: 'nachname';
    $dir = strtolower($dir) === 'desc' ? 'desc' : 'asc';
    $allowedSort = ['vorname', 'nachname', 'position'];
    if (!in_array($sort, $allowedSort, true)) $sort = 'nachname';

    usort($rows, function ($a, $b) use ($sort, $dir) {
      $va = mb_strtolower((string)($a[$sort] ?? ''));
      $vb = mb_strtolower((string)($b[$sort] ?? ''));
      $cmp = $va <=> $vb;
      return $dir === 'desc' ? -$cmp : $cmp;
    });

    $total = count($rows);
    $perPage = max(1, $perPage);
    $pages = max(1, (int)ceil($total / $perPage));
    $page = max(1, min($page, $pages));
    $offset = ($page - 1) * $perPage;
    $items = array_slice($rows, $offset, $perPage);

    return [
      'items'    => $items,
      'total'    => $total,
      'pages'    => $pages,
      'page'     => $page,
      'perPage'  => $perPage,
      'hasPrev'  => $page > 1,
      'hasNext'  => $page < $pages,
      'prevPage' => $page > 1 ? $page - 1 : null,
      'nextPage' => $page < $pages ? $page + 1 : null,
    ];
  }

  public function getAllVerwaltungsRollen(): array
  {
    $db = $this->dbConnect();
    $stmt = $db->query('SELECT * FROM verwaltungs_rollen ORDER BY id ASC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Mögliche Spaltennamen für die Bezeichnung
    $candidates = [
      'bezeichnung',
      'beschreibung',
      'titel',
      'title',
      'name',
      'rolle',
      'rollenname',
      'positionsname',
      'position',
      'verwaltungsrolle',
      'label'
    ];
    // Fallback-Mapping lt. Vorgabe
    $defaults = [1 => 'Direktor', 2 => 'Sekretariat', 3 => 'Admin', 4 => 'Hausmeister'];

    foreach ($rows as &$r) {
      $label = null;
      foreach ($candidates as $col) {
        if (isset($r[$col]) && trim((string)$r[$col]) !== '') {
          $label = trim((string)$r[$col]);
          break;
        }
      }
      $id = (int)($r['id'] ?? 0);
      if (!$label && isset($defaults[$id])) {
        $label = $defaults[$id];
      }
      $r['label'] = $label ?: ('Rolle #' . $id);
    }
    return $rows;
  }

  public function getVerwaltungsrollenMap(): array
  {
    $map = [];
    foreach ($this->getAllVerwaltungsRollen() as $r) {
      $map[(int)$r['id']] = (string)$r['label'];
    }
    return $map;
  }

  public function createSubject(string $fach): bool
  {
    $fach = trim($fach);
    if ($fach === '') return false;
    $db = $this->dbConnect();
    $stmt = $db->prepare('INSERT INTO faecher (fach) VALUES (:fach)');
    return $stmt->execute([':fach' => $fach]);
  }
  public function createClass(string $klasse): bool
  {
    $klasse = trim($klasse);
    if ($klasse === '') return false;
    $db = $this->dbConnect();
    $stmt = $db->prepare('INSERT INTO klassen (klasse) VALUES (:klasse)');
    return $stmt->execute([':klasse' => $klasse]);
  }

  public function createOffice(string $vorname, string $nachname, int $rolleId): bool
  {
    $vorname = trim($vorname);
    $nachname = trim($nachname);
    if ($vorname === '' || $nachname === '' || $rolleId <= 0) return false;

    $db = $this->dbConnect();
    try {
      $db->beginTransaction();

      // 1) User anlegen (Minimalfelder: vorname, nachname)
      $stmtU = $db->prepare('INSERT INTO users (vorname, nachname) VALUES (:v, :n)');
      $stmtU->execute([':v' => $vorname, ':n' => $nachname]);
      $userId = (int)$db->lastInsertId();

      // 2) Verwaltung-Eintrag mit Rolle verknüpfen
      $stmtV = $db->prepare('INSERT INTO verwaltung (user_id, verwaltungs_rolle_id) VALUES (:uid, :rid)');
      $stmtV->execute([':uid' => $userId, ':rid' => $rolleId]);

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  public function getUsersPaginated(
    int $page,
    int $perPage,
    ?string $sort,
    string $dir,
    ?string $q,
    array $fields,
    bool $matchAll
  ): array {
    $db = $this->dbConnect();

    $stmt = $db->query(
      'SELECT u.id,
                u.email,
                u.password_hash,
                u.vorname,
                u.nachname,
                u.adresse,
                u.plz,
                u.telefon,
                u.geburtstag,
                u.role_id,
                r.status AS rolle,
                u.created,
                u.updated
         FROM users u
         LEFT JOIN roles r ON r.id = u.role_id'
    );
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Felder bestimmen
    $searchFields = [];
    foreach ($fields as $f) {
      $searchFields[] = is_array($f) ? (string)($f['key'] ?? '') : (string)$f;
    }
    $searchFields = array_filter($searchFields) ?: ['vorname', 'nachname', 'email', 'rolle'];

    // Suche
    if ($q) {
      $qLower = function_exists('mb_strtolower') ? mb_strtolower($q) : strtolower($q);
      $terms = preg_split('/\s+/', $qLower, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      $rows = array_values(array_filter($rows, function ($r) use ($terms, $searchFields, $matchAll) {
        $toLower = function (string $s): string {
          return function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
        };
        $hayParts = [];
        foreach ($searchFields as $f) {
          $hayParts[] = $toLower((string)($r[$f] ?? ''));
        }
        $hay = implode(' ', $hayParts);
        if ($matchAll) {
          foreach ($terms as $t) {
            if (strpos($hay, $t) === false) return false;
          }
          return true;
        }
        foreach ($terms as $t) {
          if (strpos($hay, $t) !== false) return true;
        }
        return empty($terms);
      }));
    }

    // Sortierung
    $sort = $sort ?: 'nachname';
    $dir = strtolower($dir) === 'desc' ? 'desc' : 'asc';
    $allowedSort = ['vorname', 'nachname', 'email', 'rolle'];
    if (!in_array($sort, $allowedSort, true)) $sort = 'nachname';

    usort($rows, function ($a, $b) use ($sort, $dir) {
      $toLower = function (string $s): string {
        return function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
      };
      $va = $toLower((string)($a[$sort] ?? ''));
      $vb = $toLower((string)($b[$sort] ?? ''));
      $cmp = $va <=> $vb;
      return $dir === 'desc' ? -$cmp : $cmp;
    });

    // Pagination
    $total = count($rows);
    $perPage = max(1, $perPage);
    $pages = max(1, (int)ceil($total / $perPage));
    $page = max(1, min($page, $pages));
    $offset = ($page - 1) * $perPage;
    $items = array_slice($rows, $offset, $perPage);

    return [
      'items'    => $items,
      'total'    => $total,
      'pages'    => $pages,
      'page'     => $page,
      'perPage'  => $perPage,
      'hasPrev'  => $page > 1,
      'hasNext'  => $page < $pages,
      'prevPage' => $page > 1 ? $page - 1 : null,
      'nextPage' => $page < $pages ? $page + 1 : null,
    ];
  }
  public function createUser(array $data): bool
  {
    $db = $this->dbConnect();

    $email    = trim((string)($data['email'] ?? ''));
    $password = (string)($data['password'] ?? '');
    $vorname  = trim((string)($data['vorname'] ?? ''));
    $nachname = trim((string)($data['nachname'] ?? ''));

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    if ($password === '' || strlen($password) < 6) return false;
    if ($vorname === '' || $nachname === '') return false;

    $adresse    = (string)($data['adresse'] ?? '');
    $plz        = (string)($data['plz'] ?? '');
    $telefon    = (string)($data['telefon'] ?? '');
    $geburtstag = (string)($data['geburtstag'] ?? '');

    if ($geburtstag !== '') {
      $dt = DateTime::createFromFormat('Y-m-d', $geburtstag);
      $errs = DateTime::getLastErrors();
      if (!$dt || !empty($errs['warning_count']) || !empty($errs['error_count'])) {
        $geburtstag = '';
      } else {
        $geburtstag = $dt->format('Y-m-d');
      }
    }

    $role_id        = ($data['role_id'] ?? '') !== '' ? (int)$data['role_id'] : null;
    $klasse_id      = (int)($data['klasse_id'] ?? 0);
    $verwaltungsRid = (int)($data['verwaltungs_rolle_id'] ?? 0);

    $pwdHash = password_hash($password, PASSWORD_DEFAULT);

    $fields = ['email', 'password_hash', 'vorname', 'nachname'];
    $params = [
      ':email' => $email,
      ':password_hash' => $pwdHash,
      ':vorname' => $vorname,
      ':nachname' => $nachname,
    ];
    if ($adresse !== '') {
      $fields[] = 'adresse';
      $params[':adresse'] = $adresse;
    }
    if ($plz !== '') {
      $fields[] = 'plz';
      $params[':plz'] = $plz;
    }
    if ($telefon !== '') {
      $fields[] = 'telefon';
      $params[':telefon'] = $telefon;
    }
    if ($geburtstag !== '') {
      $fields[] = 'geburtstag';
      $params[':geburtstag'] = $geburtstag;
    }
    if ($role_id !== null) {
      $fields[] = 'role_id';
      $params[':role_id'] = $role_id;
    }

    $placeholders = ':' . implode(',:', $fields);
    $sql = 'INSERT INTO users (' . implode(',', $fields) . ') VALUES (' . $placeholders . ')';

    try {
      $db->beginTransaction();

      // User
      $stmt = $db->prepare($sql);
      $stmt->execute($params);
      $userId = (int)$db->lastInsertId();
      if ($userId <= 0) throw new RuntimeException('User insert failed');

      // Zuordnungen je Rolle
      if ($role_id === 1 && $klasse_id > 0) {
        $stmtL = $db->prepare('INSERT INTO schueler (user_id, klasse_id) VALUES (:uid, :kid)');
        $stmtL->execute([':uid' => $userId, ':kid' => $klasse_id]);
      } elseif ($role_id === 2) {
        $stmtT = $db->prepare('INSERT INTO lehrer (user_id) VALUES (:uid)');
        $stmtT->execute([':uid' => $userId]);
      } elseif ($role_id === 3) {
        if ($verwaltungsRid > 0) {
          $stmtV = $db->prepare('INSERT INTO verwaltung (user_id, verwaltungs_rolle_id) VALUES (:uid, :rid)');
          $stmtV->execute([':uid' => $userId, ':rid' => $verwaltungsRid]);
        } else {
          $stmtV = $db->prepare('INSERT INTO verwaltung (user_id) VALUES (:uid)');
          $stmtV->execute([':uid' => $userId]);
        }
      }

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      return false;
    }
  }

  // Get, Update, Delete User
  public function getUserById(int $id): ?array
  {
    $db = $this->dbConnect();
    $stmt = $db->prepare('
    SELECT u.id, u.email, u.vorname, u.nachname, u.adresse, u.plz, u.telefon, u.geburtstag, u.role_id
    FROM users u
    WHERE u.id = :id
    LIMIT 1
  ');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return null;
    return [
      'id' => (int)$row['id'],
      'email' => (string)($row['email'] ?? ''),
      'vorname' => (string)($row['vorname'] ?? ''),
      'nachname' => (string)($row['nachname'] ?? ''),
      'adresse' => (string)($row['adresse'] ?? ''),
      'plz' => (string)($row['plz'] ?? ''),
      'telefon' => (string)($row['telefon'] ?? ''),
      'geburtstag' => (string)($row['geburtstag'] ?? ''),
      'role_id' => isset($row['role_id']) ? (int)$row['role_id'] : null,
    ];
  }

  public function updateUser(int $id, array $data): bool
  {
    $db = $this->dbConnect();

    $email    = trim((string)($data['email'] ?? ''));
    $vorname  = trim((string)($data['vorname'] ?? ''));
    $nachname = trim((string)($data['nachname'] ?? ''));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    if ($vorname === '' || $nachname === '') return false;

    $adresse    = (string)($data['adresse'] ?? '');
    $plz        = (string)($data['plz'] ?? '');
    $telefon    = (string)($data['telefon'] ?? '');
    $geburtstag = (string)($data['geburtstag'] ?? '');
    if ($geburtstag !== '') {
      $dt = DateTime::createFromFormat('Y-m-d', $geburtstag);
      $errs = DateTime::getLastErrors();
      if (!$dt || !empty($errs['warning_count']) || !empty($errs['error_count'])) {
        $geburtstag = '';
      } else {
        $geburtstag = $dt->format('Y-m-d');
      }
    }

    $fields = ['email = :email', 'vorname = :vorname', 'nachname = :nachname'];
    $params = [':email' => $email, ':vorname' => $vorname, ':nachname' => $nachname, ':id' => $id];

    $fields[] = 'adresse = :adresse';
    $params[':adresse'] = $adresse;
    $fields[] = 'plz = :plz';
    $params[':plz'] = $plz;
    $fields[] = 'telefon = :telefon';
    $params[':telefon'] = $telefon;
    $fields[] = 'geburtstag = :geburtstag';
    $params[':geburtstag'] = $geburtstag;

    // Optional: Passwort ändern, wenn gesetzt und min. 6 Zeichen
    $password = (string)($data['password'] ?? '');
    if ($password !== '' && strlen($password) >= 6) {
      $fields[] = 'password_hash = :pwd';
      $params[':pwd'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $stmt = $db->prepare($sql);
    return $stmt->execute($params);
  }

  public function deleteUser(int $id): bool
  {
    $db = $this->dbConnect();
    $db->beginTransaction();
    try {
      // Lehrer-ID holen (falls vorhanden)
      $stmt = $db->prepare('SELECT id FROM lehrer WHERE user_id = :uid LIMIT 1');
      $stmt->execute([':uid' => $id]);
      $lehrerId = (int)($stmt->fetchColumn() ?: 0);

      if ($lehrerId > 0) {
        // Kindtabellen zu Lehrer
        $this->safeExec($db, 'DELETE FROM lehrer_fach WHERE lehrer_id = :lid', [':lid' => $lehrerId]);
        $this->safeExec($db, 'DELETE FROM klassen_lehrer WHERE lehrer_id = :lid', [':lid' => $lehrerId]);
        $this->safeExec($db, 'DELETE FROM lehrer WHERE id = :lid', [':lid' => $lehrerId]);
      }

      // Schüler/Verwaltungseinträge
      $this->safeExec($db, 'DELETE FROM schueler WHERE user_id = :uid', [':uid' => $id]);
      $this->safeExec($db, 'DELETE FROM verwaltung WHERE user_id = :uid', [':uid' => $id]);

      // Optionale, projektabhängige Tabellen (ignoriert, wenn nicht vorhanden)
      $this->safeExec($db, 'DELETE FROM user_sessions WHERE user_id = :uid', [':uid' => $id]);
      $this->safeExec($db, 'DELETE FROM sessions WHERE user_id = :uid', [':uid' => $id]);
      $this->safeExec($db, 'DELETE FROM password_reset_tokens WHERE user_id = :uid', [':uid' => $id]);

      // User löschen (nachdem alle Referenzen entfernt sind)
      $stmt = $db->prepare('DELETE FROM users WHERE id = :uid');
      $stmt->execute([':uid' => $id]);

      $db->commit();
      return true;
    } catch (Throwable $e) {
      if ($db->inTransaction()) $db->rollBack();
      // optional: error_log('[deleteUser] '.$e->getMessage());
      return false;
    }
  }

  private function safeExec(PDO $db, string $sql, array $params = []): void
  {
    try {
      $stmt = $db->prepare($sql);
      $stmt->execute($params);
    } catch (Throwable $e) {
      // optional: error_log('[deleteUser:safeExec] '.$e->getMessage());
    }
  }
}