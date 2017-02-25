<?php
require_once 'form_helper.php';
use \GeneralClasses\FormHelper;

require_once 'open_database.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  list($inputs, $errors) = validate_form();
  if($errors) {
    show_form($errors);
  } else {
    process_form($inputs);
    print "Recipe with name $inputs[name] created!</br>";
    print "<a href='index.php'>Return to recipes page</a>";
  }
} else {  
  show_form();
}

function process_form($inputs) {
  global $db;
  $sql = "INSERT into recipes VALUES(0, ?)";  
  $query = $db->prepare($sql);
  $query->execute([$inputs['name']]);
}

function show_form($errors=[]) {
  $form = new FormHelper();

  require_once 'add_recipe_form.php';
}

function validate_form() {
  $errors = [];
  $inputs['name'] = trim($_POST['name']);
  if(strlen($inputs['name']) < 3) {
    $errors[] = 'Please enter a name more than 3 characters long';    
  }
  $inputs['name'] = htmlentities($inputs['name']);

  return [$inputs, $errors];
}
?>