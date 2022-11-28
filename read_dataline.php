<?php

function read_dataline($ticker) {

  $state = 0;

  $positions = array();
  $actions = array();

  $filename = "dataline/$ticker.txt";
  if (file_exists($filename) && ($handle = fopen($filename,"r"))!==false) {
    while (($line = fgets($handle))!==false) {
      $items = explode(" ",rtrim($line));
      if ($items[0]=="state") {
	$state = $items[1];
	$n = sizeof($items);
	$i = 2;
	while ($i<$n) {
	  $date = $items[$i++];
	  $price = $items[$i++];
	  $positions[$date] = $price;
	}
      } else if ($items[0]=="actions") {
	$n = sizeof($items);
	$i = 1;
	while ($i<$n) {
	  $price = $items[$i++];
	  $action = $items[$i++];
	  $actions[$price] = $action;
	}
      }
    }
    fclose($handle);
  }

  return array($state,$positions,$actions);
}
?>
