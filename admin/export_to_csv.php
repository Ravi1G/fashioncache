<?php

// Database Connection
require_once("../inc/config.inc.php");

if(isset($_POST['query']) && $_POST['query']!="")
{
	// Fetch Record from Database
	$query = $_POST['query'];
	$file_name = $_POST['file_name'];
	$output = "";
 
	$sql = mysql_query("$query");
	$columns_total = mysql_num_fields($sql);
	
	// Get The Field Name
	
	for ($i = 0; $i < $columns_total; $i++) {
	$heading = mysql_field_name($sql, $i);
	$output .= '"'.$heading.'",';
	}
	$output .="\n";
	
	// Get Records from the table
	
	while ($row = mysql_fetch_array($sql)) {
	for ($i = 0; $i < $columns_total; $i++) {
	$output .='"'.$row["$i"].'",';
	}
	$output .="\n";
	}
	
	// Download the file
	
	$filename = $file_name;
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename='.$filename);
	
	echo $output;
	exit;		
}
?>