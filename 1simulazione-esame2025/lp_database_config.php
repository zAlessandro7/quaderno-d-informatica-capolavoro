<?php
// 1simulazione-esame2025/lp_database_config.php
if (!defined('LP_DB_HOST')) define('LP_DB_HOST', 'localhost');
if (!defined('LP_DB_USER_APP')) define('LP_DB_USER_APP', 'ElTaras');
if (!defined('LP_DB_PASS_APP')) define('LP_DB_PASS_APP', 'ciao');
if (!defined('LP_DB_CHARSET')) define('LP_DB_CHARSET', 'utf8mb4');

if (!defined('LP_DB_PREFIX_GENERAL')) define('LP_DB_PREFIX_GENERAL', '202425_5IB_ElTaras_');
define('LP_DB_NAME_BASE', 'LinguePlatformDB');

function get_lp_db_name() {
    return LP_DB_PREFIX_GENERAL . LP_DB_NAME_BASE;
}
?>