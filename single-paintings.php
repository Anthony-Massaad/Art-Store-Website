<?php 
  include 'includes/header.inc.php';
  require_once('includes/constants.inc.php');
  require_once('includes/artist-config.inc.php');  
  require_once('includes/database.inc.php');

  if (isset($_GET["id"])){
    $cacheKeyID = "PaintingID".$_GET["id"];
    if (!$memcache->get($cacheKeyID)){
      $thePainting = getPaintingByID($_GET["id"], $pdo);
      $painting = $thePainting; 
      $memcache->set($cacheKeyID, $painting, false, 240) or die ("Failed to save cache data at the server.");
    }else{
      $painting = $memcache->get($cacheKeyID);
    }
  }else{
    $cacheKeyID = "PaintingID599";
    if (!$memcache->get($cacheKeyID)){
      $thePainting = getPaintingByID(599, $pdo);
      $painting = $thePainting;
      $memcache->set($cacheKeyID, $painting, false, 240) or die ("Failed to save cache data at the server.");
    }else{
      $painting = $memcache->get($cacheKeyID);
    }
  }
  
  $frames = array();
  $glass = array();
  $matts = array();
  $framesCacheKey = "TypeFrames";
  $glassCacheKey = "TypeGlass";
  $mattsCacheKey = "TypeGlass";

  if (!$memcache->get($framesCacheKey)){
    $sql = "SELECT * FROM typesframes";
    $allFrames = new Types($sql, $pdo);
    $frames = $allFrames->parseTypeFrame();
    $memcache->set($framesCacheKey, $frames, false, 240) or die ("Failed to save cache data at the server.");
  }else{
    $frames = $memcache->get($framesCacheKey);
  }

  if (!$memcache->get($glassCacheKey)){
    $sql = "SELECT * FROM typesglass";
    $allGlass = new Types($sql, $pdo);
    $glass = $allGlass->parseTypeGlass();
    $memcache->set($glassCacheKey, $glass, false, 240) or die ("Failed to save cache data at the server.");
  }else{
    $glass = $memcache->get($glassCacheKey);
  }

  if (!$memcache->get($mattsCacheKey)){
    $sql = "SELECT * FROM typesmatt";
    $allMatts = new Types($sql, $pdo);
    $matts = $allMatts->parseTypeGlass();
    $memcache->set($mattsCacheKey, $matts, false, 240) or die ("Failed to save cache data at the server.");
  }else{
    $matts = $memcache->get($mattsCacheKey);
  }

