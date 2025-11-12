<?php

declare(strict_types=1);

abstract class DataProviderSchool
{
  protected string $source;
  public function __construct(string $source)
  {
    $this->source = $source;
  }

  // getter
  // abstract public function getSubjects(): array;
  abstract public function getSubjectsPaginated(int $page, int $perPage, string $sort = 'fach', string $dir = 'asc'): array;
  // abstract public function getClasses(): array;
  abstract public function getClassesPaginated(int $page, int $perPage, string $sort = 'klasse', string $dir = 'asc'): array;
  abstract public function getPLZ(): array;
  abstract public function getCity(): array;
  // abstract public function getTeachers(): array;
  abstract public function getTeachersPaginated(int $page, int $perPage): array;
  // abstract public function getLearners(): array;
  abstract public function getLearnersPaginated(int $page, int $perPage): array;

  // abstract public function getOffices(): array;
  // abstract public function getOfficesPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc'): array;

  // setter
  abstract public function setSubjects(string $newSubject): bool;
  abstract public function setClasses(string $newClass): bool;
  abstract public function setPLZ(int $newPLZ): bool;
  abstract public function setCity(string $newCity): bool;

  // Mutation function
  abstract public function deleteSubject(int $id): bool;
  abstract public function deleteLearner(int $id): bool;
  abstract public function deleteTeacher(int $id): bool;
  abstract public function deleteOffice(int $id): bool;
  abstract public function deleteClass(int $id): bool;
  abstract public function getSubjectById(int $id): ?array;
  abstract public function updateSubject(int $id, string $fach): bool;
  abstract public function getTeacherByUserId(int $userId): ?array;
  abstract public function updateTeacher(int $userId, string $vorname, string $nachname): bool;

  // abstract public function getOfficeByUserId(int $userId): ?array;
  // abstract public function updateOffice(int $userId, string $vorname, string $nachname, string $email): bool;

  abstract public function getLearnerByUserId(int $userId): ?array;
  abstract public function updateLearner(int $userId, string $vorname, string $nachname, int $klasseId): bool;

  abstract public function getClassById(int $id): ?array;
  abstract public function updateClass(int $id, string $klasse): bool;

  abstract public function getAllClasses(): array;
  // Subjects
  abstract public function getAllSubjects(): array;
  abstract public function getTeacherSubjectIds(int $userId): array;
  abstract public function updateTeacherSubjects(int $userId, array $subjectIds): bool;

  // Verwaltungsrollen
  // abstract public function getAllVerwaltungsRollen(): array;
  abstract public function getVerwaltungsrollenMap(): array;

  // Offices
  abstract public function getOfficeByUserId(int $userId): ?array;
  abstract public function updateOffice(int $userId, string $vorname, string $nachname, int $verwaltungsRolleId): bool;

  // Classes – Lehrer-Zuordnung
  abstract public function getAllTeachersForAssign(): array;        // [ ['id'=>lehrer_id, 'name'=>...], ... ]
  abstract public function getTeacherIdsByClassId(int $classId): array; // [lehrer_id, ...]
  abstract public function updateClassTeachers(int $classId, array $teacherIds): bool;
  abstract public function getAllOfficesWithRoles(): array;
  abstract public function getOfficesPaginated(
    int $page,
    int $perPage,
    ?string $sort,
    string $dir,
    ?string $q,
    array $fields,
    bool $matchAll
  ): array;
  abstract public function createSubject(string $fach): bool;
  abstract public function createClass(string $klasse): bool;
  abstract public function getAllVerwaltungsRollen(): array;
  // abstract public function createOffice(string $vorname, string $nachname, int $rolleId): bool;

  abstract public function getUsersPaginated(
    int $page,
    int $perPage,
    ?string $sort,
    string $dir,
    ?string $q,
    array $fields,
    bool $matchAll
  ): array;

  abstract public function createUser(array $data): bool;
}