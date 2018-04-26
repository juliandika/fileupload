<?php
	

	$start5 = microtime(true);

	ini_set('mysql.connect_timeout', 300);
	ini_set('default_socket_timeout', 300);

	include 'connect.php';

	$index = array();
	$term = array();
	$tf = array();
	$w = array();
	$bobot = array();

	mysqli_query($conn, "TRUNCATE TABLE tbindex_real");
	$resn = "INSERT INTO tbindex_real (term, docid, freq) SELECT tokenstem,nama_file,count(*) FROM dok_real GROUP BY nama_file,tokenstem";

	if ($conn->query($resn) === TRUE) {

   		echo "New record created successfully";

	} else {
		
    	echo "Error: ";
	}

	

	$n = mysqli_num_rows($resn);
	
	$resn = mysqli_query($conn, "SELECT DISTINCT docid FROM tbindex_real");

	$n = mysqli_num_rows($resn);



	$sql3 = mysqli_query($conn, "SELECT * FROM tbindex_real");

	while($row = mysqli_fetch_array($sql3)){

	 	  $term[] = $row['term'];

	 	  $index[] = array('docid'=>$row['docid'], 'tf' => $row['freq'], 'term' => $row['term'], 'bobot' => $row['bobot']);

	}

	for($i=0; $i<count($term); $i++){

		echo $i . "<br>";

		  $df = array_count_values($term);

		  $index[$i]['bobot'] = $index[$i]['tf'] * log10($n/$df[$index[$i]['term']]);


		  $bobot[] = array($index[$i]['term'],$index[$i]['docid'],$index[$i]['tf'],$index[$i]['bobot']);

	}


	echo "<pre>";
	print_r($bobot);
	echo "</pre>";

	mysqli_query($conn, "TRUNCATE TABLE tbindex_real");

	$data = array();
	foreach($bobot as $row) {
	    $term = mysqli_real_escape_string($conn, $row[0]);
	    $docid = mysqli_real_escape_string($conn, $row[1]);
	    $tf = (int) $row[2];
	    $bobot = (float) $row[3];
	    $data[] = "('$term', '$docid', $tf, $bobot)";
	}

	$values = implode(',', $data);

	$sql = "INSERT INTO tb_index (term, docid, freq, bobot) VALUES $values";

	echo $sql;

	$conn->query($sql);


	$end5 = microtime(true);

	echo '<br><strong>Total waktu: </strong>', $end5 - $start5, ' microsecond<br>';
  	

?>