?>
<main>
  <!-- Main section about painting -->
  <section class="ui segment grey100">
    <div class="ui doubling stackable grid container">
      <div class="nine wide column">
        <?php echo '<img src="images/art/works/medium/'.$painting->getImageFileName().'.jpg" alt="..." class="ui big image" id="artwork">'; ?>
          <div class="ui fullscreen modal">
            <div class="image content">  
            <?php echo '<img src="images/art/works/large/'.$painting->getImageFileName().'.jpg" alt="..." class="image" >'; ?>
            <div class="description">
            <p></p>
          </div>
        </div>
      </div>                    
    </div>	<!-- END LEFT Picture Column --> 
    
    <div class="seven wide column">        
      <!-- Main Info -->
      <div class="item">
        <?php 
          echo '<h2 class="header">'.$painting->getTitle().'</h2>'; 
          echo '<h3>'.$painting->getArtist()->getLastName().'</h3>';
        ?>
            
        <div class="meta">
          <p>
            <?php 
              for ($i=0;$i<5;$i++){
                if ($i < $painting->getAverageReviews()){
                  echo '<i class="orange star icon"></i>';
                  continue;
                }
                echo '<i class="empty star icon"></i>';
              }
            ?>
          </p>
          <p>
            <?php echo $painting->getExcerpt(); ?>
          </p>
        </div>  
      </div>                          
                
      <!-- Tabs For Details, Museum, Genre, Subjects -->
      <div class="ui top attached tabular menu ">
          <a class="active item" data-tab="details"><i class="image icon"></i>Details</a>
          <a class="item" data-tab="museum"><i class="university icon"></i>Museum</a>
          <a class="item" data-tab="genres"><i class="theme icon"></i>Genres</a>
          <a class="item" data-tab="subjects"><i class="cube icon"></i>Subjects</a>    
      </div>
              
      <div class="ui bottom attached active tab segment" data-tab="details">
        <table class="ui definition very basic collapsing celled table">
          <tbody>
            <tr>
              <td>
                Artist
              </td>
              <td>
                <?php  echo '<a href="#">'.$painting->getArtist()->getLastName().'</a>'; ?>
              </td>                       
            </tr>
            <tr>                       
              <td>
                Year
              </td>
              <td>
                <?php echo $painting->getYearOfWork(); ?>
              </td>
            </tr>       
            <tr>
              <td>
                Medium
              </td>
              <td>
                <?php echo $painting->getMedium(); ?>
              </td>
            </tr>  
            <tr>
              <td>
                Dimensions
              </td>
              <td>
                <?php echo  $painting->getWidth().'cm x '.$painting->getHeight().'cm'; ?>
              </td>
            </tr>        
          </tbody>
        </table>
      </div>
      
      <div class="ui bottom attached tab segment" data-tab="museum">
        <table class="ui definition very basic collapsing celled table">
          <tbody>
            <tr>
              <td>
                  Museum
              </td>
              <td>
                <?php  echo $painting->getGallery()->getGalleryName(); ?>
              </td>
            </tr>       
            <tr>
              <td>
                  Assession #
              </td>
              <td>
                <?php echo $painting->getAccessionNumber(); ?>
              </td>
            </tr>  
            <tr>
              <td>
                  Copyright
              </td>
              <td>
                <?php echo $painting->getCopyRight(); ?>
              </td>
            </tr>       
            <tr>
              <td>
                  URL
              </td>
              <td>
                <?php echo '<a href="'.$painting->getMuseumLink().'">View painting at museum site</a>';  ?>
              </td>
            </tr>        
          </tbody>
        </table>    
      </div>     
          
      <div class="ui bottom attached tab segment" data-tab="genres">
        <ul class="ui list">
          <?php   
            foreach ($painting->getGenres() as $genre){
              echo '<li class="item"><a href="'.$genre->getLink().'">'.$genre->getGenreName().'</a></li>'; 
            }
          
          ?>
        </ul>
      </div>  

      <div class="ui bottom attached tab segment" data-tab="subjects">
        <ul class="ui list">
          <?php   
            foreach ($painting->getSubjects() as $subject){
              echo '<li class="item"><a href="#">'.$subject->getSubjectName().'</a></li>';
            }
          ?>
        </ul>
      </div>  
              
      <!-- Cart and Price -->
      <div class="ui segment">
        <div class="ui form">
          <div class="ui tiny statistic">
            <div class="value">
              <?php echo '$'.$painting->getCost(); ?>
            </div>
          </div>
          <div class="four fields">
            <div class="three wide field">
              <label>Quantity</label>
              <input type="number">
            </div>                               
            <div class="four wide field">
              <label>Frame</label>
              <select id="frame" class="ui search dropdown">
                <?php
                  printTypesWithPrice($frames);
                ?>
              </select>
            </div>  
            <div class="four wide field">
              <label>Glass</label>
              <select id="glass" class="ui search dropdown">
                <?php
                  printTypesWithPrice($glass);
                ?>
              </select>
            </div>  
            <div class="four wide field">
              <label>Matt</label>
              <select id="matt" class="ui search dropdown">
                <?php
                  printTypeWithoutPrice($matts);
                ?>
              </select>
            </div>           
          </div>                     
        </div>

        <div class="ui divider"></div>
        <button class="ui labeled icon orange button">
          <i class="add to cart icon"></i>
          Add to Cart
        </button>
        <button class="ui right labeled icon button">
            <?php echo '<a  href="addToFavorites.php?id='.$painting->getPaintingID().'&imageID='.$painting->getImageFileName().'&title='.$painting->getTitle().'"><i class="heart icon"></i> Add To Favorites</a>'; ?>
        </button>        
      </div>     <!-- END Cart -->                      
                        
    </div>	<!-- END RIGHT data Column --> 
  </div>		<!-- END Grid --> 
