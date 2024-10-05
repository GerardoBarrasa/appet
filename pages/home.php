<h1>Hola Mundo con Core!</h1>

<p>Hola Mundo!</p>

<select class="form-control">					
<?php
foreach($idiomas as $idioma)
{ ?>
	<option value="<?=$idioma->slug ?>"><?= $idioma->nombre ?></option>
<?php
}
?>
</select>

<p><?=l('segundo-texto');?></p>

<?php
if( !empty($test) )
	echo "<p>".$test."</p>";
?>

<p>Probar ajax GET</p>
<button type="button" id="trigger_get">TEST</button><br/>
<div id="ajax__result_get"></div>

<br/><br/>
<p>Probar ajax POST</p>
<input type="text" id="ajax__testvar_post">
<button type="button" id="trigger_post">TEST</button><br/>
<div id="ajax__result_post"></div>

<br/><br/>
<h3>Cliente antes de actualizar</h3>
<?php vd($old_cliente);?>
<br/><br/>
<h3>Cliente después de actualizar</h3>
<?php vd($cliente);?>
