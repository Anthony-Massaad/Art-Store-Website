<?php
require_once('constants.inc.php');


// BASIC FUNCTIONS THAT ARE ONLY USED ONCE //

function getPaintingByID($id, $pdo){
    $sql = "SELECT * FROM Paintings where PaintingID=?";
    $exe = $pdo->prepare($sql);
    $exe->execute([$id]);
    $row = $exe->fetch(); 
    return createPainting($row, $pdo);
}

function createPainting($painting){
    return new Painting($painting[PAINTING_ID], $painting[ARTIST_ID], $painting[GALLERY_ID], $painting[IMAGE_FILE_NAME], 
                            $painting[TITLE], $painting[SHAPE_ID], $painting[ACCESSION_NUMBER], 
                            $painting[DESCRIPTION], $painting[EXCERPT], $painting[COST], $painting[COPYRIGHT], $painting[YEAR_OF_WORK], $painting[WIDTH], 
                                $painting[HEIGHT], $painting[MEDIUM], $painting[MUSEUM_LINK], $painting[WIKI_LINK], $painting[GOOGLE_LINK], $painting[GOOGLE_DESCRIPTION]); 
}

function printTypesWithPrice($types){
    $counter = count($types);
    while($counter){
        if ($counter == count($types)){
            echo '<option selected>None [+$'.$types[--$counter]->getPrice().']</option>';
            continue; 
        }
        echo '<option>'.$types[--$counter]->getTitle().' [+$'.$types[$counter]->getPrice().']</option>';
    }
}

function printTypeWithoutPrice($type){
    $counter = count($type);
    while($counter){  
        if ($counter == count($type)){
            echo '<option selected>'.$type[--$counter]->getTitle().'</option>';
            continue; 
        }
        echo '<option>'.$type[--$counter]->getTitle().'</option>';
    }
}



// CLASSES FOR OBJECTS BEING USED MORE THAN ONCE OR COMMON OBJECTS //
class Types{
    private $sql; 
    private $pdo; 
    private $lst; 
    function __construct($sql, $pdo){
        $this->sql = $sql; 
        $this->pdo = $pdo; 
    }

    public function parseTypeFrame(){
        $rows = $this->pdo->query($this->sql);
        $this->lst = array();
        foreach ($rows as $row){
            $this->lst[] = new TypeFrame($row[TITLE], $row[PRICE], $row[COLOR], $row[SYLE]);
        }
        return $this->lst;
    }

    public function parseTypeMatt(){
        $rows = $this->pdo->query($this->sql);
        $this->lst = array();
        foreach ($rows as $row){
            $this->lst[] = new TypeMatt($row[TITLE], $row[COLOR_CODE]);
        }
        return $this->lst;
    }

    public function parseTypeGlass(){
        $rows = $this->pdo->query($this->sql);
        $this->lst = array();
        foreach ($rows as $row){
            $this->lst[] = new TypeGlass($row[TITLE], $row[DESCRIPTION], $row[PRICE]);
        }
        return $this->lst;
    }
}

class RetrievePaintingData{
    private $sql;
    private $pdo;
    function __construct($sql, $pdo){
        $this->sql = $sql;
        $this->pdo = $pdo;
    }

    public function runQueryToGetTop20Paintings(){
        $rows = $this->pdo->query($this->sql);
        $reducedListOfPaintings = array();
        foreach ($rows as $row){
            $reducedListOfPaintings[] = createPainting($row);
        }
        return $reducedListOfPaintings;
    }
}

class FilterDropDown{
    private $sql;
    private $attribute; 
    private $pdo; 
    function __construct($sql, $attribute, $pdo){
        $this->sql = $sql; 
        $this->attribute = $attribute;
        $this->pdo = $pdo;
    }

    public function getDropDownOptions(){
        $rows = $this->pdo->query($this->sql);
        $reducedListOfPaintings = array();
        foreach ($rows as $row){
            $reducedListOfPaintings[] = $row[$this->attribute];
        }
        return $reducedListOfPaintings;
    }
}

class Artist{
    private $firstName;
    private $lastName;
    private $birth;
    private $city;
    private $death;
    function __construct($firstName, $lastName, $city, $birth, $death=null){
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birth = $birth;
        $this->city = $city;
        $this->death = $death; 
    }

    public function getFirstName(){
        return $this->firstName; 
    }

    public function getLastName(){
        return $this->lastName; 
    }

    public function getCity(){
        return $this->city;
    }

    public function getBirth(){
        return $this->birth;
    }

    public function getDeath(){
        return $this->death; 
    }

}