</section>		<!-- END Main Section --> 
  
  <!-- Tabs for Description, On the Web, Reviews -->
  <section class="ui doubling stackable grid container">
    <div class="sixteen wide column">
    
      <div class="ui top attached tabular menu ">
        <a class="active item" data-tab="first">Description</a>
        <a class="item" data-tab="second">On the Web</a>
        <a class="item" data-tab="third">Reviews</a>
      </div>
    
      <div class="ui bottom attached active tab segment" data-tab="first">
        <?php  echo $painting->getDescription(); ?>
      </div>	<!-- END DescriptionTab --> 
    
      <div class="ui bottom attached tab segment" data-tab="second">
        <table class="ui definition very basic collapsing celled table">
          <tbody>
            <tr>
              <td>
                Wikipedia Link
              </td>
              <td>
                <?php echo '<a href="'.$painting->getWikiLink().'">View painting on Wikipedia</a>';  ?>
              </td>                       
            </tr>                       
            
            <tr>
              <td>
                Google Link
              </td>
              <td>
                <?php echo '<a href="'.$painting->getGoogleLink().'">View painting on Google Art Project</a>';  ?>
              </td>                       
            </tr>
            
            <tr>
              <td>
                Google Text
              </td>
              <td>
                <?php   
                  if ($painting->getGoogleText() != null){
                    echo $painting->getGoogleText();
                  }
                ?>
              </td>                       
            </tr>                      
          </tbody>
        </table>
      </div>   <!-- END On the Web Tab --> 
    
      <div class="ui bottom attached tab segment" data-tab="third">                
        <div class="ui feed">
        
        <?php 
          $counter = 0;
          foreach ($painting->getReviews() as $review){
            echo '<div class="event">';
            echo '<div class="content">';
            echo '<div class="date">'.$review->getDate().'</div>';
            echo '<div class="meta">';
            echo '<a class="like">';
            for ($i=0; $i<$review->getRating(); $i++){
              echo '<i class="star icon"></i>';
            }
            echo '</a>';
            echo '</div>';
            echo '<div class="summary">';
            echo $review->getComment();
            echo '</div>';
            echo '</div>';
            echo '</div>';
            $counter++; 
            if ($counter != sizeof($painting->getReviews())){
              echo '<div class="ui divider"></div>';
            }
          }
        ?>                        
        </div>   <!-- END Reviews Tab -->          
      
      </div>        
  </section> <!-- END Description, On the Web, Reviews Tabs --> 
  
  <!-- Related Images ... will implement this in assignment 2 -->    
  <section class="ui container">
  <h3 class="ui dividing header">Related Works</h3> 
  <?php
    $cacheKeyID = "PaintingID".$_GET["id"]."related";
    $lastName = $painting->getArtist()->getLastName();
    $paintingID = $painting->getPaintingID();
    if (!$memcache->get($cacheKeyID)){
      $sql = "SELECT * From paintings WHERE paintings.ArtistID=(select ArtistID from artists where artists.LastName='$lastName') AND paintings.paintingID!='$paintingID' order by AccessionNumber desc limit 8";
      $data = new RetrievePaintingData($sql, $pdo);
      $relatedPaintings = $data->runQueryToGetTop20Paintings();
      $memcache->set($cacheKeyID, $relatedPaintings, false, 240) or die ("Failed to save cache data at the server.");
    }else{
      $relatedPaintings = $memcache->get($cacheKeyID);
    }    
    foreach ($relatedPaintings as $relatedPainting){
      echo '<a class="ui small image" href="single-paintings.php?id='.$relatedPainting->getPaintingID().'"><img src="images/art/works/square-medium/'.$relatedPainting->getImageFileName().'.jpg"></a> ';  
    }
  ?>
       
</section>  

</main>   
<?php include 'includes/footer.inc.php';?>
