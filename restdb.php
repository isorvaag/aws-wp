<?php

function post($request, $mysqli) {
  if (isset($request[0]) && isset($request[1])) {
    $username=$request[0];
    $fname=$request[1];
    $sql="SELECT id from tbl_users where username='$username'"; 
    $result = $mysqli->query($sql); echo $mysqli->error;
    if ($result->num_rows > 0) {
      echo "Object already exists!";
      http_response_code(404);
    }
    else { 
        $sql = "INSERT into tbl_users (username, firstname) VALUES ('$username', '$fname')";
        if ($mysqli->query($sql) === TRUE) {	 
          echo json_encode(array("username"=>$username,"action" => "created"));
        }
	else echo $mysqli->error;
    }
  }
  else {
    echo "You need to set both username and firstname";
    http_response_code(404);
  } 
}

function deleter($request, $mysqli){
  $username=$request[0];
  $sql="SELECT id from tbl_users where username='$username'";
  $result = $mysqli->query($sql); echo $mysqli->error;
  if ($result->num_rows > 0) {
    $sql = "DELETE from tbl_users where username='$username'";
    $mysqli->query($sql); echo $mysqli->error;
    echo json_encode(array("username"=>$username, "action"=>"deleted"));
  }
  else {
    echo "Object doesn't exist!";
    http_response_code(404);
  }
}

$host='';
$dbuser='';
$dbpass='';
$db='';

$mysqli = new mysqli($host, $dbuser, $dbpass, $db);
if ($mysqli->connect_errno) {
  echo "error connecting"; exit;
}
$method = $_SERVER['REQUEST_METHOD'];
if (isset($_SERVER['PATH_INFO'])) {
  $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));

  switch ($method) {
    case 'GET':
      get($request, $bucket); break;
    case 'PUT':
      put($request, $bucket); break;
    case 'POST':
      post($request, $mysqli); break;
    case 'DELETE':
      deleter($request, $mysqli); break;
  }
}
else {
  echo "Nothing here, use http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'/username/firstname to use the API';
}
?>