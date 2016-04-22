<?php

class ComprimirZip{
	
	function comprimir($archivos,$dir,$rutaFinal){
		$zip = new ZipArchive();
 		if(!file_exists($rutaFinal)){
		  mkdir($rutaFinal);
		}
		$hoy = getdate();
		$archivoZip = "inbox" . "-" . $hoy['mday'] . "-" . $hoy['mon'] . "-" . $hoy['year'] . ".zip";
		 
		if ($zip->open($archivoZip, ZIPARCHIVE::CREATE) === true) {
			foreach ($archivos as $archivo):
				if (is_file($dir . $archivo) && $archivo != "." && $archivo != "..") {
					echo "<p>Agregando archivo: $dir$archivo </p>";
					$zip->addFile($dir . $archivo, $dir . $archivo);
				}else{
					echo "<p style='color:red;'>El archivo $dir$archivo no existe";
				}
			endforeach;
			$zip->close();
			rename($archivoZip, "$rutaFinal/$archivoZip");
			if (file_exists($rutaFinal. "/" . $archivoZip)) {
				echo "<p>Archivo comprimido $rutaFinal/$archivoZip ha sido creado correctamente";
				return $archivoZip;
			} else {
				echo "<p style='color:red'>Error, archivo zip no ha sido creado correctamente!!</p>";
				return false;
			}
		}
	}
}