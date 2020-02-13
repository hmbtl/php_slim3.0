<?php

class DB {
  private $_connection;
  private $_result;
  private static $_instance;

  public static function getInstance() {

    if(!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function __construct() {
    $this->_connection = new PDO('mysql:charset=utf8;dbname=' . DB_DATABASE,DB_USER,DB_PASSWORD,array(
          PDO::ATTR_PERSISTENT => true,
          PDO::MYSQL_ATTR_FOUND_ROWS => true,
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
          ));
  }

  public function getConnection() {
    return $this->_connection;
  }

  public function lastInsertId(){
    return $this->_connection->lastInsertId();
  }

  public function getData($query,$params,$assoc = true) {
    $stmt = $this->_connection->prepare($query);
    $stmt->execute($params);
    return $assoc ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetchAll();
  }

  public function getOne($query,$params, $assoc = true) {
    $stmt = $this->_connection->prepare($query);
    $stmt->execute($params);
    return $assoc ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetch();
  }

  public function getDataCount($query,$params) {
    $stmt = $this->_connection->prepare($query);
    $stmt->execute($params);
    return array('results' => $stmt->fetchAll(), 'count' => $stmt->rowCount());
  }

  public function execData($query,$params,$seq=0) {
    $stmt = $this->_connection->prepare($query);
    $stmt->execute($params);
    return $stmt->rowCount();
  }

  public function join($table, $fields) {

  }

  public function update($table, $fields, $where) {
    if(empty($fields) or empty($where)){
      return NULL;
    } else {
      $query = "UPDATE $table SET ";
      $index = 0;
      foreach($fields as $key=>$value){
        $index ++;
        $query .= " $key = :$key";
        if(sizeof($fields) > $index )
          $query .= ",";
      }

      $query .= " WHERE ";
      $index = 0;
      foreach($where as $key=>$value){
        $index ++;
        $query .= " $key = :wr_$key";
        $fields["wr_".$key] = $value;
        if(sizeof($where) > $index )
          $query .= " AND";
      }

      $stmt = $this->_connection->prepare($query);
      $stmt->execute($fields);
      return $stmt->rowCount();
    }
  }

  public function insert($table, $fields){
    if(empty($fields)){
      return NULL;
    } else {
      $query = "INSERT INTO $table (";
      $index = 0;
      foreach($fields as $key=>$value){
        $index ++;
        $query .= $key;
        if(sizeof($fields)>$index)
          $query .= ",";
      }
      $query .= ") VALUES (";

      $index = 0;
      foreach($fields as $key=>$value){
        $index ++;
        $query .= ":$key";
        if(sizeof($fields)>$index)
          $query .= ",";
      }
      $query .= ")";

      $stmt = $this->_connection->prepare($query);
      $stmt->execute($fields);
      return $stmt->rowCount();
    }
  }

  public function delete($table, $where = array()){
    $params = array();
    $query_where = "";
    $index = 0;
    foreach($where as $key=>$value){
      $index ++;
      $query_where .= " $key = :wr_$key";
      $params["wr_".$key] = $value;
      if(sizeof($where) > $index){
        $query_where .= " and";
      }
    }
    if(!empty($query_where))
      $query_where = "WHERE $query_where";
    $query = "DELETE FROM $table $query_where";

    $stmt = $this->_connection->prepare($query);
    $stmt->execute($params);
    return $stmt->rowCount();
  }


  public function one($table, $fields = array(), $where = array()){
    return $this->load($table, $fields, $where, True);
  }

  public function load($table, $fields = array(), $where = array(), $isOne = False){
    $params = array();
    $index = 0;
    $query_fields = "";
    foreach($fields as $key){
      $index ++;
      $query_fields .= $key;
      if(sizeof($fields)>$index){
        $query_fields .= ", ";
      }
    }

    $index = 0;
    $query_where = "";
    foreach($where as $key=>$value){
      $index ++;
      $query_where .= " $key = :wr_$key";
      $params["wr_".$key] = $value;
      if(sizeof($where) > $index){
        $query_where .= " and";
      }
    }

    if(empty($query_fields))
      $query_fields = "*";

    if(!empty($query_where))
      $query_where = "WHERE $query_where";

    $query = "SELECT $query_fields FROM $table $query_where";

    $stmt = $this->_connection->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($isOne){
      if(sizeof($result) == 1)
        return $result[0];
    }
    return $result;
  }

}

?>
