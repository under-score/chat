<?php
foreach($_GET as $k => $v) {
  $get[$k] = is_array($_GET[$k]) ? filter_var_array($_GET[$k], FILTER_SANITIZE_STRING) : filter_var($_GET[$k], FILTER_SANITIZE_STRING);
}
$k=$get["k"];

$sql =<<<EOF
SELECT *, strftime('%H:%M',date_entry) as HM, strftime('%d.%m.%Y',date_entry) as DMY FROM chat WHERE id>={$k} AND active=1 ORDER BY id ASC;
EOF;

try {

  $db = new PDO('sqlite:/'.__DIR__.'/index.sqlite3');
  # next lines produces a segmentation fault if the query is empty and called by ajax
  # so we need to rewrite our $sql to have always >1 result
  $result = $db->query($sql);
	
  foreach($result as $row) {
    $row['FILENAME'] != "" ? $img='<img class="postimg" src="cache/' . $row['FILENAME'] . '">' : $img='';
    $row['MESSAGE'] = preg_replace('/\:\r\n?/','', $row['MESSAGE']);
    if ($row['ID']>$k) {
      $myarray[]  = array("ID" => $row['ID'], "MESSAGEPOSITION" => $row['MESSAGEPOSITION'], "DMY" => $row['DMY'], "HM" => $row['HM'], "NICKNAME" => $row['NICKNAME'], "LATITUDE" => $row['LATITUDE'], "LONGITUDE" => $row['LONGITUDE'], "MESSAGE" => $row['MESSAGE'] . "<br/>", "IMG" => $img);
      $out=json_encode($myarray);
      break;
    }
  }
  $db=NULL;
  echo $out=="" ? http_response_code(202) : $out;
}

catch(PDOException $e) {
  #echo $e->getMessage();
  http_response_code(400);
}

# file_put_contents('debug.log', print_r(get_defined_vars(),1));
?>
