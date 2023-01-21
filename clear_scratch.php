<html>
<body>
<?php
$tickers = explode(" ",rtrim(file_get_contents("ticker_list.txt")));
foreach ($tickers as $ticker) {
  $filename = "scratch/$ticker.csv";
  if (is_file($filename)) unlink($filename);
}
?>
Scratch clearing complete.
</body>
</html>
