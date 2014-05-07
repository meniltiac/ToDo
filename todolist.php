<?php
    session_start();
    $Body = "";
	$errors = 0;

	if($_POST['project']) {
		$project = $_POST['project'];
	}
	else {
		$project = $_SESSION['project'];
	}
	 //Convert Session Variables into Database Info
     $DatabaseName = $_SESSION['databaseName'];
     $DatabaseUser = $_SESSION['databaseUser'];
     $DatabasePassword = $_SESSION['databasePassword'];

	if ($errors == 0) {
		//checking to see if a connection to the database can be made
	     $DBConnect = @mysql_connect("$DatabaseName", "$DatabaseUser", "$DatabasePassword");
	     if ($DBConnect === FALSE) {
	          $Body .= "<p>Unable to connect to the database server. " . "Error code " . mysql_errno() . ": " . mysql_error() . "</p>\n"; 
	          ++$errors; 
	          }
	           
	     else { 
	     	//for the debugging
	     	$Body .= "<!--Connected...-->"; 	
	     	
	     	$DBName = "ToDoLists"; 
	     	$TableName = "Project";
	     	$SecondTableName = "Items";
	     	
	     	if (!@mysql_select_db($DBName, $DBConnect)) { 
	     		$Body .= "<p>It looks like you haven't saved any to do lists.</p><p><a class='button' href='index.php'>Why don't you go add something</a></p>";
	     	
	     	if($QueryResult === FALSE) 
	     		
	     		$Body .= "<p>Unable to execute the query.</p>" . "<p>Error Code " . mysql_errno() . ":" . mysql_error($DBConnect) . "</p>";
	     	
	     	else 
	     		//for the debugging
	     		$Body .= "<!--THE DATABASE EXISTS!-->";	     		
	     	} 
	     	
	     	$result = @mysql_select_db($DBName, $DBConnect); 
	     	
	     	if ($result === FALSE) { 
	     		$Body .= "<p>Unable to select the database. " . "Error code " . mysql_errno($DBConnect) . ": " . mysql_error($DBConnect) . "</p>"; 
	     		++$errors; 
	     		} 
	     	//for the debugging	
	     	$Body .= "<!--selected the correct database-->";
	     		
/*      		mysql_select_db($DBName, $DBConnect); */
     		
     		$SQLstring = "SELECT project_ID FROM $TableName WHERE project_name='$project';"; 
     		$QueryResult = @mysql_query($SQLstring, $DBConnect); 
     		$num_rows = mysql_num_rows($QueryResult);
	 		
 			if ($num_rows == 0) {
	 			$Body .= "TO DO: Add things to your to do list.<br />";
	 		}

	 		else {
		 		while ($row = mysql_fetch_assoc($QueryResult)) {
		 		
			 		$ProjectID = $row['project_ID'];
			 		$SQLstring = "SELECT item, item_number FROM $SecondTableName WHERE project_ID=$ProjectID;"; 
			 		$QueryResult = @mysql_query($SQLstring, $DBConnect); 
		    		$num_rows = mysql_num_rows($QueryResult);

			 		$Body .= "<h3>" . $project . "</h3><ol>";

		 			if ($num_rows == 0) {
			 			$Body .= "TO DO: Add things to your to do list.<br />";
			 		}			 			
			 		else {
			 			while($item = mysql_fetch_assoc($QueryResult)) {
				 			$ToDo = $item['item'];
				 			$itemNumber = $item['item_number'];
				 			
				 			$Body .= "<li><input type='checkbox' name='item_to_delete[]' id='item_to_delete' value='$itemNumber'>" . $ToDo . "</li>";
			 			}
			 		
			 		$Body .= "</ol>";
			 		$Body .= '<input type="submit" class="btn" name="submit" value="Update, I got stuff done!" /><input type="reset" class="btn" name="reset" value="Undo Selection" />';
			 		}
		 		}
	 		}

	     	mysql_close($DBConnect); 
	     } 
	     	 
	  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Submitting Your To Dos</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<link href='http://fonts.googleapis.com/css?family=Homemade+Apple' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Raleway:500' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css.css">
</head>
	<body>
		<h1><a href="index.php">Dos are for Doing</a></h1>
		<div class="to_do">
			<h2>What have you completed today?</h2>
		
			<form method="post" action="update.php" id="done_form">
		<?php 
			echo $Body;
		?>
			</form>
			<div class="go_to">	
				<a class="button" href="index.php">Add More To Dos</a>
			</div>
		</div>
	</body>
</html>

