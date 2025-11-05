<?php
session_start();
require __DIR__ . '/app/app.php';
require APP_PATH . '/utility/entity.php';

$isAuthenticated = isAuthenticated();
$learnersLinks = getLearnersLinks($isAuthenticated);

$params = parseListQuery(['sortDefault' => 'nachname']);
$result = DataSchool::getLearnersPaginated(
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

$entity = $learnersLinks['entity'];
$columns = $learnersLinks['columns'];
$search  = buildSearchContext($params['q'], $params['matchAll'], $learnersLinks['fields']);


setView($entity, $isAuthenticated, $columns, $rows, $pagination, $search);