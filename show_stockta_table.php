<html>
<head>
<style type="text/css">
body {
  background color: white;
  font-size: 12pt; 
}
.borderTable {
  background-color: #FFFFFF ; 
  border: 1px solid #96C7E0;
  width: 100%;  
}
.analysisTd {
  border: 1px solid; 
  font-size: 12pt;
  background-color: #FFFFFF;
}
</style>
</head>

<body>
<table class="borderTable"><tr><td class="analysisTd">Symbol</td><td class="analysisTd">Date</td><td class="analysisTd"><b>Overall</b></td><td class="analysisTd" >Short</td><td class="analysisTd">Intermediate</td><td class="analysisTd">Long</td></tr>

<?php
include 'fetch_table.html';
?>

</table>
<br/>
<form method="POST" action="update_fetch.php"><input type="submit" name="action_name" value="update fetch"></form>
</body>
</body>
</html>
