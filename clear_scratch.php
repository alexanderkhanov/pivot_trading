<html>
<body>
<?php
$tickers = explode(" ",rtrim(file_get_contents("ticker_list.txt")));
foreach ($tickers as $ticker) {
  unlink("scratch/$ticker.csv");
}
?>
Scratch clearing complete.
</body>
</html>
