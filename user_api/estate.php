<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';

$GLOBALS['rstate'] = $rstate;
class Estate
{


  function restatelogin($username, $password, $tblname)
  {
    if ($tblname == 'admin') {
      $q = "select * from " . $tblname . " where username='" . $username . "' and password='" . $password . "'";
      return $GLOBALS['rstate']->query($q)->num_rows;
    } else if ($tblname == 'restate_details') {
      $q = "select * from " . $tblname . " where email='" . $username . "' and password='" . $password . "'";
      return $GLOBALS['rstate']->query($q)->num_rows;
    } else {
      $q = "select * from " . $tblname . " where email='" . $username . "' and password='" . $password . "' and status=1";
      return $GLOBALS['rstate']->query($q)->num_rows;
    }
  }

  function restateinsertdata($field, $data, $table)
  {

    return 0;
  }




  function insmulti($field, $data, $table)
  {

    $field_values = implode(',', $field);
    $data_values = implode("','", $data);

    $sql = "INSERT INTO $table($field_values)VALUES('$data_values')";
    $result = $GLOBALS['rstate']->multi_query($sql);
    return $result;
  }

  function restateinsertdata_id($field, $data, $table)
  {

    return 0;
  }

  function restateinsertdata_Api($field, $data, $table)
  {

    $field_values = implode(',', $field);
    $placeholders = implode(',', array_fill(0, count($field), '?'));

    $sql = "INSERT INTO $table ($field_values) VALUES ($placeholders)";
    $stmt = $GLOBALS['rstate']->prepare($sql);

    // Execute query with provided data
    $stmt->execute($data);
    // Get the last inserted ID
    $lastId = $GLOBALS['rstate']->insert_id;
    return $lastId;
  }

  function restateinsertdata_Api_Id($field, $data, $table)
  {

    $field_values = implode(',', $field);
    $data_values = implode("','", $data);

    $sql = "INSERT INTO $table($field_values)VALUES('$data_values')";
    $result = $GLOBALS['rstate']->query($sql);
    return $GLOBALS['rstate']->insert_id;
  }

  function restateupdateData($field, $table, $where)
  {
    return 0;
  }




  function restateupdateData_Api($field, $table, $where , $where_conditions)
  {
    // Prepare columns and values (excluding NULL values)
    $cols = [];
    $values = [];
    foreach ($field as $key => $val) {
        if ($val !== NULL) {
            $cols[] = "$key = ?";
            $values[] = $val;
        }
    }
      // Build the query
      $sql = "UPDATE $table SET " . implode(', ', $cols) . " $where";
      $combined =array_merge($values, $where_conditions);
      $stmt = $GLOBALS['rstate']->prepare($sql);
      
      // Execute the query
      $result = $stmt->execute($combined);
      
      return $result;
  }

  function restateupdateDatanull_Api($field, $table, $where)
  {
    $cols = array();

    foreach ($field as $key => $val) {
      if ($val != NULL) // check if value is not null then only add that colunm to array
      {
        $cols[] = "$key = '$val'";
      } else {
        $cols[] = "$key = NULL";
      }
    }

    $sql = "UPDATE $table SET " . implode(', ', $cols) . " $where";
    $result = $GLOBALS['rstate']->query($sql);
    return $result;
  }




  function restateupdateData_single($field, $table, $where)
  {
    return 0;
  }

  function restaterestateDeleteData($where, $table)
  {

    return 0;
  }

  function restateDeleteData_Api($where, $table)
  {

    return 0;
  }
  function restateDeleteData_Api_fav($where, $table)
  {

    $sql = "Delete From $table $where";
    $result = $GLOBALS['rstate']->query($sql);
    return $result;
  }
}
