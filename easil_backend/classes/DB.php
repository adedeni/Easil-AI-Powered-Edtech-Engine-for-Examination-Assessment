<?php
class DB {
    private static $_instance = null;
    private $_pdo,
            $_query,
            $_error = false,
            $_results,
            $_count = 0,
            $_errorMessage = '';

private function __construct() {
    try {
        $this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), config::get('mysql/username'), Config::get('mysql/password'));
        $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        $this->_error = true;
        error_log($e->getMessage());
        return;
    }
}

public static function getInstance() {
    if (!isset(self::$_instance)) {
        self::$_instance = new DB();
    }
    return self::$_instance;
}

public function query($sql, $params = []) {
    $this->_error = false;
    $this->_errorMessage = '';
    
    try {
        if($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if(count($params)) {
                foreach($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            
            if($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
                $this->_errorMessage = $this->_query->errorInfo()[2];
            }
        }
    } catch(PDOException $e) {
        $this->_error = true;
        $this->_errorMessage = $e->getMessage();
        error_log($e->getMessage());
    }
    return $this;
}

public function action($action, $table, $where = []){
    if(count($where) === 3){
        $operators = ['=', '>', '<', '>=', '<='];
        $field = $where[0];
        $operator = $where[1];
        $value = $where[2];
        if(in_array($operator, $operators)){
            $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
            if(!$this->query($sql, [$value])->error()){
                return $this;
            }
        }
    }
    return false;
}

public function get($table, $where){
    return $this->action('SELECT *', $table, $where);
}

public function delete($table, $where){
    return $this->action('DELETE', $table, $where);
}

public function insert($table, $fields = []) {
        $keys = array_keys($fields);
        $values = '';
        $x = 1;
        
        foreach($fields as $field) {
            $values .= '?';
            if($x < count($fields)) {
                $values .= ', ';
            }
            $x++;
        }
        
        $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";
        
        
        if(!$this->query($sql, $fields)->error()){
            return true;
        }
        return false;
}

public function update($table, $id, $fields = []) {
    $set = '';
    $x = 1;

    foreach($fields as $name => $value) {
        $set .= "`{$name}` = ?";
        if($x < count($fields)) {
            $set .= ', ';
        }
        $x++;
    }

    $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
    
    if(!$this->query($sql, array_values($fields))->error()) {
        return true;
    }
    return false;
}
 
public function results(){
    return $this->_results;
}
public function first(){
    return $this->results()[0];
}
public function error(){
    return $this->_error;
}
public function count(){
   return $this->_count;
}
public function getError() {
    return $this->_errorMessage;
}
}
