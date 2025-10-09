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
 public function getSubjects(): array
{
    return $this->fetchEntity("faecher", "fach");
}
  public function getClasses(): array
  {
    return $this->fetchEntity("klassen", "klasse");
  }
  public function getPLZ(): array
  {
    return [];
  }
  public function getCity(): array
  {
    return [];
  }

  public function getTeachers(): array
  {
    return $this->fetchUsers(
      'SELECT vorname, nachname, email FROM lehrer JOIN users ON lehrer.user_id = users.id ORDER BY nachname ASC'
    );
  }
  public function getLearners(): array
  {
    return $this->fetchUsers(
      'SELECT vorname, nachname, email FROM schueler JOIN users ON schueler.user_id = users.id ORDER BY nachname ASC'
    );
  }
  public function getOffices(): array
  {
    return $this->fetchUsers(
      'SELECT vorname, nachname, email FROM verwaltung JOIN users ON verwaltung.user_id = users.id ORDER BY nachname ASC'
    );
  }

  // setter
  public function setSubjects(string $newSubject): array
  {
    return [];
  }
  public function setClasses(string $newClass): array
  {
    return [];
  }
  public function setPLZ(int $newPLZ): array
  {
    return [];
  }
  public function setCity(string $newCity): array
  {
    return [];
  }

  // private functions
  private function fetchEntity(string $table, string $column): array
  {
    $rows = $this->querySQL("SELECT $column FROM $table ORDER BY $column ASC");

    return array_map(static function (array $r) use ($column): array {
      return [
        'id' => $r['id'] ?? null,
        $column => trim($r[$column] ?? ''),
      ];
    }, $rows);
  }

  private function fetchUsers(string $sql, array $params = []): array
  {
    $rows = $this->querySQL($sql, $params);

    return array_map(static function (array $r): array {
      return [
        'id' => $r['id'] ?? null,
        'vorname' => trim($r['vorname'] ?? ''),
        'nachname' => trim($r['nachname'] ?? ''),
        'email' => $r['email'] ?? ''
      ];
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
   
  // private helper
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