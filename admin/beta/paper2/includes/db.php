<?php
/**
 * Created by PhpStorm.
 * User: Frontend
 * Date: 3/10/2016
 * Time: 8:58 PM
 */

if(preg_match('/db.php/', $_SERVER['REQUEST_URI'])){header('location: ../dashboard.php');}

/*$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mycity';*/

$host = 'localhost';
$user = 'root';
$pass = 'mysql';
$db = 'mycity29_maindb';

$link = mysqli_connect($host, $user, $pass, $db, 3306) or die('Database error' . mysqli_error($link));