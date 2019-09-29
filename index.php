<?php
define('APP_DIR', realpath('./'));

if (!is_file("./install/lock") && is_file("./install/index.php")) {
    require(APP_DIR . '/install/index.php');
    exit;
}
require(APP_DIR . '/protected/lib/speed.php');