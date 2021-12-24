<?php


session_start();

if (isset($_GET["id"])){
    for ($i=0; $i<count($_SESSION["fav_array"]); $i++){
        if ($_SESSION["fav_array"][$i][0] == $_GET["id"]){
            unset($_SESSION["fav_array"][$i]);
            $_SESSION["fav_array"] = array_values($_SESSION["fav_array"]);
            break; 
        }
    }
}


header("Location: view-favorites.php");

?>