<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enviar Inbox</title>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script language="JavaScript">
	function toggle(source) {
	  jQuery('table').find('.subido').each(function(index, element){
		  element.checked = source.checked
	  });
	}
</script>
  </head>
  <body>
  <?php
	require_once("./clases/RastreaCarpeta.php");
	require_once("./clases/ConexionManager.php");
	require_once("./clases/ComprimirZip.php");
	require_once("./clases/EnviarFtp.php");
	$server = "SERVIDOR";
	$uid = "ICGAdmin";
	$pwd = "masterkey";
	$inbox= 'inbox/';
	$rutaComprimidos= 'comprimidos';
	$ftpDomain="u51575462.1and1-data.host"; //ftp domain name
	$ftpusername="u51575462-fotografia";  //ftp user name 
	$ftppass="qwepoi123098."; //ftp passowrd
	$ftpdir="inbox"; //ftp main folder
	
	$rastreador=new RastreaCarpeta;
	$fotos=$rastreador->getFotos($inbox);
	
  if(!$_GET):
	?>
	<h2>Art&iacute;culos Fotografiados</h2>
	<p>selecciona los productos que se van a enviar para retocar:</p>   
	<form>
		<?php echo $rastreador->pintarTabla($inbox,$fotos,$server,$uid,$pwd); ?>
		<div class="form-group">
			<label class="control-label col-sm-2" for="numinbox">Numero de Inbox:</label>
			<div class="col-sm-10">
				<input class="form-control" id="numinbox" placeholder="Introduzca el número del inbox al que quiere añadir las fotografías seleccionadas">
			</div>
		</div>
		<input type="submit" value="Subir Items"  class="btn btn-danger"/>
	</form>
	<?php
  else:
	ob_start();
	$fotosParaSubir=array();
	if ((array_key_exists('numinbox',$_GET)) && ($_GET['numinbox']!='')):
		$numinbox=$_GET['numinbox'];
	else:
		$numinbox=1;
	endif;
	
	foreach ($_GET as $item):
		foreach ($fotos[$item] as $foto):
			$fotosParaSubir[]=$foto;
		endforeach;
	endforeach;
	?>
	<p>Iniciando subida</p>
	<p>Se van a subir <?php echo count($_GET) ?> art&iacute;culos con un total de <?php echo count($fotosParaSubir)?> fotos.</p>
	<?php
	$enviar= new EnviarFtp;
	$conn= $enviar->conectarftp($ftpDomain,$ftpusername,$ftppass);
	$hoy = getdate();
	$carpeta_inbox= "inbox" . "-" . $hoy['mday'] . "-" . $hoy['mon'] . "-" . $hoy['year']."-".$numinbox;
	$enviado=true;
	foreach ($fotosParaSubir as $foto):
		if ($enviado): //Controlamos que se envíen correctamente todas las imágenes
			$enviado=$enviar->subirftp($conn,$ftpdir,$carpeta_inbox,$foto,$inbox.$foto);
			ob_flush();			
		endif;
	endforeach;
	ftp_close($conn);
	if($enviado):
		$enviar->enviarEmailExitoso($carpeta_inbox,count($fotosParaSubir));
		?>
		<p>Actualizando artículos en Manager</p>
		<?php
		$conexionManager= new ConexionManager;
		$currentdate=date('Y-m-d');
		foreach ($_GET as $item):
			$codarticulo=substr($item,0,-1);
			$query="UPDATE ARTICULOSCAMPOSLIBRES SET FOTOGRAFIA='T',FECHA_FOTO='$currentdate' WHERE CODARTICULO=$codarticulo";
			$conexionManager->updateQuery($server,$uid,$pwd,$query);
		endforeach;
		?>
		<p>Se han actualizado los artículos en Manager correctamente</p>
		<p>Moviendo Fotografías a NAS
		<?php
		foreach ($fotosParaSubir as $fichero):
			$salida =  exec('mover_foto.bat '. $fichero);
			echo '<p>';
			print_r($salida);
			echo '</p>';
		endforeach;
		?>
		<p>Todas las fotos han sido movidas</p>
		<p>FIN DE LA SUBIDA - GRACIAS POR PARTICIPAR EN EL BINGO EN CASA</p>
		<?php
	else:
		$enviar->enviarEmailFallido();
		?>
		<p>ERROR EN LA SUBIDA</p>
		<?php
	endif;
  endif;
  ob_end_flush();
  ?>
  </body>
</html>