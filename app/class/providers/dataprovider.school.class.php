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
  abstract public function getOfficesPaginated(int $page, int $perPage, string $sort = 'nachname', string $dir = 'asc'): array;

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
}