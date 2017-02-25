<?php
require_once 'form_helper.php';
use \GeneralClasses\FormHelper;

require_once 'open_database.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  list($input, $error) = validate_form();
  if($error) {
    show_form($error);
  } else {
    process_form($input);
  }
} else {
  list($input, $error) = validate_request();
  if($error) {
    print '<p>Errors found:</p>';
    foreach($error as $e) {
      print '<p>$e</p>';
    }
  } else {
    show_form();
  }
}

function process_form($input) {
  global $db;
  $sql = "UPDATE recipes SET name=? WHERE id=? LIMIT 1";
  $query = $db->prepare($sql);
  $query->execute([$input['name'], $input['id']]);

  print "<p>Name updated!</p>";
  print "<a href='index.php'>Return to list of recipes</a>";
}

function validate_form() {
  $errors=[];
  $input['id'] = $_POST['id'];
  if(!isset($input['id']) || !is_numeric($input['id'])) {
    $errors[] = 'Invalid id input';
  } 
  $input['name'] = trim($_POST['name']);
  if(strlen($input['name']) < 3) {
    $errors [] = 'Please input name of at least 3 characters';
  }
  $input['name'] = htmlentities($input['name']);

  return [$input, $errors];
}

function show_form($errors=[]) {
  global $input;
  $form = new FormHelper($input);

  require_once 'edit_recipe_name_form.php';
}

function validate_request() {
  global $db;
  $errors=[];
  $input['id'] = $_GET['id'];
  if(!isset($input['id']) || !is_numeric($input['id'])) {
    $errors[] = 'Invalid id type';
  } else {
    $sql = "SELECT * FROM recipes WHERE id=? LIMIT 1";
    $query = $db->prepare($sql);
    $query->execute([$input['id']]);
    $result = $query->fetch();
    if(!$result) {
      $errors[] = 'No such recipe';
    } else {
      $input['name'] = $result->name;
    }
  }

  return [$input, $errors];
}



?>