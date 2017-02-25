
<?php require_once 'form_helper.php';
use \GeneralClasses\HTMLFunctions;
HTMLFunctions::add_header('Edit Recipe Name');
?>
<h2>Edit Recipe Name</h2>

<?php
  if($errors) {
    print "<p>Please correct the following errors: </p>";
    foreach($errors as $err) {
      print "<p>$err</p>";
    }
  }

?>
<form method='post' action= <?= $_SERVER['PHP_SELF'] ?>>
  <?= $form->input('text', ['name'=>'name']) ?>
  <?= $form->input('hidden', ['name'=>'id']) ?>
  <?= $form->input('submit', ['name'=>'change_name']) ?>
</form>
<?php
HTMLFunctions::add_footer();
?>