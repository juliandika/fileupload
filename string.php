<?php

include 'connect.php';

function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);

	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	$result =  substr($string,$ini,$len);


	return $result2 = implode(' ', array_slice(explode(' ', $result), 0, 200));

}


$label1 = "Daftar Isi, Abstrak, BAB I";
$label2 = "Cover";


$sql = mysqli_query($conn, "SELECT * FROM semua WHERE label = '".$label1."'");


echo "<table border=1>";
echo "<tr>";
echo "<th>"; echo "Nama Jurusan"; echo "</th>";
echo "<th>"; echo "Label"; echo "</th>";
echo "<th>"; echo "Parsed"; echo "</th>";
echo "</tr>";

while($row = mysqli_fetch_array($sql)) {

	$fullstring = $row['clean_teks'];

	$parsed = get_string_between($fullstring, "nim ", " bab ");

	echo "<tr>";
    echo "<td>"; echo $row["nama_jurusan"]; echo "</td>";
    echo "<td>"; echo $row["label"]; echo "</td>";
    echo "<td>"; echo $parsed; echo "</td>";
    echo "</tr>";


}

echo "</table>";


?>