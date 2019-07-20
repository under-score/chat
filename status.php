<?php
foreach($_GET as $k => $v) {
  $get[$k] = is_array($_GET[$k]) ? filter_var_array($_GET[$k], FILTER_SANITIZE_STRING) : filter_var($_GET[$k], FILTER_SANITIZE_STRING);
}

if (!class_exists('MyDB')) {
  class MyDB extends SQLite3 {
    function __construct() {
      $this->open(__DIR__.'/index.sqlite3');
    }
  }
}
$db = new MyDB();

if ( isset($get['content']) && isset($get['field']) ) {

  in_array( $get['field'], array('active') ) ? $hz='' : $hz='"';
  $gc= $get['content']=="" ? "null" : $get['content'];
  $sql = 'UPDATE chat SET ' . $get['field'] . '=' . $hz . $gc . $hz . ' WHERE id=' . $get['id'] . ';';
  $get['id'] !="" ?  $res=$db->query($sql) : FALSE;
 
}
else {
  $pos = isset($get["position"]) ? $get["position"] : 0;
  $sql = 'SELECT * from chat ORDER BY id ASC;';
  $ret = $db->query($sql);
  while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
	$id      = $row['ID'];
	if ($id>$pos) {
	  $idlink     = '<a href="status.php?output=chat&refresh=true&redirect=admin&position=' . ($id-1) . '">'.$id.'</a> ';
	  $active     = $row['ACTIVE']==0 ? 1 : 0;
	  $activelink = '<a class="activestatus" href="status.php?id='.$id.'&field=active&content=' . $active. '">'.$row['ACTIVE'].'</a> ';
	  $image      = $row['FILENAME']!="" ? '<a href="cache/'.$row['FILENAME'].'"><img src="cache/'.$row['FILENAME'].'" width="30px"></a> ' : "IMG ";
      $nickname   = substr(str_pad($row["NICKNAME"],12," "),0,12)." ";
	  $message    = preg_replace('/\r\n{}?/', "", $row['MESSAGE']);
	  $myarray[]  = array("id" => $id, "idlink" => $idlink, "activelink" => $activelink, "nickname" => $nickname, "image" => $image, "message" => $message);
	}
  }
  echo json_encode($myarray,JSON_UNESCAPED_SLASHES);
}

$db->close();
# file_put_contents('debug.log', print_r(get_defined_vars(),1));
?>
