<?php
/**
 * Created by PhpStorm.
 * User: Frontend
 * Date: 3/10/2016
 * Time: 8:58 PM
 */

if (preg_match('/db.php/', $_SERVER['REQUEST_URI'])) {
    header('location: ../dashboard.php');
}

/*$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mycity';*/
if (!defined('ENVIRONMENT')) {
    if ($_SERVER['SERVER_NAME'] == 'localhost') {
        define("ENVIRONMENT", "development");
        if (!defined('BASE_URL')) define("BASE_URL", "http://localhost");
        if (!defined('ADMIN_BASE_URL')) define("ADMIN_BASE_URL", "http://localhost/admin");
    } else {
        define("ENVIRONMENT", "production");
        if (!defined('BASE_URL')) define("BASE_URL", "https://mycity.com");
        if (!defined('ADMIN_BASE_URL')) define("ADMIN_BASE_URL", "https://mycity.com/admin");
    }
}
if (ENVIRONMENT == 'production') {
    $host = 'localhost';
    $user = 'mycity29_root';
    $pass = 'zBi6h49~';
    $db = 'mycity29_maindb';
} else {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'mycity29_maindb';
}

$link = mysqli_connect($host, $user, $pass, $db, 3306) or die('Database error' . mysqli_error($link));

$pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db . "", $user, $pass);
$pdo->exec("set names utf8");