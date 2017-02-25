<?php
require_once 'form_helper.php';
use \GeneralClasses\HTMLFunctions;
HTMLFunctions::add_header('Edit Recipe Step');
?>


<h2>Edit Recipe Step</h2>
<?php

if($errors) {
  print "<p>Please fix the following errors:</p>";
  foreach($errors as $error) {
    print "<p>$error</p>";
  }
}

?>
<br/>

<form enctype="multipart/form-data" method='post' action=<?= $_SERVER['PHP_SELF'] ?>>
  <table>
    <?= $form->input('hidden', ['name'=>'id']); ?>
    <?= $form->input('hidden', ['name'=>'entry']); ?>
    <?php if(isset($input['step'])) { ?>
      <tr><td>Step:</td><td><?= $form->input('text', ['name' => 'step']); ?></td></tr>
    <?php } ?>
    <?php if(isset($input['photo'])) { ?>
      <img src =<?= $input['photo']?> width=200 height=100/>
      <tr><td>Photo:</td><td><?= $form->input('file', ['name' => 'photo']); ?></td></tr>
    <?php } ?>
    <tr><td><?= $form->input('submit', ['name' => 'edit_step']); ?></td></tr>
  </table>
</form>

<?php
HTMLFunctions::add_footer();
?>