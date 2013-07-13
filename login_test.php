<?php 
include "functions/db_functions.php";
include "functions/user.class.php";
(new Database())->connect();
if(!Database::$default){
	die("Failed to connect to database.");
}
User::generateLoginSession("guuvFFNCwBCphTfh", true);
?>