<?php

include 'connect.php';

$query = $_GET['keyword'];

mysqli_query($conn, "TRUNCATE TABLE tbcache");

function hitungsim($query) {

	include 'connect.php';

	$sql = "SELECT Count(*) as n FROM tbvektor";

	$resn = $conn->query($sql);

	$rown = mysqli_fetch_array($resn);
	$n = $rown['n'];
	
	$aquery = explode(" ", $query);
	
	$panjangQuery = 0;
	$aBobotQuery = array();
	
	for ($i=0; $i<count($aquery); $i++) {


		$sql2 = "SELECT Count(*) as N from tbindex WHERE term like '$aquery[$i]'";

		$resNTerm = $conn->query($sql2);

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
		
		$resTerm = mysqli_query($conn, "SELECT * FROM tbindex WHERE docid = '$docId'");
		
		while ($rowTerm = mysqli_fetch_array($resTerm)) {
			for ($i=0; $i<count($aquery); $i++) {

				if ($rowTerm['term'] == $aquery[$i]) {
					$dotproduct = $dotproduct + $rowTerm['bobot'] * $aBobotQuery[$i];		
					
				}
					else
					{
					}
			}		
		}
		
		
		if ($dotproduct != 0) {
			$sim = $dotproduct / ($panjangQuery * $panjangDocId);

			$resInsertCache = mysqli_query($conn, "INSERT INTO tbcache (query, docid, nilai) VALUES ('$query', '$docId', $sim)");
			$jumlahmirip++;
		} 
			
	if ($jumlahmirip == 0) {
		$resInsertCache = mysqli_query($conn, "INSERT INTO tbcache (query, docid, nilai) VALUES ('$query', 0, 0)");
		}	}
		
}

hitungsim($query);

echo $query;

$result = mysqli_query($conn, "SELECT judul, nama, nama_jurusan, semua.doc AS nama_doc, nilai FROM semua INNER JOIN tbcache ON semua.doc = tbcache.docid ORDER BY nilai DESC");

echo "<table>";
echo "<tr>";
echo "<th>"; echo "Judul"; echo "</th>";
echo "<th>"; echo "Nama"; echo "</th>";
echo "<th>"; echo "Nama Jurusan"; echo "</th>";
echo "<th>"; echo "Nama Dokumen"; echo "</th>";
echo "<th>"; echo "Bobot"; echo "</th>";
echo "</tr>";

while($row = mysqli_fetch_array($result)){

    echo "<tr>";
    echo "<td>"; echo $row["judul"]; echo "</td>";
    echo "<td>"; echo $row["nama"]; echo "</td>";
    echo "<td>"; echo $row["nama_jurusan"]; echo "</td>";
    echo "<td>"; echo $row["nama_doc"]; echo "</td>";
    echo "<td>"; echo $row["nilai"]; echo "</td>";
    echo "</tr>";

}
echo "</table>";

?>