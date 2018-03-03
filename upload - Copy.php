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
        
     }

    $string = (string) new PdfToText ("uploads/" . basename( $_FILES['file']['name'])) ;

    echo '<b>Teks Sebelum Preprocessing</b><br>';

    //echo $string;

    echo '<br>';

    $teks = preg_replace('/[^a-zA-Z -]/', '', $string);

    $teks2 = preg_replace('/\b\w\b\s?/', '', $teks);

    $teks3 = preg_replace('/\s\s+/', ' ', $teks2);

    $teks4 = preg_replace('/[-\n\r]/', ' ', $teks3);


    $teks5 = strtolower($teks4);

    echo $teks5;

    $teks6 = explode(" ",$teks5);

    echo $teks6;

    //var_dump($teks5);

    $remove_stopword = "SELECT * FROM stopwords";

    $hasil = $conn->query($remove_stopword);

    if($hasil->num_rows > 0){

        while($row = $hasil->fetch_array()) {

            $stopword[] = $row['stopword'];
        }
    }
    else{

        echo "Gagal";
    }

    $teks7 = array_values(array_diff($teks6,$stopword));



    $jum_kata_unik=count($teks7);



    print_r($jum_kata_unik);
    echo '<br/> <br/>';
    print_r($teks7);

    $i=0;
    while ( $i< $jum_kata_unik) {

            require_once ('stemming.php');
            $teksAsli = $teks7[$i];

            $stemming = stemming($teksAsli);

            if ($stemming=='') {
            echo "Kata dasar : ".$teks7[$i].'<br/>';
            }
            else{
            echo "Kata dasar : ".$stemming.'<br/>';
            }
            $i++;

        }

    $teks8 = implode(" ",$teksAsli);

    echo '<b>Setelah Preprocessing</b><br>';
    echo $teks8;

    $frequency = array_count_values($teks8);

    arsort($frequency);

    echo '<pre>';
    print_r($frequency);
    echo '</pre>';



    $sql = "INSERT INTO upload (teks) VALUES ('$teks8')";

    $conn->query($sql);


?>