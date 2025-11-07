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
  public static function getOfficesPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc', string $q = '', array $fields = [], bool $matchAll = false): array
  {
    return self::$provider->getOfficesPaginated($page, $perPage, $sort, $dir, $q, $fields, $matchAll);
  }


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
  public static function deleteOffice(int $id): bool
  {
    return self::$provider->deleteOffice($id);
  }
  public static function deleteClass(int $id): bool
  {
    return self::$provider->deleteClass($id);
  }
}