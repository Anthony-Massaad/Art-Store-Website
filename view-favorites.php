<?php
    include 'includes/header.inc.php';
    //session_start();

    $favorite_array = $_SESSION["fav_array"];


    echo '<section class="eleven wide column">';
    echo '<h1 class="ui header">Favorite Painting(s)</h1>';
    echo '<ul class="ui divided items" id="paintingsList">';


    foreach ($favorite_array as $paintingInfo){
        echo '<li class="item">';
        echo '<a class="ui small image" href="single-paintings.php?id='.$paintingInfo[0].'"><img src="images/art/works/square-small/'.$paintingInfo[1].'.jpg"></a>';
        echo '<div class="content">';
        echo '<a class="header" href="single-paintings.php?id='.$paintingInfo[0].'">'.$paintingInfo[2].'</a>';
        echo '<div class="extra">';
        echo '<a class="ui icon orange button" href="cart.php?id='.$paintingInfo[0].'"><i class="add to cart icon"></i></a>';
        echo '<a class="ui icon button" href="remove-favorites.php?id='.$paintingInfo[0].'"><i class="heart icon"></i></a>';
        echo '</div>';
        echo '</div>';
        echo '</li>';
    }

    echo '</section>';

?>

<?php include 'includes/footer.inc.php';?>