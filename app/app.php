<?php
declare(strict_types=1);

define('APP_PATH', dirname(__DIR__));       // ...\schulverwaltung
define('APP_DIR', __DIR__); 

require_once APP_DIR . '/inc/config.php';
require_once APP_DIR . '/inc/utility.functions.php';
require_once APP_DIR . '/inc/auth.functions.php';

require_once APP_DIR . '/class/data.school.class.php';
require_once APP_DIR . '/class/dataprovider.school.class.php';
require_once APP_DIR . '/class/mysqldataprovider.school.class.php';

try {
    DataSchool::initialize(
        dataProvider: new MySqlDataProviderSchool(
            CONFIG['db_source'],
            dbUser: CONFIG['db_user'],
            dbPassword: CONFIG['db_password']
        )
    );
} catch (DatabaseConnectionException $e) {
    echo '<h1 style="font-family: sans-serif; color:#b00;">Datenbank aktuell nicht erreichbar.</h1>';
    echo '<p>Bitte versuche es später erneut.</p>';
    exit;
}