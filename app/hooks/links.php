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
    ['href' => 'users.php', 'title' => 'Benutzer', 'description' => 'Benutzer verwalten.', 'auth' => true],
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
    'create'  => ['href' => 'actions/offices/add.php', 'label' => 'Neu'],
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

// function getPaginationLinks(bool $isAuthenticated, array $result): array
// {
//   $pagination = [
//     'page' => $result['page'],
//     'pages' => $result['pages'],
//     'hasPrev' => $result['hasPrev'],
//     'hasNext' => $result['hasNext'],
//   ];

//   return $isAuthenticated ? $pagination : [];
// }

function getPaginationLinks(bool $isAuthenticated, array $result): array {
    if (!$isAuthenticated) return [];

    $self  = basename($_SERVER['PHP_SELF']); // z. B. users.php
    $page  = (int)($result['page'] ?? $result['current'] ?? 1);
    $pages = (int)($result['pages'] ?? 1);

    // vorhandene Query-Parameter beibehalten (außer page)
    $baseQuery = $_GET ?? [];
    unset($baseQuery['page']);

    $makeHref = function (int $p) use ($self, $baseQuery): string {
        $qs = http_build_query($baseQuery + ['page' => $p]);
        return $self . '?' . $qs;
    };

    $hasPrev = $result['hasPrev'] ?? ($page > 1);
    $hasNext = $result['hasNext'] ?? ($page < $pages);

    $links = [
        // Kompatibilität für pagination.view.php
        'page'    => $page,
        'pages'   => $pages,
        'hasPrev' => $hasPrev,
        'hasNext' => $hasNext,

        // Zusätzliche Felder
        'current'  => $page,
        'prev'     => $hasPrev ? $page - 1 : null,
        'next'     => $hasNext ? $page + 1 : null,
        'prevHref' => $hasPrev ? $makeHref($page - 1) : null,
        'nextHref' => $hasNext ? $makeHref($page + 1) : null,
        'items'    => [],
    ];

    for ($p = 1; $p <= $pages; $p++) {
        $links['items'][] = [
            'page'   => $p,
            'href'   => $makeHref($p),
            'active' => ($p === $page),
        ];
    }

    return $links;
}

// ADD
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
    'entity'  => 'Fächer',
    'columns' => $columns,
    'fields'  => $fields,
    'create'  => ['href' => 'actions/subjects/add.php', 'label' => 'Neu'],
  ];

  return $isAuthenticated ? $all : [];
}

function getUsersLinks(bool $isAuthenticated): array
{
  $columns = [
    ['label' => 'Vorname', 'field' => 'vorname', 'sortable' => true],
    ['label' => 'Nachname', 'field' => 'nachname', 'sortable' => true],
    ['label' => 'E-Mail', 'field' => 'email', 'sortable' => true],
    ['label' => 'Rolle', 'field' => 'rolle', 'sortable' => true], 
  ];

  $fields = [
    ['key' => 'vorname', 'label' => 'Vorname'],
    ['key' => 'nachname', 'label' => 'Nachname'],
    ['key' => 'email', 'label' => 'E-Mail'],
     ['key' => 'rolle', 'label' => 'Rolle'],  
  ];

  $all = [
    'entity'  => 'Benutzer',
    'columns' => $columns,
    'fields'  => $fields,
    'create'  => ['href' => 'actions/users/add.php', 'label' => 'Neu'],
  ];

  return $isAuthenticated ? $all : [];
}