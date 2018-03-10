<?php


	include 'connect.php';

	$search_value=$_POST["search"];
	
	if($conn->connect_error){
	    echo 'Connection Faild: '.$con->connect_error;
	    }else{
	        $sql = "SELECT * FROM sinonim WHERE kata LIKE '%$search_value%'";

	        $res = $conn->query($sql);

	        while($row=$res->fetch_assoc()){
	            echo 'Sinonim:  '.$row["sinonim"];

	            $sinonim = $row["sinonim"];


	            $result = explode(",", $sinonim);

	            $result2 = explode(" ", $result);

	            echo '<pre>'; print_r($result2); echo '</pre>';

	            }       

	        }
?>