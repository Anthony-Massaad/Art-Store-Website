<?php
    define('DBCONNECTION', 'mysql:host=localhost;dbname=art');
    define('DBUSER', 'testuser');
    define('DBPASS', 'mypassword');
    $pdo = new PDO(DBCONNECTION, DBUSER, DBPASS);
    $memcache = new Memcache; 
    $memcache->connect('localhost', 11211) or die ("could not connect to memcache server");
    function createPDO(){
        return new PDO(DBCONNECTION, DBUSER, DBPASS);
    }
?>