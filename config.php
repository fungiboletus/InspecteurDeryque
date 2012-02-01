<?php

define('TIME_ZONE', 'Europe/Paris');
setlocale (LC_ALL, 'fr_FR.utf8','fr_FR', 'fr'); 

// Use of url rewriting
define('URL_REWRITING', 'app');
//define('URL_REWRITING', false);

define('DEBUG',true);

define('DB_DSN_PDO', 'mysql:host=localhost;dbname=inspecteurderyque');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_FREEZE', true);
?>
