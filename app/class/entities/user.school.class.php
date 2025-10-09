<?php

declare(strict_types=1);

abstract class User
{
  private int $id;
  private string $email;
  private string $password_hash;
  private string $vorname;
  private string $nachname;
  private string $adress;     // bewusst: "adress" wie in deinem Schema
  private int $plz;
  private string $city;
  private string $telephone;
  private \DateTimeInterface $birthday;
  private string $role;
  private \DateTimeInterface $created;
  private \DateTimeInterface $updated;

  public function __construct(
    int $id,
    string $email,
    string $password_hash,
    string $vorname,
    string $nachname,
    string $adress,
    int $plz,
    string $city,
    string $telephone,
    \DateTimeInterface $birthday,
    string $role,
    \DateTimeInterface $created,
    \DateTimeInterface $updated
  ) {
    $this->id = $id;
    $this->email = $email;
    $this->password_hash = $password_hash;
    $this->vorname = $vorname;
    $this->nachname = $nachname;
    $this->adress = $adress;
    $this->plz = $plz;
    $this->city = $city;
    $this->telephone = $telephone;
    $this->birthday = $birthday;
    $this->role = $role;
    $this->created = $created;
    $this->updated = $updated;
  }

  // Getter (final, da Properties privat sind)
  final public function getId(): int
  {
    return $this->id;
  }
  final public function getEmail(): string
  {
    return $this->email;
  }
  final public function getPasswordHash(): string
  {
    return $this->password_hash;
  }
  final public function getVorname(): string
  {
    return $this->vorname;
  }
  final public function getNachname(): string
  {
    return $this->nachname;
  }
  final public function getAdress(): string
  {
    return $this->adress;
  }
  final public function getPlz(): int
  {
    return $this->plz;
  }
  final public function getCity(): string
  {
    return $this->city;
  }
  final public function getTelephone(): string
  {
    return $this->telephone;
  }
  final public function getBirthday(): \DateTimeInterface
  {
    return $this->birthday;
  }
  final public function getRole(): string
  {
    return $this->role;
  }
  final public function getCreated(): \DateTimeInterface
  {
    return $this->created;
  }
  final public function getUpdated(): \DateTimeInterface
  {
    return $this->updated;
  }

  // Setter mit einfacher Validierung (true = gesetzt, false = invalid)
  public function setEmail(string $newEmail): bool
  {
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL))
      return false;
    $this->email = $newEmail;
    return true;
  }

  public function setPasswordHash(string $newPasswordHash): bool
  {
    if ($newPasswordHash === '' || strlen($newPasswordHash) > 255)
      return false;
    $this->password_hash = $newPasswordHash;
    return true;
  }

  public function setVorname(string $newVorname): bool
  {
    $newVorname = trim($newVorname);
    if ($newVorname == '')
      return false;
    $this->vorname = $newVorname;
    return true;
  }

  public function setNachname(string $newNachname): bool
  {
    $newNachname = trim($newNachname);
    if ($newNachname == '')
      return false;
    $this->nachname = $newNachname;
    return true;
  }

  public function setAdress(string $newAdress): bool
  {
    $newAdress = trim($newAdress);
    if ($newAdress == '')
      return false;
    $this->adress = $newAdress;
    return true;
  }

  // Alias (Kompatibilität)
  public function setPLZ(int $newPLZ): bool
  {
    return $this->setPlz($newPLZ);
  }

  public function setCity(string $newCity): bool
  {
    $newCity = trim($newCity);
    if ($newCity == '')
      return false;
    $this->city = $newCity;
    return true;
  }

  public function setTelephone(string $newTelephone): bool
  {
    $newTelephone = trim($newTelephone);
    if ($newTelephone == '')
      return false;
    if (!preg_match('/^[0-9 +()\/-]{3,30}$/', $newTelephone))
      return false;
    $this->telephone = $newTelephone;
    return true;
  }

  public function setBirthday(\DateTimeInterface $newBirthday): bool
  {
    $this->birthday = $newBirthday;
    return true;
  }

  public function setRole(string $newRole): bool
  {
    $newRole = trim($newRole);
    if ($newRole == '')
      return false;
    $this->role = $newRole;
    return true;
  }

  public function setUpdated(\DateTimeInterface $newUpdated): bool
  {
    $this->updated = $newUpdated;
    return true;
  }
}