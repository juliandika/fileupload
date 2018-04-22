<?php

include 'connect.php';

ini_set('mysql.connect_timeout', 300);
ini_set('default_socket_timeout', 300);

mysqli_query($conn, "TRUNCATE TABLE tbvektor");

$resDocId = mysqli_query($conn, "SELECT DISTINCT docid FROM tbindex");

$sql = mysqli_query($conn, "SELECT * FROM tbindex");

$num_rows = mysqli_num_rows($resDocId);

$docid = array();
$doc = array();
$length = array();
$data = array();

print("Terdapat " . $num_rows . " dokumen yang dihitung panjang vektornya. <br />");

while($row = mysqli_fetch_array($resDocId)){

	 	  $doc[] = $row['docid'];

}


while($row = mysqli_fetch_array($sql)){

	 	  
	 	  $docid[] = array('doc'=>$row['docid'], 'bobot' => $row['bobot']);


}

$panjang = 0;

for($i=0; $i<count($doc); $i++){

	for($j=0; $j<count($docid); $j++){

			if($doc[$i] == $docid[$j]['doc']){

				$bobot =  $docid[$j]['bobot'];

				$panjang = $panjang + $bobot * $bobot;

			}
			
	}

	$document = $doc[$i];
	$sqrt = sqrt($panjang);
	$length[] = array($document, $sqrt);
	$panjang = 0;

}

	echo "<pre>";
	print_r($length);
	echo "</pre>";

	mysqli_query($conn, "TRUNCATE TABLE tbvektor");

	$data = array();
	foreach($length as $row) {
	    $document = mysqli_real_escape_string($conn, $row[0]);
	    $sqrt = (float) $row[1];
	    $data[] = "('$document', $sqrt)";
	}


	$values = implode(',', $data);

	$sql = "INSERT INTO tbvektor (docid, panjang) VALUES $values";

	echo $sql;

	$conn->query($sql);



?>