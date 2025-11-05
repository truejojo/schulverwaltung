<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();
$classesLinks = getClassesLinks($isAuthenticated);

$params = parseListQuery(['sortDefault' => 'klasse']);
$result = DataSchool::getClassesPaginated(
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

$entity = $classesLinks['entity'];
$columns = $classesLinks['columns'];
$search  = buildSearchContext($params['q'], $params['matchAll'], $classesLinks['fields']);

setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);