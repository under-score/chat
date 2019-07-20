<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=utf-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

<?php
date_default_timezone_set("Europe/Berlin");

function sanitize($string, $force_lowercase = true) {
	#$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ">", "/", "?");
	$strip = array("`", "\\", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“",";",":","{","}","[","]");
    $clean = trim(str_replace($strip, "-", strip_tags($string)));
    # $clean = preg_replace('/\s+/', "-", $clean);
    return ($force_lowercase) ?
        (function_exists('mb_strtolower')) ?
            mb_strtolower($clean, 'UTF-8') :
            strtolower($clean) :
        $clean;
}

#include('emoji.php');

# if all messages can be immediately displayed yes/no
$standard=0;

$id=1;
$nickname='';
$ret2='&nbsp;&nbsp;';
$ip = $_SERVER['REMOTE_ADDR'];
$timestamp = date(DATE_RFC3339);
$browser = $_SERVER['HTTP_USER_AGENT'];
$macAddr="";
$arp=`arp -an`;
$lines=explode("\n", $arp);
foreach($lines as $line) {
  if (strpos($line,$ip) !== false) {
    $macAddr=$line;
  }
}

if ( isset($_POST['submit']) )  {
  $nickname=sanitize($string=$_POST['nickname'],$force_lowercase = false);
  if ($nickname=="") $nickname="null";
   
  $msg=sanitize($string=$_POST['message'],$force_lowercase = false);
  if ($msg=="") $msg="null";
  
  #$msg=preg_replace("/\'/","",$_POST['message']);
  $lat=$_POST['latitude'];
  $lon=$_POST['longitude'];

  #$msg=emoji_google_to_unified($msg);
  
  class MyDB extends SQLite3 {
    function __construct() {
      $this->open('index.sqlite3');
    }
  }
  $db = new MyDB();

  $uploaddir = __DIR__ . "/cache/";

  $sql =<<<EOF
SELECT MESSAGEPOSITION FROM CHAT WHERE NICKNAME ='{$nickname}' LIMIT 1;
EOF;
  $ret1 = $db->query($sql);
  $msgposition = rand(15, 40);
  while($row = $ret1->fetchArray(SQLITE3_ASSOC) ){
    if ($row['MESSAGEPOSITION'] != "") $msgposition = $row['MESSAGEPOSITION'];
  }

  for($i=0; $i<count($_FILES['upload']['name']); $i++) {
    $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
        
    if ($tmpFilePath != ""){
      $fn= sanitize($_FILES['upload']['name'][$i]);
      if(move_uploaded_file($tmpFilePath, $uploaddir.$fn)) {
        $sql =<<<EOF
INSERT INTO CHAT(IP,ARP,BROWSER,NICKNAME,MESSAGE,MESSAGEPOSITION,FILENAME,DATE_ENTRY,ACTIVE)
VALUES ('{$ip}','{$macAddr}','{$browser}', '{$nickname}', '{$msg}', {$msgposition}, '{$fn}', '{$timestamp}', {$standard});
EOF;
        $ret2 = $db->query($sql);
        if(!$ret2){
          $ret2 = $db->lastErrorMsg();
          $ret2 = "&nbsp;&nbsp;" . substr($msg,0,10) . "... FAIL";
        }
        else {
          $ret2 = "&nbsp;&nbsp;" . substr($msg,0,10) . "... SENT";
        }
      }
    }
  }

  # without attachment
  if ( $_FILES['upload']['name'][0] == "" ) {
    $sql =<<<EOF
INSERT INTO CHAT(IP,ARP,BROWSER,NICKNAME,MESSAGE,MESSAGEPOSITION,DATE_ENTRY,ACTIVE)
VALUES ('{$ip}','{$macAddr}', '{$browser}', '{$nickname}', '{$msg}', {$msgposition}, '{$timestamp}', {$standard});
EOF;
    $ret3 = $db->query($sql);
  }

  $db->close();
}
?>

</head>
<body>
<div data-role="page" id="pageone" data-theme="b">

  <div data-role="panel" id="myPanel" data-position="right">
  </div> 

  <div data-role="header" data-position="fixed">
  <?php echo $ret2;?>
  </div>

  <div data-role="main" class="ui-content">
    <form method="POST" action="client.php" name="wall" enctype="multipart/form-data" data-ajax="false">
    <div class="ui-field-contain">
      <label for="nickname">Name</label>
      <input type="text" name="nickname" value="<?php echo $nickname;?>" placeholder="Bitte ausfüllen">
      <label for="message">Nachricht</label>
      <input type="hidden" name="latitude" id="latitude">
      <input type="hidden" name="longitude" id="longitude"">
      <textarea cols="40" rows="10" name="message" placeholder="Bitte ausfüllen"></textarea>
      <label for="userfile"></label>
      <input name="upload[]" type="file" multiple="" accept="image/gif, image/jpeg, image/png, .gif, .jpeg, .jpg, .png"/>
      <label for="submit"></label>
      <input type="submit" name="submit" value="Abschicken" class="ui-btn ui-btn-a">
    </div>
    </form> 
    </div>

    <div data-role="footer" data-position="fixed">
    </div>
    
  </div> 

</div>

</body>
</html>
