<?php	
	$jumlahmirip = 0;
	
	//$resDocId = mysqli_query($conn, "SELECT * FROM tbvektor ORDER BY docid");


	for ($i=0; $i<count($vektor); $i++) {
	
		$dotproduct1 = 0;
		$dotproduct2 = 0;
			
		//$docId = $rowDocId['docid'];
		//$panjangDocId = $rowDocId['panjang'];
		
		//$resTerm = mysqli_query($conn, "SELECT * FROM tbindex WHERE docid = '$docId'");
		
		for ($j=0; $j<count($index); $i++) {


			for ($i=0; $i<count($aquery); $i++) {
				//'<pre>'; print_r($aquery); echo '</pre>';


				if($adaTerm[$i] == 1){
					//echo '<pre>'; print_r($aBobotQuery); echo '</pre>';

					if ($index[$j]['term'] == $aquery[$i]) {

						/*echo "Term Query Awal: " . $aquery[$i] . "<br>";
						echo "Row term Query Awal pd Doc: " . $rowTerm['bobot'] . "<br>";
						echo "BobotQuery Awal pd Q : " . $aBobotQuery[$i] . "<br>";
						echo "Q * D : " . ($aBobotQuery[$i] *  $rowTerm['bobot']). "<br>";*/

						$dotproduct1 = $dotproduct1 + $index[$j]['bobot'] * $aBobotQuery[$i];

						/*echo "Dot product 1 : " . $dotproduct1 . "<br><br>";*/
						

					}

					if(($adaSinonim[$i] == 1) && ($adaTerm[$i] == 1)){

						for ($j=0; $j<count($asinonim); $j++) {

							if ($index[$i]['term'] == $asinonim[$j]) {

								$dotproduct2 = $dotproduct2 + $index[$j]['bobot'] * $aBobotSinonim[$j];

								/*echo "Term Sinonim: " . $asinonim[$j] . "<br>";
								echo "Row term Sinonim pd Doc: " . $rowTerm['bobot'] . "<br>";
								echo "BobotSinonim pd Q: " . $aBobotSinonim[$j] . "<br>";
								echo "Q * D : " . ($aBobotSinonim[$j] *  $rowTerm['bobot']). "<br>";
								echo "Dot product 2 : " . $dotproduct2 . "<br>";
								
								echo "Query exp " . $dotproduct . "<br>";*/
								
								} //endif	
							}
						} //endfor	
					} //endif
				}
			}

		if (($dotproduct1 != 0) || ($dotproduct2 != 0)) {

			//echo "Dot product1 total = " . $dotproduct1 . "<br>";
			//echo "Dot product2  total = " . $dotproduct2 . "<br>";

			$sim = ($dotproduct1 + $dotproduct2) / ($panjangQueryTotal * $vektor[$i]['panjang']);

			//echo "Panjang query total : "  . $panjangQueryTotal . "<br>";
			//echo "Dot product total : "  . ($dotproduct1 + $dotproduct2) . "<br>";
			//echo "Query * Bobot DocId total : "  . ($panjangQueryTotal * $panjangDocId) . "<br>";
			//echo "Sim = " . $sim  . "<br>";

			//echo "<br>";
			//echo "<hr>";
			$result[] = array($query,$docId,$sim);
			$jumlahmirip++;
		} 
			
	if ($jumlahmirip == 0) {
		$result[] = array($query,0,0);
		}
	} 

	$end4 = microtime(true);

	echo '<br><strong>Menghitung Dot product: </strong>', $end4 - $start4, ' microsecond<br>';


	mysqli_query($conn, "TRUNCATE TABLE tbcache_copy");

	$data = array();
	foreach($result as $row) {
	    $query = mysqli_real_escape_string($conn, $row[0]);
	    $docId = mysqli_real_escape_string($conn, $row[1]);
	    $sim = (float) $row[2];
	    $data[] = "('$query', '$docId', $sim)";
	}

	$values = implode(',', $data);

	$sql = "INSERT INTO tbcache (query, docid, nilai) VALUES $values";

	$conn->query($sql);

















$result = mysqli_query($conn, "SELECT DISTINCT judul, nama, nama_jurusan, label, semua.doc AS nama_doc, nilai FROM semua INNER JOIN tbcache ON semua.doc = tbcache.docid ORDER BY nilai DESC");

echo "<table border=1>";
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

$end5 = microtime(true);


echo '<br><strong>Total waktu: </strong>', $end5 - $start5, ' microsecond<br>';

?>