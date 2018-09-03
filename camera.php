<?php

$ultimafoto="IMMAGINE NON DISPONIBILE";
$filename = 'tempfs/camera.jpg';
$esiste=false;
if (file_exists($filename)) {
    $esiste=true;
    $datafoto = filemtime($filename);
    $ultimafoto = date ("H:i:s",$datafoto) . " del " . date("d/m/Y",$datafoto);
}

// Set the content-type
header('Content-Type: image/jpeg');

// Create the image from file if exists
if(!$esiste)
{
   $im = imagecreatetruecolor(370, 370);
} else {
   $im = imagecreatefromjpeg ($filename);
}

// Create some colors
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);

//fill the image if the jpeg doesnt exists
if(!$esiste)
  imagefilledrectangle($im, 0, 0, 369, 369, $grey);

//scrive data e ora dell'ultima modifica al file camera.jpg
imagestring($im, 3, 5, 1, $ultimafoto, $white);

// Using imagepng() results in clearer text compared with imagejpeg()
imagejpeg($im);
imagedestroy($im);

?>

