<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();
$teachersLinks = getTeachersLinks($isAuthenticated);

$params = parseListQuery(['sortDefault' => 'nachname']);
$result = DataSchool::getTeachersPaginated(
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

$entity = $teachersLinks['entity'];
$columns = $teachersLinks['columns'];
$search  = buildSearchContext($params['q'], $params['matchAll'], $teachersLinks['fields']);

setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);