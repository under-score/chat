<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=utf-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta name="viewport" content="width=device-width, initial-scale=1">

<script src = "http://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="projector.css" />

</head>

<body>

<div class="menu"><a href="">refresh</a></div>

<span class="lines">chat</span>
<pre>

<a href="#end">end</a>

<?
if (!file_exists('index.sqlite3')) {
  new SQLite3('index.sqlite3');
  chmod('index.sqlite3', 0777);
  class MyDB extends SQLite3 {
    function __construct() {
      $this->open('index.sqlite3');
    }
  }
  $db = new MyDB();
  $db->query("CREATE TABLE 'chat' ('ID' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 'IP' TEXT, 'BROWSER' TEXT, 'NICKNAME' TEXT, 'MESSAGE' BLOB, 'MESSAGEPOSITION' INTEGER, 'FILENAME' TEXT, 'DATE_ENTRY' DATETIME, 'ACTIVE' BOOLEAN, 'LATITUDE' INTEGER, 'LONGITUDE' INTEGER, 'FLIP' INTEGER,'ARP' TEXT);");
}

class MyDB extends SQLite3 {
  function __construct() {
    $this->open('index.sqlite3');
  }
}
$db = new MyDB();
$sql = "SELECT * from chat ORDER BY id ASC";
$ret = $db->query($sql);

$i=0;
$aid=array();
  while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
	$aid[]=$row['ID'];
	$row['ACTIVE']==0 ? $a=1 : $a =0;
	$i=$row['FILENAME'];
	$img= $i!="" ? '<a href="cache/'.$i.'"><img src="cache/'.$i.'" width="30px"></a> ' : "IMG ";
	$id=$row['ID'];
	$nick=$row["NICKNAME"];
    $nick=substr(str_pad($nick,10," "),0,10)." ";
	$msg = preg_replace('/\r\n?/', "", $row['MESSAGE']);
	echo '<div id="' . $id . '"><a class="';
	echo $id==$status["position"]+1 && $status["output"]=="chat" ? "high" : "";
	echo '" href="status.php?output=chat&refresh=true&redirect=admin&position=' . ($id-1) . '">'.$id.'</a> ';
	echo '<a class="activestatus" href="status.php?id='.$id.'&field=active&content=' . $a. '">'.$row['ACTIVE'].'</a> ';
	echo $img;
	echo $nick;
	echo $msg;
	echo '</div>'.PHP_EOL;
  }

$db->close();
?>

<a name="end" href="#top">top</a>

</pre>

<script>
var k=<?php echo max($aid);?>;
function worker() {
	$.ajax({
		url: 'status.php?position='+k,    
		success: function(data) {
		  var IS_JSON = true;
          try {
            var json = $.parseJSON(data);
          }
          catch(err) {
            IS_JSON = false;
          }
		  if (IS_JSON && json!==null) {
            for (var i = 0, len = json.length; i < len; ++i) {
               var line1 = json[i];
               var line2 = '\n<div id="' + line1.id + '">' + line1.idlink + line1.activelink + line1.image + line1.nickname + line1.message + '</div>';
			   $('#' + k).after(line2);
  		       k=line1.id;
		    }
		  }
		}
	});
}
setInterval(worker,1000);

$(document).on( "click", ".activestatus", function(e) {
  e.preventDefault();
  $.ajax({
    url: e.currentTarget.href,    
    success: function() {
      $('#'+e.currentTarget.parentElement.id ).prepend("*");
    }
 });
});

</script>
</body>
</html>