<html>
<body>
<?php
if (file_exists('update_scratch_lock')) {
  echo "Update scratch is locked.<br/>\n";
} else {
  echo "Update scratch started.<br/>\n";
  //exec("./update_scratch.sh >/dev/null 2>/dev/null &");
}
?>
</body>
</html>
