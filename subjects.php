<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();
$subjectsLinks = getSubjectsLinks($isAuthenticated);

$isAuthenticated = isAuthenticated();
$subjectsLinks = getSubjectsLinks($isAuthenticated);

$params = parseListQuery(['sortDefault' => 'fach']);
$result = DataSchool::getSubjectsPaginated(
  $params['page'],
  $params['perPage'],
  $params['sort'],
  $params['dir'],
  $params['q'],
  $params['fields'],
  $params['matchAll']
);

$rows = $result['items'];
$pagination = getPaginationLinks($isAuthenticated, $result);

$entity = $subjectsLinks['entity'];
$columns = $subjectsLinks['columns'];
$search  = buildSearchContext($params['q'], $params['matchAll'], $subjectsLinks['fields']);

setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);