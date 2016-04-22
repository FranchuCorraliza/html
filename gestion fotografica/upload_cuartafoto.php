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
	$outbox= 'outbox-cuarta-foto/';
	$rutafotos= "http://www.elitestore.es/media/import/fotosweb/outbox";
	$rastreador=new RastreaCarpeta;
	$fotos=$rastreador->getFotos($outbox);
	
  if(!$_GET):
	?>
	<h2>Art&iacute;culos Fotografiados</h2>
	<p>selecciona las cuartas fotos que desea publicar:</p>   
	<form>
		<?php echo $rastreador->pintarTabla($outbox,$fotos,$server,$uid,$pwd); ?>
		<input type="submit" value="Subir Items"  class="btn btn-danger"/>
	</form>
	<?php
  else:
  
	ob_start();
	
	$proxy = new SoapClient('http://www.elitestore.es/api/soap/?wsdl=1');
	$user = "cuartafoto";
	$password = "29843cd7dbfd10cf832792f9b2c2a1cb";
	$sessionId = $proxy->login($user, $password);
	foreach ($_GET as $item):
		$consulta= new ConexionManager;
		$codarticulo=substr($item,0,-1);
		$query="select T0.REFPROVEEDOR,T1.FOTOGRAFIA_4 from referenciasprov T0 LEFT JOIN ARTICULOSCAMPOSLIBRES T1 ON T0.CODARTICULO=T1.CODARTICULO where T1.codarticulo=$codarticulo";
		$result=$consulta->getQuery($server, $uid, $pwd, $query);
		$productSku=$result[0]['REFPROVEEDOR'];
		if ($result[0]['FOTOGRAFIA_4']!=''):
			$rutafoto= "http://www.elitestore.es".$result[0]['FOTOGRAFIA_4'];
			
			echo "<p>Subiendo Cuarta foto del art&iacute;culo $productSku ubicada en $rutafoto</p>";
			ob_flush();		
			$file_headers = @get_headers($rutafoto);
			
			if($file_headers[0]!='HTTP/1.1 404 Not Found'):
				$newImage = array(
						'file' => array(
							'name' => 'file_name',
							'content' => base64_encode(file_get_contents($rutafoto)),
							'mime'    => 'image/jpeg'
						),
						//'label'    => '',
						'position' => 3,
						//'types'    => array('small_image', 'thumbnail', 'image'),
						'exclude'  => 0
					);
				echo $productSku;
				$imageFilename = $proxy->call($sessionId, 'product_media.create', array((string)$productSku, $newImage,'admin','sku'));
				$result = $proxy->call($sessionId, 'catalog_product.update', array($productSku, array('has_four_images' => 1),'admin','sku'));
				echo "<p>Imagen subida correctamente: Sku: " . $productSku . "</br> Imagen:</br> <img src='" . $rutafoto . "' width='50px' />";
				$salida =  exec('borrar_cuarta_foto.bat '. $fichero);
				echo '<p>';
				print_r($salida);
				echo '</p>';
			endif;
		endif;
		
	endforeach;
	ob_end_flush();
  endif;
  ?>
  </body>
</html>