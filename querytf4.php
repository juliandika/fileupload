<?php

$start5 = microtime(true);
error_reporting(0);
include 'connect.php';

$query = $_GET['keyword'];

//$query = implode(' ', array_unique(explode(' ', $raw_query)));
//mysqli_query($conn, "TRUNCATE TABLE tbcache");
mysqli_query($conn, "TRUNCATE TABLE tbcache");

function hitungsim($query) {

	include 'connect.php';

	$result = array();

	$sql = "SELECT Count(*) as n FROM tbvektor";

	$resn = $conn->query($sql);

	$rown = mysqli_fetch_array($resn);
	$n = $rown['n'];
	
	$aquery = explode(" ", $query);
	
	$panjangQuery = 0;
	$panjangQuerySinonim = 0;
	$aBobotQuery = array();
	$aBobotSinonim = array();
	$adaSinonim = array();
	$adaTerm = array();
	$query = array();
	$index = array();
	$vektor = array();
	$querytotal = array();
	$totalSinonim = array();

	echo "Query: <br>";
	echo '<pre>'; print_r($aquery); echo '</pre>';
	
	for ($i=0; $i<count($aquery); $i++) {

		$start1 = microtime(true);

		//$sql2 = "SELECT Count(*) as N from tbindex WHERE term like '$aquery[$i]'";

		$sql3 = mysqli_query($conn, "SELECT * FROM tbindex WHERE term like '$aquery[$i]' LIMIT 1");

		if(mysqli_num_rows($sql3) > 0){

			while($row = mysqli_fetch_array($sql3)){

				  // OR just echo the data:

				  $idf = $row['bobot'] / $row['freq'];

				  echo "Bobot: " . $idf . "<br><br>";

				  
				  $aBobotQuery[] = $idf;

				  $query[] = $row['term'];

				  echo "Bobot query" . "<br>";
				  echo "<pre>";
				  print_r($aBobotQuery);
				  echo "</pre>";

				  echo "Query" . "<br>";
				  echo "<pre>";
				  print_r($query);
				  echo "</pre>";

				  $panjangQuery = $panjangQuery + $idf * $idf;

				  echo "panjang query" . $panjangQuery;

				  
				  $AdaTerm = 1;
			}



		}

	} //endfor





	//echo "Panjang Query Awal = " . $panjangQuery . "<br>";

	//echo "Ada term: ";

	//'<pre>'; print_r($adaTerm); echo '</pre>';

	//echo "<br>";
	
	//echo "Ada sinonim: ";

	//'<pre>'; print_r($adaSinonim); echo '</pre>';

	//echo "<br>";

	$start4 = microtime(true);

	$panjangQueryTotal = sqrt($panjangQuery);

	echo "Panjang query total: " . $panjangQueryTotal . "<br>";

	echo "Query awal: ";
	echo "<pre>";
	print_r($query);
	echo "</pre>";


	for ($i=0; $i<count($query); $i++) {


		$sql4 = mysqli_query($conn, "SELECT * FROM tbindex WHERE term like '$query[$i]'");

		while($row = mysqli_fetch_array($sql4)){

		  // add each row returned into an array
		  $index[] = $row;

		  // OR just echo the data:

		}
	}

	

	for ($i=0; $i<count($index); $i++) {

		$sql5 = mysqli_query($conn, "SELECT * FROM tbvektor WHERE docid = '".$index[$i]['docid']."'");

		while($row = mysqli_fetch_array($sql5)){

		  // add each row returned into an array
		  $vektor[] = $row;

		  $vektor = array_map("unserialize", array_unique(array_map("serialize", $vektor)));

		  // OR just echo the data:

		}
	}

	$jumlahmirip = 0;


	for ($i=0; $i<count($vektor); $i++) {

		$dotproduct = 0;

		for ($j=0; $j<count($index); $j++) {

			for ($k=0; $k<count($query); $k++) {
				//'<pre>'; print_r($aquery); echo '</pre>';

				//if($adaTerm[$k] == 1){

					if (($index[$j]['term'] == $query[$k]) && ($index[$j]['docid'] == $vektor[$i]['docid'])) {

						echo "Query awal";

						echo "Row term Query Awal pd pada Doc: " . $vektor[$i]['docid'] . "<br>";
						echo "Row term Query Awal pd pada Index: " . $index[$j]['term'] . "<br>";
						echo "Row term Query Awal pd Query: " . $query[$k] . "<br>";
						echo "Row term Query Awal pd Doc: " . $index[$j]['bobot'] . "<br>";
						echo "BobotQuery Awal pd Q : " . $aBobotQuery[$k] . "<br>";

						$dotproduct = $dotproduct + $aBobotQuery[$k] * $index[$j]['bobot'];

						echo "Dot product 1 : " . $dotproduct . "<br><br>";

					}
				//} 

			}//paste here

			//if(($adaSinonim[$k] == 1) && ($adaTerm[$k] == 1)){
			//}

		}

		if ($dotproduct != 0) {


			$sim = $dotproduct / ($panjangQueryTotal * $vektor[$i]['panjang']);

			echo "Panjang query total : "  . $panjangQueryTotal . "<br>";
			echo "Dot product total : "  . ($dotproduct) . "<br>";
			echo "Query * Bobot DocId total : "  . ($panjangQueryTotal * $vektor[$i]['panjang']) . "<br>";
			echo "Sim = " . $sim  . "<br>";

			//echo "<br>";
			//echo "<hr>";

			$result[] = array($query,$vektor[$i]['docid'],$sim);

			$jumlahmirip++;


			$docId = $vektor[$i]['docid'];

			echo "<hr>";
		}


	if ($jumlahmirip == 0) {
		$result[] = array($query,0,0);
		}
	}



	mysqli_query($conn, "TRUNCATE TABLE tbcache");

	$data = array();
	foreach($result as $row) {
	    $docId = mysqli_real_escape_string($conn, $row[1]);
	    $sim = (float) $row[2];
	    $data[] = "('$docId', $sim)";
	}

	$values = implode(',', $data);

	$sql = "INSERT INTO tbcache (docid, nilai) VALUES $values";

	$conn->query($sql);

	echo $sql;
	//echo "Panjang query total  = " . $panjangQueryTotal . "<br><br>";

}

hitungsim($query);


$result = mysqli_query($conn, "SELECT tbcache.id AS id, judul, nama, nama_jurusan, label, semua.doc AS nama_doc, nilai FROM semua INNER JOIN tbcache ON semua.doc = tbcache.docid ORDER BY nilai DESC");

echo "<table border=1>";
echo "<tr>";
echo "<th>"; echo "No"; echo "</th>";
echo "<th>"; echo "Judul"; echo "</th>";
echo "<th>"; echo "Nama"; echo "</th>";
echo "<th>"; echo "Nama Jurusan"; echo "</th>";
echo "<th>"; echo "Label"; echo "</th>";
echo "<th>"; echo "Nama Dokumen"; echo "</th>";
echo "<th>"; echo "Bobot"; echo "</th>";
echo "</tr>";

while($row = mysqli_fetch_array($result)){

    echo "<tr>";
    echo "<td>"; echo $row["id"]; echo "</td>";
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