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
  public function getSubjects(): array
  {
    return $this->fetchSubjects(
      'SELECT fach FROM faecher ORDER BY fach ASC'
    );
  }
  public function getClasses(): array
  {
    return $this->fetchClasses(
      'SELECT klasse FROM klassen ORDER BY klasse ASC'
    );
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
      // 'SELECT vorname, nachname FROM users WHERE role_id = 2 ORDER BY nachname ASC'
      'SELECT vorname, nachname FROM lehrer JOIN users ON lehrer.user_id = users.id ORDER BY nachname ASC'
    );
  }
  public function getLearners(): array
  {
    return $this->fetchUsers(
      // 'SELECT vorname, nachname FROM users WHERE role_id = 2 ORDER BY nachname ASC'
      'SELECT vorname, nachname FROM schueler JOIN users ON schueler.user_id = users.id ORDER BY nachname ASC'
    );
  }
  public function getOffices(): array
  {
    return $this->fetchUsers(
      // 'SELECT vorname, nachname FROM users WHERE role_id = 2 ORDER BY nachname ASC'
      'SELECT vorname, nachname FROM verwaltung JOIN users ON verwaltung.user_id = users.id ORDER BY nachname ASC'
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

  // private
  private function fetchSubjects(string $sql, array $params = []): array
  {
    $db = $this->dbConnect();

    if (empty($params)) {
      $statement = $db->query($sql);
    } else {
      $statement = $db->prepare($sql);
      $statement->execute($params);
    }
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn(array $row) => $row['fach'], $rows);
  }

  private function fetchClasses(string $sql, array $params = []): array
  {
    $db = $this->dbConnect();

    if (empty($params)) {
      $statement = $db->query($sql);
    } else {
      $statement = $db->prepare($sql);
      $statement->execute($params);
    }
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn(array $row) => $row['klasse'], $rows);
  }

  private function fetchUsers(string $sql, array $params = []): array
  {
    $db = $this->dbConnect();

    if (empty($params)) {
      $statement = $db->query($sql);
    } else {
      $statement = $db->prepare($sql);
      $statement->execute($params);
    }
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn(array $row) => $row['vorname'] . ' ' . $row['nachname'], $rows);
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