<?php
define('APP_DIR', realpath('./'));

if (!is_file("./install/lock") && is_file("./install/index.php")) {
    @header("location:http://{$_SERVER["HTTP_HOST"]}/install/index.php");
}
require(APP_DIR.'/protected/lib/speed.php');