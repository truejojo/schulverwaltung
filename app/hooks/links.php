<?php
function goHomeLink(string $label): void
{
  echo '<a href="index.php"
      class="text-lg inline-block px-3 bg-transparent text-gray-500 dark:text-gray-300 hover:underline">' . htmlspecialchars($label) . '</a>';
}

function getEntities(bool $isAuthenticated): array
{
  $all = [
    ['href' => 'learners.php', 'title' => 'Schüler', 'description' => 'Verwaltung von Schülerdaten.', 'auth' => true],
    ['href' => 'teachers.php', 'title' => 'Lehrer', 'description' => 'Lehrerdaten organisieren.', 'auth' => true],
    ['href' => 'offices.php', 'title' => 'Büro Assistenten', 'description' => 'Büro Assistenten verwalten.', 'auth' => true],
    ['href' => 'classes.php', 'title' => 'Klassen', 'description' => 'Klassenstrukturen verwalten.', 'auth' => true],
    ['href' => 'subjects.php', 'title' => 'Fächer', 'description' => 'Fächer verwalten.', 'auth' => true],
  ];

  return $isAuthenticated ? $all : [];
}

function getClassesLinks(bool $isAuthenticated): array
{
  $columns = [
    ['label' => 'Klasse', 'field' => 'klasse', 'sortable' => true],
    ['label' => 'Klassenlehrer/-in(nen)', 'field' => 'klassenlehrer', 'sortable' => false],
  ];

  $fields = [
    ['key' => 'klasse', 'label' => 'Klasse'],
    ['key' => 'klassenlehrer', 'label' => 'Klassenlehrer/-in(nen)'],
  ];

  $all = [
    'entity' => 'Klassen',
    'columns' => $columns,
    'fields' => $fields,
  ];

  return $isAuthenticated ? $all : [];
}
function getLearnersLinks(bool $isAuthenticated): array
{
  $columns = [
    ['label' => 'Vorname', 'field' => 'vorname', 'sortable' => true],
    ['label' => 'Nachname', 'field' => 'nachname', 'sortable' => true],
    ['label' => 'Klasse', 'field' => 'klasse', 'sortable' => true],
  ];

  $fields = [
    ['key' => 'vorname', 'label' => 'Vorname'],
    ['key' => 'nachname', 'label' => 'Nachname'],
    ['key' => 'klasse', 'label' => 'Klasse'],
  ];

  $all = [
    'entity' => 'Schüler',
    'columns' => $columns,
    'fields' => $fields,
  ];

  return $isAuthenticated ? $all : [];
}
function getOfficesLinks(bool $isAuthenticated): array
{
  $columns = [
    ['label' => 'Vorname', 'field' => 'vorname', 'sortable' => true],
    ['label' => 'Nachname', 'field' => 'nachname', 'sortable' => true],
    ['label' => 'Position', 'field' => 'position', 'sortable' => true],
  ];

  $fields = [
    ['key' => 'vorname', 'label' => 'Vorname'],
    ['key' => 'nachname', 'label' => 'Nachname'],
    ['key' => 'position', 'label' => 'Position'],
  ];

  $all = [
    'entity' => 'Büro Assistenten',
    'columns' => $columns,
    'fields' => $fields,
  ];

  return $isAuthenticated ? $all : [];
}
function getSubjectsLinks(bool $isAuthenticated): array
{
  $columns = [
    ['label' => 'Fach', 'field' => 'fach', 'sortable' => true],
    ['label' => 'Lehrkräfte', 'field' => 'lehrer', 'sortable' => false],
  ];

  $fields = [
    ['key' => 'fach', 'label' => 'Fach'],
    ['key' => 'lehrer', 'label' => 'Lehrkräfte'],
  ];

  $all = [
    'entity' => 'Fächer',
    'columns' => $columns,
    'fields' => $fields,
  ];

  return $isAuthenticated ? $all : [];
}
function getTeachersLinks(bool $isAuthenticated): array
{
  $columns =  [
    ['label' => 'Vorname', 'field' => 'vorname', 'sortable' => true],
    ['label' => 'Nachname', 'field' => 'nachname', 'sortable' => true],
    ['label' => 'Fächer', 'field' => 'faecher', 'sortable' => false],
  ];

  $fields = [
    ['key' => 'vorname', 'label' => 'Vorname'],
    ['key' => 'nachname', 'label' => 'Nachname'],
    ['key' => 'faecher', 'label' => 'Fächer'],
  ];

  $all = [
    'entity' => 'Lehrer',
    'columns' => $columns,
    'fields' => $fields,
  ];

  return $isAuthenticated ? $all : [];
}

function getPaginationLinks(bool $isAuthenticated, array $result): array
{
  $pagination = [
    'page' => $result['page'],
    'pages' => $result['pages'],
    'hasPrev' => $result['hasPrev'],
    'hasNext' => $result['hasNext'],
  ];

  return $isAuthenticated ? $pagination : [];
}
