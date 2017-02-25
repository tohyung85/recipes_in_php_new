<?php
require_once 'form_helper.php';
use \GeneralClasses\FormHelper;

require_once 'open_database.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  list($input, $errors) = validate_form();
  if($errors) {
    show_form($errors);
  } else {
    process_form($input);
  }
} else {
  list($input, $errors) = validate_request();
  if($errors) {
    print '<p>Errors: </p>';
    foreach($errors as $err) {
      print "<p>$err</p>";
    }
  } else {
    show_form();  
  }  
}

function show_form($errors=[]) {
  global $input;
  $form = new FormHelper($input);

  require_once 'edit_recipe_step_form.php';
}

function process_form($input) {
  global $db;
  $sql = "SELECT recipe_id FROM recipesteps WHERE id=?";
  $query=$db->prepare($sql);
  $query->execute([$input['id']]);
  $result = $query->fetch();  
  if(isset($input['step'])) edit_step($input);
  if(isset($input['photo'])) edit_photo($input);  

  print "<a href='recipe.php?id=$result->recipe_id'>Return to recipe</a>";
}

function edit_step($input) {
  global $db;
  $sql = "UPDATE recipesteps SET step=? WHERE id=?";
  $query=$db->prepare($sql);
  $query->execute([$input['step'], $input['id']]);

  print '<p>Step Edited!</p>';
}

function edit_photo($input) {
  $photo = time().$input['photo'];
  $target = PHOTO_UPLOADPATH . $photo;
  
  if(move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
    echo'removing photo..';
    remove_photo($input['id']);
    global $db;  
    $sql = "UPDATE recipesteps SET photo=? WHERE id=?";
    $query = $db->prepare($sql);
    $query->execute([$target, $input['id']]);
    print '<p>Edited Photo!</p>';
  } else {
    print 'Error Uploading file';
  }
}

function remove_photo($id) {
  global $db;
  $sql = "SELECT photo FROM recipesteps WHERE id=?";
  $query=$db->prepare($sql);
  $query->execute([$id]);
  $result = $query->fetch();
  if($result) @unlink($result->photo);
}

function validate_form() {
  $input=[];
  $errors=[];
  $entry_modes = ['step', 'photo'];
  if(!is_numeric($_POST['id'])) {
    $errors[] = 'Recipe id should be an integer!';
  }
  $input['id'] = $_POST['id'];
  if(!in_array($_POST['entry'], $entry_modes)){
    $errors[] = 'Entry mode must be a photo or a step!';
  }
  $input['entry'] = $_POST['entry'];
  if($input['entry'] == 'step') validate_step_entry($input, $errors);
  if($input['entry'] == 'photo') validate_photo_entry($input, $errors);

  return [$input, $errors];
}

function validate_request() {
  global $db;
  $input['id'] = $_GET['id'];
  $errors=[];
  if(!is_numeric($input['id'])) {
    $errors[] = 'Invalid id type!';
  } else {
    $sql = "SELECT * FROM recipesteps WHERE id=?";
    $query = $db->prepare($sql);
    $query->execute([$input['id']]);
    $result=$query->fetch();
    if(!$result) {
      $errors[] = 'No such recipe step';
    } else {
      if($result->step) {
        $input['step'] = $result->step;  
        $input['entry'] = 'step';
      } else {
        $input['photo'] = $result->photo;
        $input['entry'] = 'photo';
      }            
    }    
  }

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

?>