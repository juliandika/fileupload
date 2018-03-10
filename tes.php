<?php


include 'connect.php';

$sql =  "SELECT * FROM tbvektor";

$res = $conn->query($sql);

if($res->num_rows > 0){

	while($row = $res->fetch_assoc()){
		echo $row["docid"];
	}
}



?>



		/*echo $aquery[$i];

		

		while($row = $res->fetch_assoc() > 0){
	            echo $row["sinonim"];

	            $sinonim = $row["sinonim"];

	            $asinonim = explode(",", $sinonim);

	            echo "Sinonim " . '<pre>'; print_r($asinonim); echo '</pre>';

		}*/

		//while($row = $res->fetch_assoc() > 0){
			
		//}
		