<html>
<body>
<?php
if (file_exists('update_fetch_lock')) {
  echo "Update fetch is locked.<br/>\n";
} else {
  echo "Update fetch started.<br/>\n";
  //exec("./update_fetch.sh >/dev/null 2>/dev/null &");
}
?>
</body>
</html>
