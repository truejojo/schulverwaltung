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
  abstract public function getSubjects(): array;
  abstract public function getClasses(): array;
  abstract public function getPLZ(): array;
  abstract public function getCity(): array;

  abstract public function getTeachers(): array;
  abstract public function getLearners(): array;
  abstract public function getOffices(): array;

  // setter
  abstract public function setSubjects(string $newSubject): bool;
  abstract public function setClasses(string $newClass): bool;
  abstract public function setPLZ(int $newPLZ): bool;
  abstract public function setCity(string $newCity): bool;
}