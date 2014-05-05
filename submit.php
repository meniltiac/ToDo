<?php
    session_start();
    $Body = "";
	$errors = 0;

	 //Convert Session Variables into Database Info
     $DatabaseName = $_SESSION['databaseName'];
     $DatabaseUser = $_SESSION['databaseUser'];
     $DatabasePassword = $_SESSION['databasePassword'];

	//Throws an error if a project isn't selected or added
	if (empty($_POST['project']) && empty($_POST['new_project'])) {
	     ++$errors;
	     $Body .= "<p>You need to choose a project to add your do to!</p>\n";
	}
	//Throws an error if multiple projects are selected
	elseif ($_POST['project'] && $_POST['new_project']) {
		++$errors;
		$Body .= "<p>Oops, you chose too many projects to add your do to. Please hit your back button and just choose or add one. Thank you muchly!</p>";
	}
	elseif ($_POST['project'] && empty($_POST['new_project'])) {
		$project = $_POST['project'];
		$Body .= "<p>Great! I'll just go ahead and add that to your existing project.</p>";

	}
	else {
		$project = stripslashes($_POST['new_project']);
		$Body .= "<p>Starting a new project, eh? Excellent! Let's make lists of things that will help you succeed!</p>";
	}
	if ($errors > 0) {
	     $Body .= '<p><a class="button" href="index.php">GO BACK AND FIX IT</a></p>';
	}
	if ($errors == 0) {
	
		//Set session variables from submitted data
	     $toDo = htmlentities($_POST['toDo']);
		 
		 $_SESSION['project'] = $project;
		 $_SESSION['toDo'] = $toDo;		 		 
	}
	
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
	     	
	     	if (!@mysql_select_db($DBName, $DBConnect)) { 
	     		//for the debugging
	     		$Body .= "<!--Creating database...-->";
	     		$SQLstring = "CREATE DATABASE $DBName"; 
	     		$QueryResult = @mysql_query($SQLstring, $DBConnect); 
	     	
	     	if($QueryResult === FALSE) 
	     		
	     		$Body .= "<p>Unable to execute the query.</p>" . "<p>Error Code " . mysql_errno() . ":" . mysql_error($DBConnect) . "</p>";
	     	
	     	else 
	     		//for the debugging
	     		$Body .= "<!--Created a new Database, just for you!-->";
	     	} 
	     	
	     	$result = @mysql_select_db($DBName, $DBConnect); 
	     	
	     	if ($result === FALSE) { 
	     		$Body .= "<p>Unable to select the database. " . "Error code " . mysql_errno($DBConnect) . ": " . mysql_error($DBConnect) . "</p>\n"; 
	     		++$errors; 
	     		} 
	     	//for the debugging	
	     	$Body .= "<!--selected the correct database-->";
	     	
	     	//Checking for the Project Table, which includes a unique Primary Key (Project Name)	
     		mysql_select_db($DBName, $DBConnect);
     		
     		$TableName = "Project"; 
     		$SQLstring = "SHOW TABLES LIKE '$TableName'"; 
     		$QueryResult = @mysql_query($SQLstring, $DBConnect); 
     		
     		if(mysql_num_rows($QueryResult) == 0) { 
     			//for the debugging
     			$Body .= "<!--Creating Table...-->"; 
     			
     			$SQLstring = "CREATE TABLE $TableName (project_name VARCHAR (60), project_ID INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (project_ID));"; 
     			$QueryResult = @mysql_query($SQLstring, $DBConnect); 
     			
     			if ($QueryResult === FALSE) 
     				$Body .= "<p>Unable to create the table.</p>" . "<p>Error Code " . mysql_errno($DBConnect) . ": " . mysql_error($DBConnect) . "</p>";
     		} 


	     	//Checking for the Item Table, which uses a foreign key (Project Name) to relate itmes to their project	     		
     		$SecondTableName = "Items"; 
     		$SQLstring = "SHOW TABLES LIKE '$SecondTableName'"; 
     		$QueryResult = @mysql_query($SQLstring, $DBConnect); 
     		
     		if(mysql_num_rows($QueryResult) == 0) { 
     			//for the debugging
     			$Body .= "<!--Creating Table...-->"; 
     			

     			$SQLstring = "CREATE TABLE $SecondTableName (item_number INT NOT NULL AUTO_INCREMENT, item VARCHAR (1000), project_ID INT NOT NULL, PRIMARY KEY (item_number), FOREIGN KEY (project_ID) REFERENCES Project(project_ID));"; 
     			$QueryResult = @mysql_query($SQLstring, $DBConnect); 
     			
     			if ($QueryResult === FALSE) 
     				$Body .= "<p>Unable to create the table.</p>" . "<p>Error Code " . mysql_errno($DBConnect) . ": " . mysql_error($DBConnect) . "</p>";
     		} 
     		
     			//Check to see if the project already exists in the database
	 			 $SQLstring = "SELECT * FROM $TableName WHERE project_name='$project';";
	 			 $QueryResult = @mysql_query($SQLstring, $DBConnect); 
	 			 $num_rows = mysql_num_rows($QueryResult);
	 			 
	 			 if ($num_rows == 0) {
		 			 $SQLstring = "INSERT INTO $TableName (project_name) VALUES('$project');"; 
		 			 $QueryResult = @mysql_query($SQLstring, $DBConnect); 
		 			 $Body .= "The project wasn't in there. I've added it";
		 			 $ProjectID = @mysql_insert_id();
/* 		 			 echo $ProjectID; */

	 			 }
	 			 
	 			 
	 			 else {
		 			 $SQLstring = "SELECT project_id FROM $TableName WHERE project_name='$project';";
		 			 $QueryResult = @mysql_query($SQLstring, $DBConnect); 
		 			 $ProjectID = mysql_result($QueryResult, 0);
		 			 $Body .= "The project already existed!";
/* 		 			 echo $ProjectID; */

	 			 }


	 			//Get the correct project ID to assign to each To Do item	 			 			 
	 			 if($QueryResult === FALSE) {
	 			 	$Body .= "<p>Unable to execute the query</p>" . "<p>Error Code " . mysql_errno($DBConnect) . ": " . mysql_error($DBConnect) . "</p>"; 
	 			 	}
	 			 
	 			 $SQLstring = "INSERT INTO $SecondTableName (item, project_ID) VALUES('$toDo', '$ProjectID');";
	 			 
	 			 $QueryResult = @mysql_query($SQLstring, $DBConnect); 
	 			 
	 			 if($QueryResult === FALSE) {
	 			 	$Body .= "<p>Unable to enter the item to your to do list.</p>" . "<p>Error Code " . mysql_errno($DBConnect) . ": " . mysql_error($DBConnect) . "</p>"; 
	 			 	}
	     		 else {
	     			 $Body .= "<p><a class='button' href='todolist.php?PHPSESSID=" . session_id() . "'>What do you still need to do?</a></p>";
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
		<h1>Dos are for Doing</h1>
		<div class="to_do">
		<?php 
			echo $Body;
		?>
		</div>
	</body>
</html>

