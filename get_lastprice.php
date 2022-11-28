<?php

function get_lastprice($ticker) {

  $lastprice = 0;

  // try scratch first
  $filename = "scratch/$ticker.csv";
  if (file_exists($filename) && ($handle = fopen($filename,"r"))!==false) {
    $line = fgets($handle);
    $data = explode(",",$line);
    $lastprice = $data[4];
    fclose($handle);
  }
  if ($lastprice>0) return $lastprice;

  // no go? try last line of scrap
  $filename = "scrap/$ticker.csv";
  if (file_exists($filename) && ($handle = fopen($filename,"r"))!==false) {
    $pos = -2; // skip trailing \n
    $ch = " ";
    while ($ch != "\n") {
      if (fseek($handle, $pos, SEEK_END) == -1) {
	rewind($handle);
	break;
      }
      $ch = fgetc($handle);
      --$pos;
    }
    $line = fgets($handle);
    $data = explode(",",$line);
    $lastprice = $data[4];
    fclose($handle);
  }

  return $lastprice;
}

?>
