<?php
require_once 'form_helper.php';
use \GeneralClasses\HTMLFunctions;
HTMLFunctions::add_header('All Recipes');
?>

<h2>Add Recipe</h2>

<?php
if($errors) {
  print "<p>Please correct the following errors: </p>";
  foreach($errors as $error) {    
    print $error;
  }
}
?>
<br/><br/>
<form method='post' action=<?= $_SERVER['PHP_SELF'] ?>>
<table>
  <tr><td>Recipe Name: </td><td><?= $form->input('text', ['name' => 'name']) ?></td></tr>
  <tr><td><?= $form->input('submit', ['name' => 'submit']) ?></td></tr>
</table>
</form>

<?php HTMLFunctions::add_footer(); ?>