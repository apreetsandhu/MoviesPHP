<?php




ini_set         ('display_errors', 1);
error_reporting (E_ALL | E_STRICT);

/*
function localDatabase() {

    ini_set('display_errors', 1);
    error_reporting(E_ALL | E_STRICT);
	
	
	
   		$dbunixSocket = '/ubc/icics/mss/sandhuap/mysql/mysql.sock';
   		$dbuser = '';
   		$dbpass = 'a';
   		$dbname = 'sandhuap'; 
   	try {
    
   		$dbLocal = new PDO("mysql:localhost=host;dbname=$dbname", $dbuser, $dbpass);
        $dbLocal->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Server Error");
        die("HTTP/1.1 500 Server Error: Database Unavailable ({$e->getMessage()})");
    }
    return $dbLocal;
}

*/

function mainDatabase() {

    ini_set('display_errors', 1);
    error_reporting(E_ALL | E_STRICT);
	
   		$dbunixSocket = '/ubc/icics/mss/cics516/db/cur/mysql/mysql.sock';
   		$dbname = 'cics516';
 		$dbuser = 'cics516';
  		$dbpass = 'cics516password';
   		
    try {
   		$dbMain = new PDO("mysql:unix_socket=$dbunixSocket;dbname=$dbname", $dbuser, $dbpass);
        $dbMain->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        header("HTTP/1.1 500 Server Error");
        die("HTTP/1.1 500 Server Error: Database Unavailable ({$e->getMessage()})");
    }
    return $dbMain;
}



# to get all movies by actor name
function fetchActorID($db, $fName, $lName) {
	
	$sqlActorID = "SELECT id FROM actors WHERE last_name='$lName' 
							AND first_name LIKE '$fName%' 
							ORDER BY film_count DESC, id ASC;";
	$rows = $db->prepare($sqlActorID);
    try {
        $rows->execute(); 
    } catch (PDOException $ex) {
        print ("Error: <?= $ex->getMessage()?>)");
    }
    
    $rowOne = $rows->fetch();
    if ($rows->rowCount() != 0) { #checking if the actor is in db
        $id = $rowOne["id"];
        return $id;
    } else {
        print "Actor " . $fName . " " . $lName . " not found.";
        return -1;
    }

}

function searchAllMovies($db, $id, $fName, $lName) {

   $rows = $db->prepare("SELECT m.name, m.year FROM
						movies m
						INNER JOIN roles r
							ON r.movie_id = m.id
						INNER JOIN actors a
							ON a.id=r.actor_id
						WHERE first_name like '$fName%' AND last_name like'$lName%'
							AND film_count =(SELECT max(film_count) from actors
							WHERE first_name like '$fName%' AND last_name like'$lName%')
								AND a.id =(SELECT min(id) from actors WHERE first_name like '$fName%'
								AND last_name like'$lName%');");
    try {
        $rows->execute();
    } catch (PDOException $ex) {
        print ("Error: <?= $ex->getMessage()?>)");
    }
    return $rows;
}

function searchCommonMovies($db, $id, $fName, $lName){
	$rows = $db->query("SELECT m.name, m.year 
						FROM movies m
							INNER JOIN roles ro
								ON m.id = ro.movie_id
							INNER JOIN actors ac 
								ON ro.actor_id = ac.id
							INNER JOIN roles ru 
								ON m.id = ru.movie_id
							INNER JOIN actors ar 
								ON ru.actor_id = ar.id
							WHERE ac.first_name = '$fName%' 
								AND ac.last_name ='$lName%' 
								AND ar.first_name = 'Kevin' 
								AND ar.last_name ='Bacon'
									ORDER BY m.year DESC, m.name ASC;");
	 try {
        $rows->execute();
    } catch (PDOException $ex) {
        print ("Error: <?= $ex->getMessage()?>)");
    }
    return $rows;								
	
}


function printAllMoviesTable($db, $rows) {
 ?>   
	<table>
		<tr>
			<th>#</th>
			<th>Title</th>
			<th>Year</th>
		</tr>
	<?php
	$count_seq = 1;
	foreach($rows as $row):?>
		<tr>
			<td><?php echo $count_seq; $count_seq++;  ?></td>
			<td><?= $row["name"]; ?></td>
			<td><?= $row["year"]; ?></td>	
		</tr>
	<?php endforeach; ?>
	</table>
	<?php
}




?>
