<?php
require_once 'form_helper.php';
use \GeneralClasses\DatabaseFunctions;

require_once 'open_database.php';


if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  list($input, $errors) = validate_delete_request();
  if(!$errors) {
    $sql = "DELETE FROM recipes WHERE id=? LIMIT 1";
    $query = $db->prepare($sql);
    $query->execute([$input['id']]);
  }
}

function validate_delete_request() {
  $errors=[];
  parse_str(file_get_contents("php://input"),$input);
  if(!isset($input['id']) || !is_numeric($input['id'])) {
    $errors[] = 'Invalid ID type!';
  }
  return [$input, $errors];
}

$sql = "SELECT * FROM recipes";
$query= $db->prepare($sql);
$query->execute();
$rows = $query->fetchAll();

require_once 'index_page.php';

?>