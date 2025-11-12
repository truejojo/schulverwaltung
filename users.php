<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();
$usersLinks = getUsersLinks($isAuthenticated);

$params = parseListQuery(['sortDefault' => 'nachname']);
$result = DataSchool::getUsersPaginated(
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

$entity = $usersLinks['entity'];
$columns = $usersLinks['columns'];
$search  = buildSearchContext($params['q'], $params['matchAll'], $usersLinks['fields']);

setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);