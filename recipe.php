<?php

require_once 'open_database.php';

if($_SERVER['REQUEST_METHOD'] === 'GET') {
  if($input = validate_request()) {
    $sql="SELECT * FROM recipes LEFT JOIN recipesteps on recipes.id=recipesteps.recipe_id WHERE recipes.id=? ORDER BY step_order ASC";
    $query = $db->prepare($sql);
    $query->execute([$input['id']]);
    $results = $query->fetchAll();
    if($results !== null) {    
      require_once 'recipe_page.php';
    } else {
      print 'Recipe not found!' . $input['id'];
    }
    print "<a href='index.php'>Back to Recipe Page</a>";  
  } else {
    print 'No Naughty Business!' . $input['id'];
  }  
}


if($_SERVER['REQUEST_METHOD'] === 'POST') {
  global $db;
  list($input, $errors) = validate_post_request();
  if(!$errors) {
    $i = 1;
    foreach($input['step_id'] as $id) {
      $sql = "UPDATE recipesteps SET step_order=? WHERE id=?";
      $query = $db->prepare($sql);
      $query->execute([$i++, $id]);      
    }
  }  
}

if($_SERVER['REQUEST_METHOD'] === 'DELETE') {    
  list($input, $errors) = validate_delete_request();
  if(!$errors) {
    if($input['op'] == 'photo') remove_photo($input['id']);
    global $db;
    $sql = "DELETE FROM recipesteps WHERE id=? LIMIT 1";
    $query = $db->prepare($sql);
    $query->execute([$input['id']]);
  }
}

function remove_photo($id) {
  global $db;
  $sql = "SELECT photo FROM recipesteps WHERE id=?";
  $query=$db->prepare($sql);
  $query->execute([$id]);
  $result = $query->fetch();
  var_dump($result);
  if($result) @unlink($result->photo);
}

function validate_delete_request() {
  $entry_modes=['step' , 'photo'];
  $errors=[];
  parse_str(file_get_contents("php://input"),$input);
  if(!isset($input['op']) || !in_array($input['op'], $entry_modes)) {
    $errors[] = 'Invalid Operation';
  }
  if(!isset($input['id']) || !is_numeric($input['id'])) {
    $errors[] = 'Invalid ID type!';
  }

  return [$input, $errors];
}

function validate_post_request() {
  $errors=[];
  $input=$_POST;
  if(isset($input['step_id']) ) {
    foreach($input['step_id'] as $id) {
      if(!is_numeric($id)) $errors[] = 'ID is not an integer!';
    }
  } else {
    $errors[] = 'No ids provided!';
  }
  return [$input, $errors];
}

function validate_request() {
  if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $input['id'] = $_GET['id'];
    return $input;
  }
  return [];  
}

?>