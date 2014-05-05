<?php

    session_start();
    $Body = "";
	$errors = 0;

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	 //Convert Session Variables into Database Info
     $DatabaseName = $_SESSION['databaseName'];
     $DatabaseUser = $_SESSION['databaseUser'];
     $DatabasePassword = $_SESSION['databasePassword'];

	 if(isset($_POST['item_to_delete'])) {
		
			if ($errors == 0) {
			
				//Get the IDs of To Do Items that have been checked off on the To Do list.
			     $DBConnect = @mysql_connect("$DatabaseName", "$DatabaseUser", "$DatabasePassword");
			     if ($DBConnect === FALSE) {
			          $Body .= "<p>Unable to connect to the database server. " . "Error code " . mysql_errno() . ": " . mysql_error() . "</p>\n"; 
			          ++$errors; 
			          }
			           
			     else { 
					 $TableName = "Items";		
					 $IDstoDelete = $_POST['item_to_delete'];
					 $DBName = "ToDoLists"; 

			     	if (!@mysql_select_db($DBName, $DBConnect)) { 
			     		$Body .= "<p>It looks like you haven't saved any to do lists.</p><p><a class='button' href='index.php'>Why don't you go add something</a></p>";
			     	
			     	if($QueryResult === FALSE) 
			     		
			     		$Body .= "<p>Unable to execute the query.</p>" . "<p>Error Code " . mysql_errno() . ":" . mysql_error($DBConnect) . "</p>";
			     	
			     	else 
			     		//for the debugging
			     		$Body .= "<!--THE DATABASE EXISTS!-->";
			     	} 


					 foreach ($IDstoDelete as $IDtoDelete) {
					 
			 			 $SQLstring = "DELETE FROM $TableName WHERE item_number=$IDtoDelete;";
			 			 			 			 
			 			 $QueryResult = mysql_query($SQLstring, $DBConnect); 
		
				     	if($QueryResult === FALSE) {
				     		
				     		$Body .= "<p>Unable to delete that item. Maybe you were lying when you said you did it?</p>" . "<p>Error Code " . mysql_errno() . ":" . mysql_error($DBConnect) . "</p>";
				     	
				     	$errors++;
				     	}
				     	else 
				     		//for the debugging
				     		$Body .= "<!--Deleted the To Do item!-->";
			 			 }
			 			 
			 			 if($errors == 0) {
				 			$Body .= "<h2>Way to go, slugger!</h2><p>Let's look at your to do list and see what else is on the docket for today.</p>";
				     		$Body .= "<a href='todolist.php?PHPSESSID=" . session_id() . "' class='button last'>What's still on the list?</a>";

			 			 }
			 			 
				     	} 
		     		mysql_close($DBConnect); 
			}

	 
	 }
	 else
	 	$Body .= "<h3>WHAAAAAAAAT?!</h3><p>You're slacking today, shuga. Go do something and then check it off your list!</p><a href='todolist.php?PHPSESSID=" . session_id() . "' class='button last'>Go Back</a>";




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
	<h1>Dos are for Doing</h1>
	<body>
		<div class="to_do">
		<?php 
			echo $Body;
		?>
		</div>
	</body>
</html>

