<?php
// 2simulazione-esame2025/database_config.php

define('DB_HOST', 'localhost');
define('DB_USER_APP', 'ElTaras');
define('DB_PASS_APP', 'ciao');
define('DB_CHARSET', 'utf8mb4');

define('DB_PREFIX', '202425_5IB_ElTaras_');

function get_db_name($base_db_name) {
    return DB_PREFIX . $base_db_name;
}

define('DB_NAME_FILM', 'film_db');
define('DB_NAME_EVENTI', 'eventi_web');
// ... altri nomi base ...
define('DB_NAME_FOODEXPRESS', 'FoodExpressDB');
?>