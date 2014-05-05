<?php
     session_start();
     $_SESSION = array();
     
     //ENTER YOUR DATABASE INFO BELOW:
     ////////////////////////////
     $DatabaseName = "localhost";
     $DatabaseUser = "root";
     $DatabasePassword = "root";
     ////////////////////////////
     ////////////////////////////
     
     //Convert Database info to Session Variables
     $_SESSION['databaseName'] = $DatabaseName;
     $_SESSION['databaseUser'] = $DatabaseUser;
     $_SESSION['databasePassword'] = $DatabasePassword;
     
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
     		$SQLstring = "SELECT project_name FROM $TableName;"; 
     		$QueryResult = @mysql_query($SQLstring, $DBConnect); 
     		$num_rows = mysql_num_rows($QueryResult);
	 			 		
 			if ($num_rows == 0) {
	 			$Body .= "TO DO: Add things to your to do list.";
	 		}

	 		else {
	 			//Echo out Project Names that are already in the database in radio buttons
	 			for ($i = 0; $i < $num_rows; ++$i) {
	 				$row = mysql_fetch_assoc($QueryResult);
	 				$ProjectName = $row['project_name'];
			 		$Body .= "<li><input type='radio' name='project' value='$ProjectName'> " . $ProjectName . "</li>";
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
<title>To Do</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<link href='http://fonts.googleapis.com/css?family=Homemade+Apple' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Raleway:500' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css.css">

</head>
	<body>
		
			<h1>Dos are for Doing</h1>
<div class="to_do">
			<h3>Add a Do</h3>
			<form method="POST" action="<?php echo "submit.php?PHPSESSID=" . session_id();?>" id="to_do_form">
				<p>Which Project?</p>
					<?php //FOREACH loop that gets table names from DATABASE and displays them as li radio buttons ?>
				<div class="radio_btns">	
					<ul class="clearfix">
						<?php echo $Body; ?>
					</ul>
				</div>	
				<p>Add a new project: <input type="text" name="new_project"></p>
				<textarea name="toDo"> </textarea>
				<input type="submit" class="btn" name="submit" value="Next" />
				<input type="reset" class="btn" name="reset" value="Start Over" />
			</form>
		</div>
	</body>
</html>

