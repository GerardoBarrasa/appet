<h1>Hola Mundo con Jofran!</h1>

<p>Hola Mundo!</p>


<select class="form-control">					
<?php
foreach($datos_idiomas as $idiomas)
{ ?>
	<option value="<?=$idiomas->slug ?>"><?= $idiomas->nombre ?></option>
<?php
}
?>
</select>
<p><?=l('segundo-texto');?></p>

<?php
if( !empty($test) )
	echo "<p>".$test."</p>";
?>


<p>Probar ajax</p>
<input type="text" id="ajax__testvar">
<button type="button" id="trigger">TEST</button><br/>
<div id="ajax__result"></div>
