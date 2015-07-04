<?php include("top.html");
include("common.php");

#Anmolpreet Sandhu, 87685146

#try {

$db = dbLargeConnect();

$fName = $_GET["firstname"];
$lName = $_GET["lastname"];

#instead of two queries I used a single query to search the movies
# based on the name of the actor even there is confusion in names
# this query will search actors as the requirements given for the third query in question

$id = fetchActorID($db, $fName, $lName);

?>
<h1>Results for <?php echo $fName." ".$lName; ?></h1>
<?php
	$rows = searchAllMovies($db, $id, $fName,$lName);
	$rowCount = $rows->rowCount();
	if($rowCount >= 1) {
		printAllMoviesTable($db, $rows);
	}



include("bottom.html"); ?>