class Gallery{
    private $galleryName;
    private $galleryNativeName;
    private $galleryCity;
    private $galleryCountry;
    private $gallerySite;
    function __construct($galleryName, $galleryNativeName, $galleryCity, $galleryCountry, $gallerySite){
        $this->galleryName = $galleryName; 
        $this->galleryNativeName = $galleryNativeName;
        $this->galleryCity = $galleryCity;
        $this->galleryCountry = $galleryCountry;
        $this->gallerySite = $gallerySite;
    }

    public function getGalleryName(){
        return $this->galleryName;
    }

    public function getGalleryNativeName(){
        return $this->galleryNativeName; 
    }

    public function getGalleryCity(){
        return $this->gallerycity; 
    }

    public function getGalleryCountry(){
        return $this->gallleryCountry; 
    }

    public function getGallerySite(){
        return $this->gallerySite; 
    }

}

class Shape{
    private $shapeName; 
    function __construct($shapeName){
        $this->shapeName = $shapeName;
    }

    public function getShapeName(){
        return $this->shapeName; 
    }
}


class Genre{
    private $genreName; 
    private $eraID; 
    private $description; 
    private $link; 
    function __construct($genreName, $eraID, $description, $link){
        $this->genreName = $genreName;
        $this->eraID = $eraID;
        $this->description = $description; 
        $this->link = $link; 
    }

    public function getGenreName(){
        return $this->genreName; 
    }

    public function getLink(){
        return $this->link; 
    }

    public function getEraID(){
        return $this->eraID; 
    }

    public function getDescription(){
        return $this->description;
    }
}

class Subject{

    private $subjectName; 
    function __construct($name){
        $this->subjectName = $name; 
    }

    public function getSubjectName(){
        return $this->subjectName; 
    }

}


class Review{
    
    private $rating;
    private $date; 
    private $comment; 
    function __construct($rating, $date, $comment){
        $this->rating = $rating; 
        $this->date = $date; 
        $this->comment = $comment; 
    }

    public function getRating(){
        return $this->rating; 
    }

    public function getDate(){
        return $this->date; 
    }

    public function getComment(){
        return $this->comment; 
    }

}

class TypeFrame{
    private $title; 
    private $price; 
    private $color; 
    private $style;
    function __construct($title, $price, $color, $style){
        $this->title = $title; 
        $this->price = $price; 
        $this->color = $color; 
        $this->style = $style;
    }

    public function getTitle(){
        return $this->title; 
    }

    public function getPrice(){
        return round($this->price, 2); 
    }

    public function getColor(){
        return $this->color; 
    }

    public function getStyle(){
        return $this->style; 
    }
}

class TypeGlass{
    private $title; 
    private $description;
    private $price; 
    
    function __construct($title, $description, $price){
        $this->title = $title; 
        $this->description = $description; 
        $this->price = $price; 
    }

    public function getTitle(){
        return $this->title; 
    }

    public function getDescription(){
        return $this->description; 
    }

    public function getPrice(){
        return round($this->price, 2); 
    }
}

class TypeMatt{
    private $title; 
    private $colorCode; 

    function __construct($title, $colorCode){
        $this->title = $title; 
        $this->colorCode = $colorCode; 
    }

    public function getTitle(){
        return $this->title; 
    }

    public function getColorCode(){
        return $this->colorCode; 
    }
}

class Painting {
    private $paintingID;
    private $artistID;
    private $galleryID; 
    private $imageFileName; 
    private $title; 
    private $shapeID; 
    private $accessionNumber; 
    private $description; 
    private $excerpt; 
    private $cost;
    private $copyRight; 
    private $yearOfWork;
    private $width; 
    private $height; 
    private $medium; 
    private $museumLink;
    private $wikiLink;
    private $googleLink;
    private $googleText; 

    private $artist;
    private $gallery;
    private $shape;  
    private $reviews; 
    private $averageReviews;
    private $genres;  
    private $subjects;
    function __construct($paintingID, $artistID, $galleryID, $imageFileName, $title, $shapeID, $accessionNumber, 
                            $description, $excerpt, $cost, $copyright, $yearOfWork, $width, 
                                $height, $medium, $museumLink, $wikiLink, $googleLink, $googleText){
        $this->paintingID = $paintingID; 
        $this->artistID = $artistID; 
        $this->galleryID = $galleryID; 
        $this->imageFileName = $imageFileName; 
        $this->title = $title; 
        $this->shapeID = $shapeID; 
        $this->accessionNumber = $accessionNumber; 
        $this->description = $description; 
        $this->excerpt = $excerpt;
        $this->cost = $cost; 
        $this->copyRight = $copyright;
        $this->yearOfWork = $yearOfWork; 
        $this->width = $width; 
        $this->height = $height; 
        $this->medium = $medium; 
        $this->museumLink = $museumLink;
        $this->wikiLink = $wikiLink;
        $this->reviews = array(); 
        $this->genres = array(); 
        $this->subjects = array();
        $this->averageReviews = 0;
        $this->googleLink = $googleLink;
        $this->googleText = $googleText;

        $this->createArtist($artistID);
        $this->createGallery($galleryID);
        $this->createShape($shapeID);
        $this->gatherReviews($paintingID); 
        $this->gatherGenresOfPainting($paintingID);
        $this->gatherSubjects($paintingID);
    }

