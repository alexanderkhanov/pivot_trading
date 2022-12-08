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
<td>State</td>
<td>Positions</td>
<td>Last price</td>
<td>Actions</td>
</tr>

<?php

require "read_dataline.php";
require "get_lastprice.php";

$tickers = explode(" ",rtrim(file_get_contents("ticker_list.txt")));

foreach ($tickers as $ticker) {

  list($state,$positions,$actions) = read_dataline($ticker);

  $lastprice = get_lastprice($ticker);

  $price_inrange = 0;
  foreach ($actions as $price => $action) {
    if ($price<=$lastprice) $price_inrange = $price;
  }

  echo "<tr><td><form method=\"POST\" action=\"plot_ticker.php\"><input type=\"submit\" name=\"ticker_name\" value=\"$ticker\"></form></td>";

  echo "<td>$state</td>";

  echo "<td>";
  foreach ($positions as $date => $price) {
    $price = number_format($price,2);
    echo "$date: $price<br>";
  }
  echo "</td>";

  echo "<td>";
  if ($lastprice>0) {
    echo $lastprice;
  }
  echo "</td>";

  echo "<td>";
  foreach ($actions as $price => $action) {
    if ($price==$price_inrange) {
      $price = number_format($price,2);
      if ($state==0 && $action>0) {
	echo "<span style=\"background-color:PaleGreen\">$price: $action</span><br>";
      } else if ($state>0 && $action==0) {
	echo "<span style=\"background-color:Pink\">$price: $action</span><br>";
      } else if ($state>0) {
	echo "<span style=\"background-color:PowderBlue\">$price: $action</span><br>";
      } else {
	echo "$price: $action<br>";
      }
    } else {
      echo "$price: $action<br>";
    }
  }
  echo "</td>";

  echo "</tr>\n";
}

?>
</table>
</body>
</html>
