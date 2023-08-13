<?php

function rowExist($result): bool {
    return mysqli_num_rows($result);
}

function freeResult($result): void {
    if(isset($result)) mysqli_free_result($result);
}

function getColumnFromRow(object $result, int $row = 0, int $col = 0) { 
    $num_rows = mysqli_num_rows($result); 
    if ($num_rows && $row <= ($num_rows - 1) && $row >= 0){
        mysqli_data_seek($result, $row);
        $result_row = (is_numeric($col)) ? mysqli_fetch_row($result) : mysqli_fetch_assoc($result);
        if (isset($result_row[$col])){
            return $result_row[$col];
        }
    }
    return false;
}

if(!function_exists("GetSQLValueString")) { 
    function GetSQLValueString(string $theValue, string $theType, string $theDefinedValue = "", string $theNotDefinedValue = "")
    {
      $theValue = addslashes($theValue);
  
      switch ($theType) {
        case "text":
          $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
          break;    
        case "long":
        case "int":
          $theValue = ($theValue != "") ? intval($theValue) : "NULL";
          break;
        case "double":
          $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
          break;
        case "date":
          $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
          break;
        case "defined":
          $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
          break;
      }
      return $theValue;
    }
}
?>