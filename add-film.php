<?php include("top.html");
 include("common.php");

#Anmolpreet Sandhu, 87685146


try {

$db = dbSmallConnect(); #connecting to small database

#generating random numbers to to insert movie ids in small database
function randomDigits($length){
	$digits = 1;
    $numbers = range(0,9);
    shuffle($numbers);
    for($i = 0;$i < $length;$i++)
       $digits .= $numbers[$i];
    return $digits;
	}



//Fetching from database to display in the form

$director = $db->query("SELECT * FROM
				directors ORDER BY first_name;");
$actor = $db->query("SELECT * FROM
				actors ORDER BY first_name;");
$genre = $db->query("SELECT DISTINCT genre FROM
				directors_genres ORDER BY genre;");
$movie = $db->query("SELECT DISTINCT id FROM movies;");


//echo $rn;
?>

<!-- Form display -->

<form action="add-film.php" method="POST">
<fieldset>
	<legend>Some more Information</legend>
	<div>
		Title: <input name="title" type="text" size="12" pattern="[a-zA-Z\s]{2,30}" required="required" placeholder="title" /> 
		Year: <input name="year" type="text" size="12" pattern="[0-9]{4}" required="required" placeholder="year (yyyy)" /> 
		<br />Actor: <select name="act" id="act">
		<?php 
		foreach($actor as $act):?>
  			<option value="<?= $act['first_name'];?> <?= $act['last_name']; ?>">
  				<?= $act["first_name"];  ?>&nbsp;<?= $act['last_name']; ?></option>
		<?php endforeach; ?>
		</select>
		<br />Director: <select name="dir" id="dir"> //Giving some names to perform some insert actions
		<?php 
		foreach($director as $direc):?>
  			<option value="<?= $direc['first_name'];?> <?= $direc['last_name']; ?>">
  				<?= $direc["first_name"];  ?>&nbsp;<?= $direc['last_name']; ?></option>
		<?php endforeach; ?>
		</select>
		<br />
		Role: <input name="role" type="text" pattern="[a-zA-Z\s]{2,30}" 
				required="required" size="20" placeholder="Role in the movie" /> 
		<br />Genre: <select name="gen" id="gen">
		<?php 
		foreach($genre as $gen):?>
  			<option value="<?= $gen['genre'];?>">
  				<?= $gen["genre"];  ?></option>
		<?php endforeach; ?>
		</select><br />
		Rank <input name="rank" type="number" step="0.1" min="0" max="10"
				pattern="^[+]?\d+(,\d{2})?" required="required" placeholder="n.n" /><br /> 
		id: <select name="movID" id="movID">
  			<option value="<?php echo randomDigits(4); ?>"><?php echo randomDigits(4); ?></option>
		</select><br />
		<input type="submit" value="go" />
	</div>
</fieldset>
</form>

<?php

}
catch (PDOException $ex) {
  ?>
  <p>Sorry, a database error occurred. Please try again later.</p>
  <p>(Error details: <?= $ex->getMessage() ?>)</p>
  <?php
}


//Insertion

if($_SERVER['REQUEST_METHOD'] == "POST") {
		
	
	$title = $_POST["title"];
	$year = $_POST["year"];
	$rank = $_POST["rank"];
	$movieID = $_POST["movID"];
	
	$role = $_POST["role"];
	
	//directors' parameters
	$dName       = $_POST ["dir"];
	$dirNames = explode(" ", $dName);
	
	//actors' parameters
	$aName = $_POST["act"];
	$actNames = explode(" ", $aName);
	
	//genre info
	$genre = $_POST["gen"];
	

//Insertion into movie table
	try {
        	$stmt = $db->prepare ("INSERT INTO movies (id, name, year, rank) 
          						VALUES (:movieID,'$title','$year','$rank');");
          	$stmt->bindParam (":movieID", $movieID);
 			$stmt->execute();
    	
        } catch (PDOException $e) {
          header ("HTTP/1.1 500 Server Error");
          die    ("HTTP/1.1 500 Server Error: Error inserting: {$e->getMessage()}");
		}



//update film count into actors table
		try {
			if(count($actNames) == 2) {
        		$stmt = $db->prepare ("UPDATE actors SET film_count = film_count + 1 
        							WHERE first_name='$actNames[0]' and last_name='$actNames[1]';") ;
 				$stmt->execute();
 			}
 			elseif(count($actNames) == 3) {
 				$stmt = $db->prepare ("UPDATE actors SET film_count = film_count + 1 
        							WHERE first_name='$actNames[0] $actNames[1]' and last_name='$actNames[2]';") ;
 				$stmt->execute();
 			}
 			else {
 				$stmt = $db->prepare ("UPDATE actors SET film_count = film_count + 1 
        							WHERE first_name='$actNames[0] $actNames[1] $actNames[2]' and last_name='$actNames[3]';") ;
 				$stmt->execute();
 			}
        
        } catch (PDOException $e) {
          header ("HTTP/1.1 500 Server Error");
          die    ("HTTP/1.1 500 Server Error: Error inserting: {$e->getMessage()}");
		}
		
	
//insertion into movie_directors table

		try {
        	$stmt = $db->prepare ("INSERT INTO movies_directors(movie_id,director_id) 
									SELECT :movieID,d.id FROM directors AS d
    								WHERE first_name like '$dirNames[0]' and last_name like '$dirNames[1]%';");
    		$stmt->bindParam (":movieID", $movieID);
 			$stmt->execute();
 
        } catch (PDOException $e) {
          header ("HTTP/1.1 500 Server Error");
          die    ("HTTP/1.1 500 Server Error: Error inserting: {$e->getMessage()}");
		}
		
//Inserting into Roles table
		try {
        	$stmt = $db->prepare ("INSERT INTO roles(role,movie_id,actor_id) 
									SELECT :role,:movieID,a.id FROM actors AS a
    								WHERE first_name like '$actNames[0]%' and last_name like '$actNames[1]%';");
    		$stmt->bindParam (":movieID", $movieID);
    		$stmt->bindParam (":role", $role);
 			$stmt->execute();
 
        } catch (PDOException $e) {
          header ("HTTP/1.1 500 Server Error");
          die    ("HTTP/1.1 500 Server Error: Error inserting: {$e->getMessage()}");
		}
		
		 print "Movie: " . $title . ", " . $year . " added successfully."; 
		 

    } 
include("bottom.html"); ?>
