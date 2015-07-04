<?php include("top.html");
include("common.php"); 

#Anmolpreet Sandhu, 87685146

$db = dbLargeConnect();

$fName = $_GET["firstname"];
$lName = $_GET["lastname"];


$id = fetchActorIDFromName($db, $fName, $lName);

?>
<h1>Results for <?php echo $fName." ".$lName; ?> and Kevin Bacon</h1>
<?php
	$rows = searchCommonMovies($db, $id, $fName,$lName);
	$rowCount = $rows->rowCount();
	if($rowCount >= 1) {
		printAllMoviesTable($db, $rows);
	}
include("bottom.html"); ?>
