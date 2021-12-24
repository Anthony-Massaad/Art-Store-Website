<?php
  include 'includes/header.inc.php';
  require_once('includes/constants.inc.php');
  require_once('includes/artist-config.inc.php');  
  require_once('includes/database.inc.php');
  
  

  if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['artist']) && !isset($_GET['museum']) && !isset($_GET['shape'])){
    $selectedCacheKey = $_GET['artist'];
    if (!$memcache->get($selectedCacheKey)){
      $sql = "SELECT * From paintings WHERE paintings.ArtistID=(select ArtistID from artists where artists.LastName='$selectedCacheKey') order by AccessionNumber desc limit 20";
      $data = new RetrievePaintingData($sql, $pdo);
      $paintingToDisplay = $data->runQueryToGetTop20Paintings();
      $memcache->set($selectedCacheKey, $paintingToDisplay, false, 240) or die ("Failed to save cache data at the server.");
    }else{
      $paintingToDisplay = $memcache->get($selectedCacheKey);
    }
  }else if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['artist']) && isset($_GET['museum']) && !isset($_GET['shape'])){
    $selectedCacheKey = $_GET['museum'];
    if (!$memcache->get($selectedCacheKey)){
      $sql = "SELECT * From paintings WHERE paintings.GalleryID=(select GalleryID from galleries where galleries.GalleryName='$selectedCacheKey') order by AccessionNumber desc limit 20";
      $data = new RetrievePaintingData($sql, $pdo);
      $paintingToDisplay = $data->runQueryToGetTop20Paintings();
      $memcache->set($selectedCacheKey, $paintingToDisplay, false, 240) or die ("Failed to save cache data at the server.");
    }else{
      $paintingToDisplay = $memcache->get($selectedCacheKey);
    }
  }else if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['artist']) && !isset($_GET['museum']) && isset($_GET['shape'])){
    $selectedCacheKey = $_GET['shape'];
    if (!$memcache->get($selectedCacheKey)){
      $sql = "SELECT * From paintings WHERE paintings.ShapeID=(select ShapeID from shapes where shapes.ShapeName='$selectedCacheKey') order by AccessionNumber desc limit 20";
      $data = new RetrievePaintingData($sql, $pdo);
      $paintingToDisplay = $data->runQueryToGetTop20Paintings();
      $memcache->set($selectedCacheKey, $paintingToDisplay, false, 240) or die ("Failed to save cache data at the server.");
    }else{
      $paintingToDisplay = $memcache->get($selectedCacheKey);
    }
  }else{
    $selectedCacheKey = 'AllPaintings';
    if (!$memcache->get($selectedCacheKey)){
      $sql = 'SELECT * From Paintings ORDER BY AccessionNumber DESC LIMIT 20';
      $data = new RetrievePaintingData($sql, $pdo);
      $paintingToDisplay = $data->runQueryToGetTop20Paintings();
      $memcache->set($selectedCacheKey, $paintingToDisplay, false, 240) or die ("Failed to save cache data at the server.");
    }else{
      $paintingToDisplay = $memcache->get($selectedCacheKey);
    }
  }
?>

<main class="ui segment doubling stackable grid container">
  
<section class="five wide column">
      <form class="ui form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <h4 class="ui dividing header">Filters</h4>

        <div class="field">
          <label>Artist</label>
          <select class="ui fluid dropdown" name="artist">
              <option disabled selected>Select Artist</option>  
              <?php 
                $cacheKey = 'ArtistDropdown';
                if (!$memcache->get($cacheKey)){
                  $sql = "SELECT LastName FROM artists";
                  $data = new FilterDropDown($sql, LASTNAME, $pdo);
                  $artistLastNames = $data->getDropDownOptions();
                  $memcache->set($cacheKey, $artistLastNames, false, 240) or die ("Failed to save cache data at the server.");
                }else{
                  $artistLastNames = $memcache->get($cacheKey);
                }
                foreach ($artistLastNames as $artistName){
                  echo '<option>'.$artistName.'</option>';
                }
              ?>
          </select>
        </div>  
        <div class="field">
          <label>Museum</label>
          <select class="ui fluid dropdown" name="museum">
              <option disabled selected>Select Museum</option>  
              <?php 
                $cacheKey = 'MuseumDropdown';
                if (!$memcache->get($cacheKey)){
                  $sql = "SELECT GalleryName FROM galleries";
                  $data = new FilterDropDown($sql, GALLERY_NAME, $pdo);
                  $galleryNames = $data->getDropDownOptions();
                  $memcache->set($cacheKey, $galleryNames, false, 240) or die ("Failed to save cache data at the server.");
                }else{
                  $galleryNames = $memcache->get($cacheKey);
                } 
                
                foreach ($galleryNames as $galleryName){
                  echo '<option>'.$galleryName.'</option>';
                }
              ?>
          </select>
        </div>   
        <div class="field">
          <label>Shape</label>
          <select class="ui fluid dropdown" name="shape">
              <option disabled selected>Select Shape</option>  
              <?php 
                $cacheKey = 'ShapeDropdown';
                if (!$memcache->get($cacheKey)){
                  $sql = "SELECT ShapeName FROM shapes";
                  $data = new FilterDropDown($sql, SHAPE_NAME, $pdo);
                  $shapeNames = $data->getDropDownOptions();
                  $memcache->set($cacheKey, $shapeNames, false, 240) or die ("Failed to save cache data at the server.");
                }else{
                  $shapeNames = $memcache->get($cacheKey);
                } 

                foreach ($shapeNames as $shapeName){
                  echo '<option>'.$shapeName.'</option>';
                }
              ?>
          </select>
        </div>   
          <button class="small ui orange button" type="submit">
            <i class="filter icon"></i> Filter 
          </button>    
      </form>
  </section>
    

  <section class="eleven wide column">
      <h1 class="ui header">Top 20 Painting(s)</h1>
      <ul class="ui divided items" id="paintingsList">
      <?php
        foreach ($paintingToDisplay as $painting){
          echo '<li class="item">';
          echo '<a class="ui small image" href="single-paintings.php?id='.$painting->getPaintingID().'"><img src="images/art/works/square-medium/'.$painting->getImageFileName().'.jpg"></a>';
          echo '<div class="content">';
          echo '<a class="header" href="single-paintings.php?id='.$painting->getPaintingID().'">'.$painting->getTitle().'</a>';
          echo '<div class="meta"><span class="cinema">'.$painting->getArtist()->getLastName().'</span></div>';
          echo '<div class="description"><p>'.$painting->getDescription().'</p></div>';
          echo '<div class="meta"><strong>$'.$painting->getCost().'</strong></div>';
          echo '<div class="extra">';
          echo '<a class="ui icon orange button" href="cart.php?id='.$painting->getPaintingID().'"><i class="add to cart icon"></i></a>';
          echo '<a class="ui icon button" href="addToFavorites.php?id='.$painting->getPaintingID().'&imageID='.$painting->getImageFileName().'&title='.$painting->getTitle().'"><i class="heart icon"></i></a>';
          echo '</div>';
          echo '</div>';
          echo '</li>';
        }
      ?>  

      </ul>        
  </section>  
</main>  
<?php include 'includes/footer.inc.php';?>
