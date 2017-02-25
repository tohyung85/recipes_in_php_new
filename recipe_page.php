<?php
require_once 'form_helper.php';
use \GeneralClasses\HTMLFunctions;

HTMLFunctions::add_header($results[0]->name);

$list='';
$order=1;
foreach($results as $result) {
  if($result->step) {
    $list .= "<li id='step_id-$result->id'><span style='display: inline-block; width: 300px;'><span class='order_display'>$order</span>. $result->step</span>";
    $list .= "<a href='edit_recipe_step.php?id=$result->id'>Edit</a>    ";
    $list .= "<a class='remove_instruction' data-op='step' data-id=$result->id href='#'>Remove</a></li>";
    $order++;
  } 
  if($result->photo) {
    $list .= "<li id='step_id-$result->id'><span style='display: inline-block; width: 300px;'><img src='$result->photo' height=100 width=200></span>";
    $list .= "<a href='edit_recipe_step.php?id=$result->id'>Edit</a>    ";
    $list .= "<a class='remove_instruction' data-op='photo' data-id=$result->id href='#'>Remove</a></li>";
  }  
}
?>

<h2><?= $results[0]->name ?></h2>

<ul id="sortable" style="list-style:none;">
  <?= $list; ?>
</ul>

<a href="add_recipe_step.php?id=<?= $input['id'] ?>&entry=step">Add Step</a>
<a href="add_recipe_step.php?id=<?= $input['id'] ?>&entry=photo">Add Photo</a>
<br/>
<br/>

<?php
HTMLFunctions::add_footer();
?>