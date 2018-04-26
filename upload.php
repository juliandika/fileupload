<?php

    error_reporting(0);

    include ( 'uploads/PdfToText.phpclass' ) ;
    include 'connect.php';
    require_once ('stemming.php');

    $targetfolder = "uploads/";

    $targetfolder = $targetfolder . basename( $_FILES['file']['name']) ;

    if(move_uploaded_file($_FILES['file']['tmp_name'], $targetfolder))

    {

       // echo "The file ". basename( $_FILES['file']['name']). " is uploaded \n";
        
    }else{

        echo 'Gagal';
    }

    $string = (string) new PdfToText ("uploads/" . basename( $_FILES['file']['name'])) ;

    $nama_file = basename( $_FILES['file']['name']);

    echo basename( $_FILES['file']['name']);

    echo '<br>';

    /*$teks = preg_replace('/[^a-zA-Z -]/', '', $string);

    $teks2 = preg_replace('/\b\w\b\s?/', '', $teks);

    $teks3 = preg_replace('/\s\s+/', ' ', $teks2);

    $teks4 = preg_replace('/[-\n\r]/', ' ', $teks3);*/

    echo "<br><br>" . $string . "<br><br>";

    $teks = preg_replace('/[^a-zA-Z -]/', '', $string);

    echo "<br><br>" . $teks . "<br><br>";

    $teks4 = preg_replace('/[-\n\r]/', ' ', $teks);

    echo  "<br><br>" . $teks4 . "<br><br>";

    $teks5 = strtolower($teks4);

    echo "<br><br>" . $teks5 . "<br><br>";

    $teks6 = explode(" ",$teks5);

    //var_dump($teks5);

    $remove_stopword = "SELECT * FROM tb_stopwords";

    $hasil = $conn->query($remove_stopword);

    if($hasil->num_rows > 0){

        while($row = $hasil->fetch_array()) {

            $stopword[] = $row['stopword'];
        }
    }
    else{

        echo "Gagal";
    }


    $filter = array_diff($teks6,$stopword);

    print_r($filter);

    for($i=0; $i<count($filter); $i++){

        if(!empty($filter[$i]) && strlen($filter[$i]) > 2){

            mysqli_query($conn, "INSERT INTO dok2_copy (nama_file, tokenstem) VALUES('".$nama_file."', '".$filter[$i]."')");

            $strlen = strlen($filter[$i]);

            echo $strlen
        }

              
    }

?>