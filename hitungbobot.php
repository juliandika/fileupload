<?php


	include 'connect.php';

	mysqli_query($conn, "TRUNCATE TABLE tbindex_real_copy");
	$resn = "INSERT INTO tbindex_real_copy (term, docid, count) SELECT tokenstem,nama_file,count(*) FROM dok_real2 GROUP BY nama_file,tokenstem";

	if ($conn->query($resn) === TRUE) {

   		echo "New record created successfully";

	} else {
		
    	echo "Error: ";
	}

	$n = mysqli_num_rows($resn);
	
	$resn = mysqli_query($conn, "SELECT DISTINCT docid FROM tbindex_real_copy");
	$n = mysqli_num_rows($resn);

	$resBobot = mysqli_query($conn, "SELECT * FROM tbindex_real_copy ORDER BY Id");
	$num_rows = mysqli_num_rows($resBobot);
	print("Terdapat " . $num_rows . " Term yang diberikan bobot. <br />");

	while($rowbobot = mysqli_fetch_array($resBobot)) {
		
		$term = $rowbobot['term'];		
		$tf = $rowbobot['count'];
		$id = $rowbobot['id'];
		
		$resNTerm = mysqli_query($conn, "SELECT Count(*) as N FROM tbindex_real_copy WHERE Term = '$term'");
		$rowNTerm = mysqli_fetch_array($resNTerm);
		$NTerm = $rowNTerm['N'];
		
		$w = $tf * log10($n/$NTerm);
		
		
		
		$resUpdateBobot = mysqli_query($conn, "UPDATE tbindex_real_copy SET Bobot = $w WHERE Id = $id");		
  	}

?>