<?php
declare(strict_types=1);

trait SubjectsTrait
{
  protected array $subjects = [];

  public function getSubjects(): array
  {
    return $this->subjects;
  }

  // akzeptiert 1..n Subjects
  public function setSubject(string ...$newSubjects): bool
  {
    $added = 0;
    foreach ($newSubjects as $s) {
      $s = trim($s);
      if ($s === '') {
        continue;
      }
      $this->subjects[] = $s;
      $added++;
    }
    return $added > 0;
  }

  // Optional: Liste setzen/ersetzen
  protected function setSubjectList(array $list): void
  {
    $this->subjects = array_values(
      array_filter(
        array_map(fn($v) => trim((string) $v), $list),
        fn($v) => $v !== ''
      )
    );
  }
}