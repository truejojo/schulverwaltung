 <?php
  class DataSchool {
    private static $provider;

    public static function initialize($dataProvider): void {
      self::$provider = $dataProvider;
    }

    // getter
    public static function getSubjects(): array {
      return self::$provider->getSubjects();
    }

    public static function getClasses(): array {
      return self::$provider->getClasses();
    }
    
    public static function getPLZ(): array {
      return self::$provider->getPLZ();
    }

    public static function getCity(): array {
      return self::$provider->getCity();
    }
    public static function getTeachers(): array {
      return self::$provider->getTeachers();
    }
    public static function getLearners(): array {
      return self::$provider->getLearners();
    }
    public static function getOffices(): array {
      return self::$provider->getOffices();
    }

    

    // setter
    public static function setSubjects(string $newSubject): array {
      return self::$provider->setSubjects($newSubject);
    }

    public static function setClasses(string $newClass): array {
      return self::$provider->setClasses($newClass);
    }

    public static function setPLZ(int $newPLZ): array {
      return self::$provider->setPLZ($newPLZ);
    }

    public static function setCity(string $newCity): array {
      return self::$provider->setCity($newCity);
    } 
  }