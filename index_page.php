<?php
require_once 'form_helper.php';
use \GeneralClasses\HTMLFunctions;
HTMLFunctions::add_header('All Recipes');

$display='';
foreach($rows as $row) {
  $display .= "<li><span style='display: inline-block; width: 300px;'><a href='recipe.php?id=$row->id'>$row->name</a></span>";
  $display .= "<a href='edit_recipe_name.php?id=$row->id'>Edit</a>    ";
  $display .= "<a class='remove_recipe' data-id=$row->id href='#'>Remove</a></li>";
}

?>

<h2>Recipes!</h2>
<ul>
  <?= $display; ?>
</ul>
<br/>
<br/>

<a href='add_recipe.php'>Add a new recipe!</a>

<?php
HTMLFunctions::add_footer();
?>