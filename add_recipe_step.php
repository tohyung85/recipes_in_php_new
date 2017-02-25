<?php
require_once 'form_helper.php';
use \GeneralClasses\FormHelper;

require_once 'open_database.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  list($input, $errors) = validate_form();
  $entry = $_POST['entry'];
  if($errors) {
    show_form($errors);
  } else {
    process_form($input);
  }
} else {
  list($recipe_details, $request_errors) = validate_request();
  $entry = $recipe_details['entry'];
  if($request_errors) {
    print "<p>Invalid requests:</p>";
    foreach($request_errors as $err) {
      print "<p>$err</p>";
    }
  } else {
    show_form();      
  }
}

function show_form($errors=[]) {
  global $recipe_details;
  global $entry;
  $form = new FormHelper($recipe_details);

  require_once 'add_recipe_step_form.php';
}

function process_form($input) {
  if(isset($input['step'])) add_step($input);
  if(isset($input['photo'])) add_photo($input);
  
  print "<a href='recipe.php?id=$input[id]'>Return to recipe</a>";
}

function add_step($input) {
  global $db;
  $sql = "INSERT INTO recipesteps VALUES(0, ?, '', ?, ?)";
  $query = $db->prepare($sql);
  $query->execute([$input['step'], $input['id'], $input['next_order']]);
  print '<p>Added Step!</p>';
}

function add_photo($input) {
  $photo = time().$input['photo'];
  $target = PHOTO_UPLOADPATH . $photo;
  
  if(move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
    global $db;  
    $sql = "INSERT INTO recipesteps VALUES(0, '', ?, ?, ?)";
    $query = $db->prepare($sql);
    $query->execute([$target, $input['id'], $input['next_order']]);
    print '<p>Added Photo!</p>';
  } else {
    print 'Error Uploading file';
  }
}

function validate_form() {
  $errors = [];
  $entry_mode = ['step', 'photo'];
  $input['entry'] = $_POST['entry'];
  if(!in_array($input['entry'], $entry_mode)) {
    $errors[] = 'Invalid Entry mode';
  }

  if($input['entry'] == 'step') validate_step_entry($input, $errors);
  if($input['entry'] == 'photo') validate_photo_entry($input, $errors);
  
  if(!is_numeric($_POST['next_order'])) {
    $errors[] = 'Next order should be an integer!';
  }
  $input['next_order'] = $_POST['next_order'];
  if(!is_numeric($_POST['id'])) {
    $errors[] = 'Recipe id should be an integer!';
  }
  $input['id'] = $_POST['id'];
  // var_dump($input);
  return [$input, $errors];
}

function validate_step_entry(& $input, & $errors) {
  $input['step'] = trim($_POST['step']);  
  if(strlen($input['step']) < 3) {
    $errors[] = 'Please enter at least 3 characters!';
  }
  $input['step'] = htmlentities($input['step']);
}

function validate_photo_entry(& $input, & $errors) {
  if(!isset($_FILES['photo']) || !file_exists($_FILES['photo']['tmp_name'])) {
    $errors[] = 'You need to upload a file!';
  } else {
    if(!is_uploaded_file($_FILES['photo']['tmp_name'])) {
      $errors[] = 'Uploaded files only!';
    }  
  }  
  $input['photo'] = $_FILES['photo']['name'];
}

function validate_request() {
  global $db;
  $entry_mode = ['step', 'photo'];
  $input=[];
  $errors =[];
  if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $sql = "SELECT * FROM recipes LEFT JOIN recipesteps ON recipes.id = recipesteps.recipe_id WHERE recipes.id=? ORDER BY step_order ASC";
    $query=$db->prepare($sql);
    $query->execute([$_GET['id']]);
    $result = $query->fetchAll();

    if($result) {
      $input['id'] = $_GET['id'];
      if($result[0]->step) {
        $input['next_order'] = $result[count($result) - 1]->step_order + 1;
      } else {
        $input['next_order'] = 1;
      }
    } else {
      $errors[] = 'Recipe does not exist!';
    }
  } else {
    $errors[] = 'ID is invalid type';
  }
  $input['entry'] = $_GET['entry'];
  if(!isset($input['entry']) || !in_array($input['entry'], $entry_mode)) {
    $errors[] = 'Invalid form type requested';
  }
    
  return [$input, $errors];
}
?>