    private function createArtist($artistID){
        $sql = "SELECT * FROM Artists where ArtistID=?";
        $exe = createPDO()->prepare($sql);
        $exe->execute([$artistID]);
        $row = $exe->fetch();
        $this->artist = new Artist($row[FIRSTNAME], $row[LASTNAME], $row[NATIONALITY], $row[YEAR_OF_BIRTH], $row[YEAR_OF_DEATH]);
    }

    private function createGallery($galleryID){
        $sql = "SELECT * FROM Galleries where GalleryID=?";
        $exe = createPDO()->prepare($sql);
        $exe->execute([$galleryID]);
        $row = $exe->fetch();
        $this->gallery = new Gallery($row[GALLERY_NAME], $row[GALLERY_NATIVE_NAME], $row[GALLERY_CITY], $row[GALLERY_COUNTRY], $row[GALLERY_WEB_SITE]);
    }

    private function createShape($shapeID){
        $sql = "SELECT * FROM Shapes where ShapeID=?";
        $exe = createPDO()->prepare($sql);
        $exe->execute([$shapeID]);
        $row = $exe->fetch();
        $this->shape = new Shape($row[SHAPE_NAME]);
    }

    private function gatherReviews($paintingID){
        $sql = "SELECT * FROM Reviews WHERE PaintingID='$paintingID'";
        $results = createPDO()->query($sql);
        foreach ($results as $row){
            $this->reviews[] = new Review($row[RATING], $row[RATING_DATE], $row[COMMENT]);
            $this->averageReviews += $row[RATING];
        }
        if (sizeof($this->reviews) == 0){
            $this->averageReviews = 0;
        }else{
            $this->averageReviews = round($this->averageReviews/sizeof($this->reviews));
        }
    }

    private function gatherGenresOfPainting($paintingID){
        $sql = "SELECT * FROM Genres AS g INNER JOIN PaintingGenres AS pg ON g.GenreID=pg.GenreID AND pg.PaintingID='$paintingID'";
        $results = createPDO()->query($sql);
        foreach ($results as $row){
            $this->genres[] = new Genre($row[GENRE_NAME], $row[ERA_ID], $row[DESCRIPTION], $row[WIKI_GENRE_LINK]);
        }
    }

    private function gatherSubjects($paintingID){
        $sql = "SELECT * FROM Subjects AS s 
                    INNER JOIN PaintingSubjects AS ps ON 
                        s.SubjectID=ps.SubjectID AND ps.PaintingID='$paintingID'";
        $results = createPDO()->query($sql);
        foreach ($results as $row){
            $this->subjects[] = new Subject($row[SUBJECT_NAME]);
        } 
    }

    public function getSubjects(){
        return $this->subjects;
    }

    public function getReviews(){
        return $this->reviews; 
    }

    public function getGenres(){
        return $this->genres; 
    }

    public function getArtist(){
        return $this->artist; 
    }

    public function getGallery(){
        return $this->gallery; 
    }

    public function getShape(){
        return $this->shape; 
    }

    public function getAverageReviews(){
        return $this->averageReviews; 
    }

    public function getPaintingID(){
        return $this->paintingID; 
    }

    public function getArtistID(){
        return $this->artistID; 
    }

    public function getGalleryID(){
        return $this->galleryID;
    }

    public function getImageFileName(){
        return $this->imageFileName;
    }

    public function getTitle(){
        return $this->title; 
    }

    public function getShapeID(){
        return $this->shapeID; 
    }

    public function getAccessionNumber(){
        return $this->accessionNumber; 
    }

    public function getDescription(){
        return $this->description; 
    }

    public function getExcerpt(){
        return $this->excerpt;
    }

    public function getCost(){
        return round($this->cost, 2);
    }

    public function getCopyRight(){
        return $this->copyRight; 
    }

    public function getYearOfWork(){
        return $this->yearOfWork;
    }

    public function getWidth(){
        return $this->width; 
    }

    public function getHeight(){
        return $this->height; 
    }
    
    public function getMedium(){
        return $this->medium;
    }

    public function getMuseumLink(){
        return $this->museumLink;
    }

    public function getWikiLink(){
        return $this->wikiLink; 
    }

    public function getGoogleLink(){
        return $this->googleLink;
    }

    public function getGoogleText(){
        return $this->googleText; 
    }

}


?>