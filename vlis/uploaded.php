<?php
session_start();
var_dump($_SESSION);
unset($_SESSION['video_name']);
unset($_SESSION['upload_data']); exit;
/*session_destroy();
session_unset();
echo 'SESSION ', ' DESTROYED!!!';*/

if ($_POST['name']){
    $name = '1' . '_' . $_POST['name'];
    $total_packets = $_POST['total_packets'];
    $_SESSION['video_name'] = $name;
    
    echo json_encode(['code'=>'0', 'message'=>'Start sending packets']);
    exit;
}

if (isset($_POST['uploaded_complete']) && isset($_SESSION["video_name"])){
    $name = $_SESSION["video_name"];
    $movie_name = md5('1' . time()) . '.flv';
    $array = [];
    $conversion_code = 0;
    //encode video
    exec("ffmpeg -i $name -r 24 -s 480x360 -ar 44100 $movie_name", $array, $conversion_code);
    
    $thb_nail = md5('1' . time()) . '.png';
    $thb_array = [];
    $thb_conversion_code = 0;
    //take picture
    exec("ffmpeg -i  $name -r 1 -y -s 320x240 -vframes 1 $thb_nail", $thb_array, $thb_conversion_code);    
    unlink($name);
    unset($_SESSION['video_name']);
    echo json_encode(['code'=>'0', 'message'=>'Conversion complete']);
    exit;
}


if (isset($_SESSION["video_name"])){
    $name = $_SESSION["video_name"];
    
    $infile = fopen('php://input', 'rb');
    $outfile = fopen($name, 'ab');

    while ($buffer = fread($infile, 4096)){
        fwrite($outfile, $buffer);
    }
    fclose($infile);
    fclose($outfile);
}