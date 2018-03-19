<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');
include 'connect.php';

$sql = mysqli_query($conn, "SELECT * FROM semua WHERE id <= 20");

while($row = mysqli_fetch_array($sql)) {
 	
 	//echo $row['clean_teks'];

 	$arr = $row['clean_teks'];

 	$nama_file = $row['doc'];

 	$filter = explode(" ",$arr);

 	print_r($filter);

 	for($i=0; $i<count($filter); $i++){

 		if(!empty($filter[$i]) && strlen($filter[$i]) > 2){

 			echo $filter[$i]. "<br>";

            mysqli_query($conn, "INSERT INTO dok_copy (nama_file, tokenstem) VALUES('".$nama_file."', '".$filter[$i]."')");
            

        }

              
    }

}





?>