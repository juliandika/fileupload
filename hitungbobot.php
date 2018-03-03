<?php


	include 'connect.php';

	mysqli_query($conn, "TRUNCATE TABLE tbindex");
	$resn = "INSERT INTO tbindex (term, docid, count) SELECT tokenstem,nama_file,count(*) FROM dokumen group by nama_file,tokenstem";

	if ($conn->query($resn) === TRUE) {
    echo "New record created successfully";
	} else {
    echo "Error: ";

	}

	$n = mysqli_num_rows($resn);
	
	$resn = mysqli_query($conn, "SELECT DISTINCT docid FROM tbindex");
	$n = mysqli_num_rows($resn);

	$resBobot = mysqli_query($conn, "SELECT * FROM tbindex ORDER BY Id");
	$num_rows = mysqli_num_rows($resBobot);
	print("Terdapat " . $num_rows . " Term yang diberikan bobot. <br />");

	while($rowbobot = mysqli_fetch_array($resBobot)) {
		
		$term = $rowbobot['term'];		
		$tf = $rowbobot['count'];
		$id = $rowbobot['id'];
		
		
		$resNTerm = mysqli_query($conn, "SELECT Count(*) as N FROM tbindex WHERE Term = '$term'");
		$rowNTerm = mysqli_fetch_array($resNTerm);
		$NTerm = $rowNTerm['N'];
		
		$w = $tf * log($n/$NTerm);
		
		$resUpdateBobot = mysqli_query($conn, "UPDATE tbindex SET Bobot = $w WHERE Id = $id");		
  	}

?>