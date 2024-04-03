<?php

?>

<div class="category_wrapper">
<span><h3>Categories that need to Import</h3></span>
<?php
 $include_category = get_option('included_category',array());
if($include_category)
{ 
?>
<table>
<tr>
<th>
Categories that need to Import
</th>
</tr>

<?php

foreach($include_category as $key=> $value)
{
?>
<tr>
<td class = "include_category">
<input type="text" class= "ced_include_category" value ="<?php echo $value; ?>">
</td>
</tr>
<?php }
?>
<tr>
<td>
<input type= "button" class= "ced_include_more_category button button-primary" value = "One More+"></input>
</td>
<td>
<input type= "button" class= "ced_save_include_category button button-primary" value = "Save"></input>
</td>
</tr>
</table>   
<?php
} 
else { ?>
<table>
<tr>
<td class = "include_category">
<input type="text" class= "ced_include_category" value ="">
</td>
</tr>
<tr>
<td>
<input type= "button" class= "ced_include_more_category button button-primary" value = "One More+"></input>
</td>
<td>
<input type= "button" class= "ced_save_include_category button button-primary" value = "Save"></input>
</td>
</tr>
</table> 
<?php } ?>
</div>
<?php
?>