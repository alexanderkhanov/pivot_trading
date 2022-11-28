<html>
<body>
<?php
$ticker = $_POST["ticker_name"];

echo "Ticker: $ticker<br>\n";

$ticker_data = array();
foreach (array("history/$ticker.csv","scrap/$ticker.csv","scratch/$ticker.csv") as $filename) {
  if (file_exists($filename) && ($handle = fopen($filename,"r"))!==false) {
    while (($line = fgets($handle))!==false) {
      $ticker_data[] = $line;
    }
    fclose($handle);
  }
}

// keep fixed number of data points
$npoints = 20;
array_splice($ticker_data,0,-$npoints);

$im_width = 640; $im_height = 320;
$im = imagecreate($im_width,$im_height)
or die("Cannot Initialize new GD image stream");
$u0 = 50; $u1 = 620;
$v1 = 20; $v0 = 290; // (0,0) is top left corner

$colors = array();
$colors["background"] = imagecolorallocate($im,255,255,255);
$colors["black"] = imagecolorallocate($im,0,0,0);
imagefilledrectangle($im,0,0,$im_width-1,$im_height-1,$colors["background"]);

$font = imageloadfont("Latin2Alex_7x14_LE.gdf");

require "draw_frame.php";
$yrange = draw_frame($im,$font,$u0,$v0,$u1,$v1,$colors["black"],$ticker_data);
$xrange = draw_dates($im,$font,$u0,$v0,$u1,$v1,$colors["black"],$ticker_data);
draw_points($im,$u0,$v0,$u1,$v1,$colors["black"],$colors["background"],$xrange,$yrange,$ticker_data);

require "read_dataline.php";
list($state,$positions,$actions) = read_dataline($ticker);
draw_positions($im,$u0,$v0,$u1,$v1,$colors["black"],$xrange,$ticker_data,$positions);
draw_actions($im,$u0,$v0,$u1,$v1,$colors["black"],$yrange,$actions);

ob_start();
imagepng($im);
$data = ob_get_contents();
$data = base64_encode($data);
ob_end_clean();

imagedestroy($im);

echo "<img src=\"data:image/$format;base64,$data\"><p>\n";
?>
</body>
</html>
