<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();
$officesLinks = getOfficesLinks($isAuthenticated);

$params = parseListQuery(['sortDefault' => 'nachname']);
$result = DataSchool::getOfficesPaginated(
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

$entity = $officesLinks['entity'];
$columns = $officesLinks['columns'];
$search  = buildSearchContext($params['q'], $params['matchAll'], $officesLinks['fields']);

setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);