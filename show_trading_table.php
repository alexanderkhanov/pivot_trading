<html>
<head>
<style type="text/css">
form {
  margin:0px; padding:0px; display:inline;
}
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>
<body>
<table>
<tr>
<td>Ticker</td>
<td>Last updated</td>
<td>State</td>
<td>Positions</td>
<td>Last price</td>
<td>Actions</td>
</tr>

<?php

require "read_dataline.php";
require "get_lastprice.php";

$dir = $_GET["madir"];

$tickers = explode(" ",rtrim(file_get_contents("ticker_list.txt")));

foreach ($tickers as $ticker) {

  list($state,$positions,$actions) = read_dataline($dir,$ticker);

  list($lastprice,$lasttime) = get_lastprice($ticker);

  $price_inrange = 0;
  foreach ($actions as $price => $action) {
    if ($price<=$lastprice) $price_inrange = $price;
  }

  echo "<tr><td><form method=\"POST\" action=\"plot_ticker.php\"><button type=\"submit\" name=\"ticker_name\" value=\"$dir:$ticker\">$ticker</button></form></td>";

  echo "<td>$lasttime</td>";

  echo "<td style=\"text-align: center\">$state</td>";

  echo "<td>";
  if (count($positions)==0) {
    echo "none";
  } else {
    foreach ($positions as $date => $price) {
      $price = number_format($price,2);
      echo "$date: $price<br>";
    }
  }
  echo "</td>";

  echo "<td>";
  if ($lastprice>0) {
    echo $lastprice;
  }
  echo "</td>";

  echo "<td>";
  if ($state==0 && count($actions)==1 && end($actions)==0) {
    echo "none";
  } else {
    foreach ($actions as $price => $action) {
      if ($price==$price_inrange) {
	$price = number_format($price,2);
	if (($state==0 && $action>0)||($state<10 && $action>=10)) {
	  echo "<span style=\"background-color:#c0ffc0\">$price: $action</span><br>";
	} else if ($state>0 && $action==0) {
	  echo "<span style=\"background-color:#ffc0c0\">$price: $action</span><br>";
	} else if ($state>0) {
	  echo "<span style=\"background-color:#ffff00\">$price: $action</span><br>";
	} else {
	  echo "$price: $action<br>";
	}
      } else {
	echo "$price: $action<br>";
      }
    }
  }
  echo "</td>";

  echo "</tr>\n";
}

?>
</table>
<br/>
<form method="POST" action="clear_scratch.php"><input type="submit" name="action_name" value="clear scratch"></form>
<form method="POST" action="update_scratch.php"><input type="submit" name="action_name" value="update scratch"></form>
</body>
</html>
