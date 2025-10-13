<?php
class DataSchool
{
  private static $provider;

  public static function initialize($dataProvider): void
  {
    self::$provider = $dataProvider;
  }

  // getter
  // public static function getSubjects(): array
  // {
  //   return self::$provider->getSubjects();
  // }

  public static function getSubjectsPaginated(int $page, int $perPage, string $sort = 'fach', string $dir = 'asc'): array
  {
    return self::$provider->getSubjectsPaginated($page, $perPage, $sort, $dir);
  }

  // public static function getClasses(): array
  // {
  //   return self::$provider->getClasses();
  // }

  public static function getClassesPaginated(int $page, int $perPage, string $sort = 'klasse', string $dir = 'asc'): array
  {
    return self::$provider->getClassesPaginated($page, $perPage, $sort, $dir);
  }

  public static function getPLZ(): array
  {
    return self::$provider->getPLZ();
  }

  public static function getCity(): array
  {
    return self::$provider->getCity();
  }

  // public static function getTeachers(): array
  // {
  //   return self::$provider->getTeachers();
  // }
  public static function getTeachersPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc'): array
  {
    return self::$provider->getTeachersPaginated($page, $perPage, $sort, $dir);
  }

  // public static function getLearners(): array
  // {
  //   return self::$provider->getLearners();
  // }

  public static function getLearnersPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc'): array
  {
    return self::$provider->getLearnersPaginated($page, $perPage, $sort, $dir);
  }

  // public static function getOffices(): array
  // {
  //   return self::$provider->getOffices();
  // }

  public static function getOfficesPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc'): array
  {
    return self::$provider->getOfficesPaginated($page, $perPage, $sort, $dir);
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
}