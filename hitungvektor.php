<?php

include 'connect.php';

mysqli_query($conn, "TRUNCATE TABLE tbvektor");

$resDocId = mysqli_query($conn, "SELECT DISTINCT docid FROM tbindex");

$num_rows = mysqli_num_rows($resDocId);


print("Terdapat " . $num_rows . " dokumen yang dihitung panjang vektornya. <br />");

while($rowDocId = mysqli_fetch_array($resDocId)) {

	$docId = $rowDocId['docid'];

	$resVektor = mysqli_query($conn, "SELECT bobot FROM tbindex WHERE docid = '$docId'");
	
	$panjangVektor = 0;		
	while($rowVektor = mysqli_fetch_array($resVektor)) {
		$panjangVektor = $panjangVektor + $rowVektor['bobot']  *  $rowVektor['bobot'];	
	}
	
	$panjangVektor = sqrt($panjangVektor);
			
	$resInsertVektor = mysqli_query($conn, "INSERT INTO tbvektor (docId, panjang) VALUES ('$docId', $panjangVektor)");	
}


?>