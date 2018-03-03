<?php


function hitungsim($query) {

	include 'connect.php';


	echo 'jkajdkaja';

	$resn = mysqli_query($conn, "SELECT Count(*) as n FROM tbvektor");
	$rown = mysqli_fetch_array($resn);	
	$n = $rown['n'];
	
	print_r($resn);
	
	$aquery = explode(" ", $query);
	
	$panjangQuery = 0;
	$aBobotQuery = array();
	
	for ($i=0; $i<count($aquery); $i++) {

		$resNTerm = mysqli_query($conn, "SELECT Count(*) as N from tbindex WHERE Term like '%$aquery[$i]%'");

		$rowNTerm = mysqli_fetch_array($resNTerm);	
		$NTerm = $rowNTerm['N'] ;
		
		$idf = log($n/$NTerm);

		$aBobotQuery[] = $idf;
		
		$panjangQuery = $panjangQuery + $idf * $idf;		
	}
	
	$panjangQuery = sqrt($panjangQuery);
	
	$jumlahmirip = 0;
	
	$resDocId = mysqli_query($conn, "SELECT * FROM tbvektor ORDER BY docid");
	while ($rowDocId = mysqli_fetch_array($resDocId)) {
	
		$dotproduct = 0;
			
		$docId = $rowDocId['docid'];
		$panjangDocId = $rowDocId['panjang'];
		
		$resTerm = mysqli_query($conn, "SELECT * FROM tbindex WHERE DocId = '$docId'");

		
		
		while ($rowTerm = mysqli_fetch_array($resTerm)) {
			for ($i=0; $i<count($aquery); $i++) {

				if ($rowTerm['Term'] == $aquery[$i]) {
					$dotproduct = $dotproduct + $rowTerm['Bobot'] * $aBobotQuery[$i];		
					
				}
					else
					{
					}
			}		
		}
		
		if ($dotproduct != 0) {
			$sim = $dotproduct / ($panjangQuery * $panjangDocId);	
			
			$resInsertCache = mysqli_query($conn, "INSERT INTO tbcache (query, docid, value) VALUES ('$query', '$docId', $sim)");
			$jumlahmirip++;
		} 
			
	if ($jumlahmirip == 0) {
		$resInsertCache = mysqli_query($conn, "INSERT INTO tbcache (Query, DocId, Value) VALUES ('$query', 0, 0)");
	}	
	} 
	
		
}


?>