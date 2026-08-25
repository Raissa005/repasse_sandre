<?php

function echod($dbug,$string = false,$die = true ){
	echo("<pre>")  ;
	print_r($dbug) ; 
	if($string)
		echo $string ; 
	if($die)
		die("fim");
}

function findAll(){
    $result = mysql_query("SELECT * FROM $table WHERE 1 = 1");
    do{
        $retorno[] = mysql_fetch_assoc($result);
        echod($retorno);
    }while($row);
}

function findAllBy($table, $where){
    
    $result = mysql_query("SELECT * FROM $table WHERE $where");
    while($row = mysql_fetch_assoc($result)){
        $rows[] = $row ;
    }
    
    return $rows ;
}

function gerenciaPost($table, $regra = array()){
	
	validaPost($regra);

    ($_POST['id'])?  $id =  update($table)   :  $id = insert7181($table);
	
    return $id;
}

function delete($table, $id){
    return mysql_query("delete from $table where id = $id");
}

function deleteBy($table , $where ){
    return mysql_query("delete from $table where $where");
}

function find($table, $id){
    return mysql_fetch_assoc(mysql_query("select * from $table where id = $id")); 
}

function findBy($table , $where){
    return mysql_fetch_assoc(mysql_query("select * from $table where $where")); 
}

function update($table) {
    $sql = "UPDATE $table set "; 
    if (!$_SESSION['model'][$table]) {
        montaSession($table);
    }
    foreach ($_POST as $key => $value) {
        if (in_array($key, $_SESSION['model'][$table])) {
            $sql .= "`$key` = '$value', " ; 
        }
    }
    $sql = substr_replace($sql, " where `id` = ", -2);
    $sql .= "'".$_POST['id']."'" ; 
    mysql_query($sql);
	return $_POST['id'] ; 
    
}

function montaSession($table) {
    $rows = mysql_query("SHOW COLUMNS FROM $table");
    if (mysql_num_rows($rows) > 0) {
        while ($row = mysql_fetch_assoc($rows)) {
            $_SESSION['model'][$table][] = $row['Field'];
        }
    }
}

function insert7181($table) {

    if (!$_SESSION['model'][$table]) {
        montaSession($table);
    }
    foreach ($_POST as $key => $value) {
        if (in_array($key, $_SESSION['model'][$table])) {
            @$colunas .= "`".$key."`" . ",";
            @$values .= "'$value',";
        }
    }
    $colunas = substr_replace($colunas, "", -1);
    $values = substr_replace($values, "", -1);
    mysql_query("insert into `" . $table . "` (" . $colunas . ") values (" . $values . ")");
	return mysql_insert_id();
}

function validaPost(array $regra) {
	if($regra){
		foreach ($regra as $value) {
			if(!$_POST[$value])
				throw new Exception('Erro de formulario');
		}
	}
	return false ;	
}

?>
