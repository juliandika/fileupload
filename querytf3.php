<?php

include 'connect.php';

$query = $_GET['keyword'];

mysqli_query($conn, "TRUNCATE TABLE tbcache");

function hitungsim($query) {

	include 'connect.php';

	$sql = "SELECT Count(*) as n FROM tbvektor_copy";

	$resn = $conn->query($sql);

	$rown = mysqli_fetch_array($resn);
	$n = $rown['n'];
	
	$aquery = explode(" ", $query);
	
	$panjangQuery = 0;
	$panjangQuerySinonim = 0;
	$aBobotQuery = array();
	$aBobotSinonim = array();
	$adaSinonim = array();

	echo '<pre>'; print_r($aquery); echo '</pre>';
	
	for ($i=0; $i<count($aquery); $i++) {

		$sql2 = "SELECT Count(*) as N from tbindex_copy WHERE term like '$aquery[$i]'";       

		$resNTerm = $conn->query($sql2);

		$rowNTerm = mysqli_fetch_array($resNTerm);

		echo '<pre>'; print_r($rowNTerm); echo '</pre>';

		echo $n;

		$NTerm = $rowNTerm['N'];

		echo '<br>NTerm = ' . $NTerm . '<br>';

		if($NTerm > 0) {

			$idf = log($n/$NTerm);

			echo "IDF = " . $idf . '<br>';

			echo "Jumlah Dokumen = " . $n . '<br>';

			echo "Jumlah Term  " . $aquery[$i] . " = " . $NTerm . '<br>';

			$aBobotQuery[] = $idf;

			echo '<pre>'; print_r($aBobotQuery); echo '</pre>';

			$panjangQuery = $panjangQuery + $idf * $idf;

			echo "Panjang query = " . $panjangQuery . "<br>";

			$sin = "SELECT * FROM sinonim WHERE kata LIKE '$aquery[$i]'";
			
	    	$res = $conn->query($sin);

	   		if($res->num_rows > 0){

				while($row = $res->fetch_assoc()){

					$sinonim = $row["sinonim"];

	            	$asinonim = explode(",", $sinonim);

	            	echo "Sinonim: " . '<pre>'; print_r($asinonim); echo '</pre>';

				}

				for($j=0; $j<count($asinonim); $j++){

					$adaSinonim[$i] = 1;

					echo '<pre>'; print_r($adaSinonim); echo '</pre>';

					$idf_sinonim = 0.5 * $idf;

					$aBobotSinonim[] = $idf_sinonim;

					echo "Bobot sinonim: " . $asinonim[$j] . '<pre>'; print_r($aBobotSinonim); echo '</pre>';

					$panjangQuerySinonim = $panjangQuerySinonim + $idf_sinonim * $idf_sinonim;

					echo "Panjang Query Sinonim = " . $panjangQuerySinonim . "<br>";

				}
			}
			else
			{

				$adaSinonim[$i] = 0;
			}

		
			//echo $panjangQuery . "<br>";
			//echo $panjangQuerySinonim . "<br>";
		} //endif

	} //endfor

	
	$panjangQueryTotal = sqrt($panjangQuery + $panjangQuerySinonim);

	echo $panjangQueryTotal;

	print_r($adaSinonim);
	
	$jumlahmirip = 0;
	
	$resDocId = mysqli_query($conn, "SELECT * FROM tbvektor_copy ORDER BY docid");
	while ($rowDocId = mysqli_fetch_array($resDocId)) {
	
		$dotproduct = 0;
			
		$docId = $rowDocId['docid'];
		$panjangDocId = $rowDocId['panjang'];
		
		$resTerm = mysqli_query($conn, "SELECT * FROM tbindex_copy WHERE docid = '$docId'");
		
		while ($rowTerm = mysqli_fetch_array($resTerm)) {
			for ($i=0; $i<count($aquery); $i++) {

				if(!empty($aBobotQuery[$i])){

						if ($rowTerm['term'] == $aquery[$i]) {
							$dotproduct = $dotproduct + $rowTerm['bobot'] * $aBobotQuery[$i];

							//echo "Query" . $dotproduct . "<br>";	
							
						}

					if($adaSinonim[$i] == 1){

						for ($j=0; $j<count($asinonim); $j++) {

							if ($rowTerm['term'] == $asinonim[$j]) {

								$dotproduct = $dotproduct + $rowTerm['bobot'] * $aBobotSinonim[$j];

								//echo "Query exp " . $dotproduct . "<br>";	
								
								} //endif	
							}
						} //endfor	
					} //endif
				}
			}
		$dotproduct2 = $dotproduct;

		if ($dotproduct2 != 0) {
			$sim = $dotproduct2 / ($panjangQueryTotal * $panjangDocId);

			$resInsertCache = mysqli_query($conn, "INSERT INTO tbcache (query, docid, nilai) VALUES ('$query', '$docId', $sim)");
			$jumlahmirip++;
		} 
			
	if ($jumlahmirip == 0) {
		$resInsertCache = mysqli_query($conn, "INSERT INTO tbcache (query, docid, nilai) VALUES ('$query', 0, 0)");
		}
	}
}

hitungsim($query);

$result = mysqli_query($conn, "SELECT DISTINCT judul, nama, nama_jurusan, label, semua.doc AS nama_doc, nilai FROM semua INNER JOIN tbcache ON semua.doc = tbcache.docid ORDER BY nilai DESC");

echo "<table>";
echo "<tr>";
echo "<th>"; echo "Judul"; echo "</th>";
echo "<th>"; echo "Nama"; echo "</th>";
echo "<th>"; echo "Nama Jurusan"; echo "</th>";
echo "<th>"; echo "Label"; echo "</th>";
echo "<th>"; echo "Nama Dokumen"; echo "</th>";
echo "<th>"; echo "Bobot"; echo "</th>";
echo "</tr>";

while($row = mysqli_fetch_array($result)){

    echo "<tr>";
    echo "<td>"; echo $row["judul"]; echo "</td>";
    echo "<td>"; echo $row["nama"]; echo "</td>";
    echo "<td>"; echo $row["nama_jurusan"]; echo "</td>";
    echo "<td>"; echo $row["label"]; echo "</td>";
    echo "<td>"; echo $row["nama_doc"]; echo "</td>";
    echo "<td>"; echo $row["nilai"]; echo "</td>";
    echo "</tr>";

}
echo "</table>";

?>