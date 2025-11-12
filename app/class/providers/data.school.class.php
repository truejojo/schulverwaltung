<?php
class DataSchool
{
  private static $provider;

  public static function initialize($dataProvider): void
  {
    self::$provider = $dataProvider;
  }

  // getter
  public static function getPLZ(): array
  {
    return self::$provider->getPLZ();
  }

  public static function getCity(): array
  {
    return self::$provider->getCity();
  }

  public static function getLearnersPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc', string $q = '', array $fields = [], bool $matchAll = false): array
  {
    return self::$provider->getLearnersPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);
  }
  public static function getTeachersPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc', string $q = '', array $fields = [], bool $matchAll = false): array
  {
    return self::$provider->getTeachersPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);
  }
  public static function getSubjectsPaginated(int $page, int $perPage, string $sort = 'fach', string $dir = 'asc', string $q = '', array $fields = [], bool $matchAll = false): array
  {
    return self::$provider->getSubjectsPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);
  }
  public static function getClassesPaginated(int $page, int $perPage, string $sort = 'klasse', string $dir = 'asc', string $q = '', array $fields = [], bool $matchAll = false): array
  {
    return self::$provider->getClassesPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);
  }
  // public static function getOfficesPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc', string $q = '', array $fields = [], bool $matchAll = false): array
  // {
  //   return self::$provider->getOfficesPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);
  // }


  // setter
  public static function setSubjects(string $newSubject): bool
  {
    return self::$provider->setSubjects($newSubject);
  }

  public static function setClasses(string $newClass): bool
  {
    return self::$provider->setClasses($newClass);
  }

  public static function setPLZ(int $newPLZ): bool
  {
    return self::$provider->setPLZ($newPLZ);
  }

  public static function setCity(string $newCity): bool
  {
    return self::$provider->setCity($newCity);
  }

  // Mutation function
  public static function deleteSubject(int $id): bool
  {
    return self::$provider->deleteSubject($id);
  }
  public static function deleteLearner(int $id): bool
  {
    return self::$provider->deleteLearner($id);
  }
  public static function deleteTeacher(int $id): bool
  {
    return self::$provider->deleteTeacher($id);
  }

  // Verwaltungsrollen
  // public static function getAllVerwaltungsRollen(): array
  // {
  //   return self::$provider->getAllVerwaltungsRollen();
  // }
  public static function getVerwaltungsrollenMap(): array
  {
    return self::$provider->getVerwaltungsrollenMap();
  }

  // Offices: statt Email die Rolle ändern
  public static function getOfficeByUserId(int $userId): ?array
  {
    return self::$provider->getOfficeByUserId($userId);
  }
  public static function updateOffice(int $userId, string $vorname, string $nachname, int $verwaltungsRolleId): bool
  {
    return self::$provider->updateOffice($userId, $vorname, $nachname, $verwaltungsRolleId);
  }
  public static function deleteOffice(int $id): bool
  {
    return self::$provider->deleteOffice($id);
  }
  public static function deleteClass(int $id): bool
  {
    return self::$provider->deleteClass($id);
  }

  // Subjects
  public static function getAllSubjects(): array
  {
    return self::$provider->getAllSubjects();
  }
  public static function getTeacherSubjectIds(int $userId): array
  {
    return self::$provider->getTeacherSubjectIds($userId);
  }
  public static function updateTeacherSubjects(int $userId, array $subjectIds): bool
  {
    return self::$provider->updateTeacherSubjects($userId, $subjectIds);
  }
  public static function getSubjectById(int $id): ?array
  {
    return self::$provider->getSubjectById($id);
  }
  public static function updateSubject(int $id, string $fach): bool
  {
    return self::$provider->updateSubject($id, $fach);
  }
  public static function getTeacherByUserId(int $userId): ?array
  {
    return self::$provider->getTeacherByUserId($userId);
  }
  public static function updateTeacher(int $userId, string $vorname, string $nachname): bool
  {
    return self::$provider->updateTeacher($userId, $vorname, $nachname);
  }

  // public static function getOfficeByUserId(int $userId): ?array
  // {
  //   return self::$provider->getOfficeByUserId($userId);
  // }
  // public static function updateOffice(int $userId, string $vorname, string $nachname, string $email): bool
  // {
  //   return self::$provider->updateOffice($userId, $vorname, $nachname, $email);
  // }

  public static function getLearnerByUserId(int $userId): ?array
  {
    return self::$provider->getLearnerByUserId($userId);
  }
  public static function updateLearner(int $userId, string $vorname, string $nachname, int $klasseId): bool
  {
    return self::$provider->updateLearner($userId, $vorname, $nachname, $klasseId);
  }

  public static function getClassById(int $id): ?array
  {
    return self::$provider->getClassById($id);
  }
  public static function updateClass(int $id, string $klasse): bool
  {
    return self::$provider->updateClass($id, $klasse);
  }

  public static function getAllClasses(): array
  {
    return self::$provider->getAllClasses();
  }

  // Classes: Lehrer-Zuordnung
  public static function getAllTeachersForAssign(): array
  {
    return self::$provider->getAllTeachersForAssign();
  }
  public static function getTeacherIdsByClassId(int $classId): array
  {
    return self::$provider->getTeacherIdsByClassId($classId);
  }
  public static function updateClassTeachers(int $classId, array $teacherIds): bool
  {
    return self::$provider->updateClassTeachers($classId, $teacherIds);
  }
  public static function getAllOfficesWithRoles(): array
  {
    return self::$provider->getAllOfficesWithRoles();
  }
  public static function getOfficesPaginated(
    int $page,
    int $perPage,
    ?string $sort,
    string $dir,
    ?string $q,
    array $fields,
    bool $matchAll
  ): array {
    return self::$provider->getOfficesPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);
  }
  public static function createSubject(string $fach): bool
  {
    return self::$provider->createSubject($fach);
  }
  public static function createClass(string $klasse): bool
  {
    return self::$provider->createClass($klasse);
  }
  public static function getAllVerwaltungsRollen(): array
  {
    return self::$provider->getAllVerwaltungsRollen();
  }

  // public static function createOffice(string $vorname, string $nachname, int $rolleId): bool
  // {
  //   return self::$provider->createOffice($vorname, $nachname, $rolleId);
  // }

  public static function getUsersPaginated(
    int $page,
    int $perPage,
    ?string $sort,
    string $dir,
    ?string $q,
    array $fields,
    bool $matchAll
  ): array {
    return self::$provider->getUsersPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);
  }

  public static function createUser(array $data): bool
  {
    return self::$provider->createUser($data);
  }

  // Get, Update, Delete User
  public static function getUserById(int $id): ?array
  {
    return self::$provider->getUserById($id); // provider() an dein Projekt anpassen
  }

  public static function updateUser(int $id, array $data): bool
  {
    return self::$provider->updateUser($id, $data);
  }

  public static function deleteUser(int $id): bool
  {
    return self::$provider->deleteUser($id);
  }
}