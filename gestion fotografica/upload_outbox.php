<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Publicar Outbox</title>
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
	$outbox= 'outbox/';
	$ftpDomain="elitestore.es"; //ftp domain name
	$ftpusername="completo";  //ftp user name 
	$ftppass="&ZEt:W{q/7&"; //ftp passowrd
	$ftpdir="httpdocs/media/import/fotosweb/outbox"; //ftp main folder
	$rutafotos="/media/import/fotosweb/outbox";
	
	?>
	<p>Trasladando fotos al servidor local</p>
	<?php
	
	$salida =  exec('mover_fotos_outbox.bat ');
	echo '<p>';
	print_r($salida);
	echo '</p>';
	
	$rastreador=new RastreaCarpeta;
	$fotos=$rastreador->getFotos($outbox);
	
  if(!$_GET):
	?>
	<h2>Art&iacute;culos Fotografiados</h2>
	<p>selecciona los productos que quieres publicar:</p>   
	<form>
		<?php echo $rastreador->pintarTabla($outbox,$fotos,$server,$uid,$pwd); ?>
		<input type="submit" value="Subir Items"  class="btn btn-danger"/>
	</form>
	<?php
  else:
  
	ob_start();
	$fotosParaSubir=array();
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
	$carpeta_destino= "outbox" . "-" . $hoy['mday'] . "-" . $hoy['mon'] . "-" . $hoy['year'];
	$enviado=true;
	foreach ($fotosParaSubir as $foto):
		if ($enviado): //Controlamos que se envíen correctamente todas las imágenes
			$enviado=$enviar->subirftp($conn,$ftpdir,$carpeta_destino,$foto,$outbox.$foto);
			ob_flush();			
		endif;
	endforeach;
	ftp_close($conn);
	if ($enviado):
		?>
		<p>Las imagenes han sido subidas con exito a su destino ftp</p>
		<p>Actualizando artículos en Manager</p>
		<?php 
		$conexionManager= new ConexionManager;
		$currentdate=date('Y-m-d');
		foreach ($_GET as $item):
			$codarticulo=substr($item,0,-1);
			$query ="UPDATE ARTICULOSCAMPOSLIBRES SET FECHA_SUBIDA='$currentdate',"; 
			switch(count($fotos[$item])):
				case 1:
					$query.="CAMPO13='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][0]."";
					break;
				case 2:
					$query.="CAMPO13='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][0]."', CAMPO4='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][1]."'";
					break;
				case 3:
					$query.="CAMPO13='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][0]."', CAMPO4='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][1]."', CAMPO5='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][2]."'";
					break;
				case 4:
					$query.="CAMPO13='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][0]."', CAMPO4='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][1]."',CAMPO5='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][2]."', FOTOGRAFIA_4='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][3]."'";
					break;
				case 5:
					$query.="CAMPO13='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][0]."', CAMPO4='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][1]."', CAMPO5='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][2]."', FOTOGRAFIA_4='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][3]."', FOTOGRAFIA_5='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][4]."'";
			endswitch;
			$query.=" WHERE CODARTICULO=$codarticulo";
			$query2="BEGIN
				   IF NOT EXISTS (SELECT * FROM ARTICULOSDOC WHERE CODARTICULO=$codarticulo AND TIPO=0)
				   BEGIN
					   INSERT INTO ARTICULOSDOC (CODARTICULO,TIPO,PATH) VALUES ($codarticulo,0,'".$rutafotos."/".$carpeta_destino."/".$fotos[$item][0]."')
				   END
				   ELSE
				   UPDATE ARTICULOSDOC SET PATH='".$rutafotos."/".$carpeta_destino."/".$fotos[$item][0]."' WHERE CODARTICULO=$codarticulo AND TIPO=0;
				END";
				echo "<p>$query2</p>";
			$query3="UPDATE ARTICULOS SET TACON='SI'WHERE CODARTICULO=$codarticulo";
			$conexionManager->updateQuery($server,$uid,$pwd,$query);
			$conexionManager->updateQuery($server,$uid,$pwd,$query2);
			$conexionManager->updateQuery($server,$uid,$pwd,$query3);
		endforeach;
		?>
		<p>Se han actualizado los artículos en Manager correctamente</p>
		<p>Creando Backup de las fotos</p>
		<?php
		foreach ($fotosParaSubir as $fichero):
			$salida =  exec('mover_foto_subida.bat '. $fichero);
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