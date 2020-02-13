<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';


global $db;
$db = DB::getInstance();

#-----------------------------------------------
#               GENERAL FUNCTIONS
#-----------------------------------------------

function notFound($request, $response, $args){
  return error($response, 400);
}

function authenticate($request, $response, $next){
  $token = current($request->getHeader("Authorization"));

  if ($token) {
    global $db;

    $user = $db->one("users", array(), array("token"=>$token));        

    if (empty($user)) {
      return error($response, 101);
    } else {
      $request = $request->withAttribute('user', $user);
      $response = $next($request, $response);
      return $response;
    }
  } else {
    return error($response, 100);
  }
}

function getErrors($request, $response, $args){
  global $ERR;
  return response($response, $ERR); 
}

function getUser($request, $response, $args){
  global $db;

  // get user information from slim
  $user = $request->getAttribute("user");
  

  return response($response, $user);
}

?>
