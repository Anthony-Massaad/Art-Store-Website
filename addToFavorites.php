<?php

session_start();

if (isset($_GET["id"]) && isset($_GET["imageID"]) && isset($_GET["title"])){
    $painting_info = array($_GET["id"], $_GET["imageID"], $_GET["title"]);
    if (isset($_SESSION["fav_array"])){
        $_SESSION["fav_array"][] = $painting_info; 
    }else{
        $_SESSION["fav_array"] = array();
        $_SESSION["fav_array"][] = $painting_info; 
    }
}

header("Location: view-favorites.php");

?>