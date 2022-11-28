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

  echo "<tr><td><form method=\"POST\" action=\"plot_ticker.php\"><input type=\"submit\" name=\"ticker_name\" value=\"$ticker\"></form></td>";

  echo "<td>$state</td>";

  echo "<td>";
  foreach ($positions as $date => $price) {
    echo "$date: $price<br>";
  }
  echo "</td>";

  echo "<td>";
  //if ($lastprice>0) {
    echo $lastprice;
  //}
  echo "</td>";

  echo "<td>";
  foreach ($actions as $price => $action) {
    echo "$price: $action<br>";
  }
  echo "</td>";

  echo "</tr>\n";
}

?>
</table>
</body>
</html>
