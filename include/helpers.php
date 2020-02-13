<?php
require_once __DIR__ . '/errors.php';
/*
function response($res, $success = True, $data = null){
  $status = "success";

  if (!$success){
    $status = "error";
  }

  $response = array();
  $response['status'] = $status;

  if($status){
    if(is_array($data)){
      $response['data'] = $data;
    } else if (is_string($data)){
      $response['message'] = $data;
    } else {
      $response['data'] = null;
    }
  } else {
    $response['error']['code'] = $data;
    $response['error']['message'] = $ERR[$data];
  }

  return $res->withJson($response)
    ->withHeader('Content-Type', 'application/json;charset=utf-8');
}
*/
function response($res, $data = array()){
  $response = array();
  $response["status"] = "success";
  $response["data"] = $data;
  
  return $res->withJson($response)
    ->withHeader('Content-Type', 'application/json');
}

function error($res, $code){
  global $ERR;

  $response = array();
  $response["status"] = "error";
  $response["error"]["code"] = $code;
  $response["error"]["message"] = $ERR[$code];
  
  return $res->withJson($response)
    ->withHeader('Content-Type', 'application/json');
}


function removeKeys($data){
  $newData = array();
  foreach($data as $item){
    $itemData = array();
    foreach($item as $key=>$value){
      array_push($itemData, $value);
    }
    array_push($newData, $itemData);
  }
  return $newData;
}

function optionals($requires, $actuals){
  foreach($actuals as $key => $value){
    if(!in_array($key, $requires)){
      unset($actuals[$key]);
    }
  }

  return $actuals;
}

function verify($requires, $actuals){
  $fields = array();

  $actuals = array_keys($actuals);

  foreach($requires as $require){
    if(!in_array($require, $actuals)){
      array_push($fields, $require);
    }
  }

  if(empty($fields)){
    return null;
  } else {
    return "[".implode(",", $fields)."]";
  }
}

function gen_uuid() {
  return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

      // 16 bits for "time_mid"
      mt_rand( 0, 0xffff ),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand( 0, 0x0fff ) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand( 0, 0x3fff ) | 0x8000,

      // 48 bits for "node"
      mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
      );
}
?>
