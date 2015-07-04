<?php include("top.html"); 

#Anmolpreet Sandhu
	#index page of the hw4

?>

<h1>The One Degree of Kevin Bacon</h1>
<p>Type in an actor's name to see if he/she was ever in a movie with Kevin Bacon!</p>
<p><img src="images\kevin_bacon.jpg" alt="Kevin Bacon" /></p>

<!-- Add film fields -->
<form action="add-film.php" method="GET">
	<fieldset>
		<legend>Add a new movie</legend>
		<div>
			<input type="submit" name="addMovie" value="go" />
		</div>
	</fieldset>
</form>

<?php include("bottom.html"); ?>
