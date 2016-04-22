<?php
class EnviarFtp{
	function conectarftp($ftpDomain,$ftpusername,$ftppass){
		$conn_id = ftp_connect($ftpDomain); 

		// iniciar una sesi�n con nombre de usuario y contrase�a
		$login_result = ftp_login($conn_id, $ftpusername, $ftppass); 

		// verificar la conexi�n
		if ((!$conn_id) || (!$login_result)) {  
			echo "<p>�La conexi�n FTP ha fallado!</p>";
			return false;
			exit; 
		} else {
			echo "<p>Conexi&oacute;n a $ftpDomain realizada con &eacute;xito</p>";
		}
		return $conn_id;
	}
	function subirftp($conn_id,$ftpdir,$carpeta_destino,$file,$rutaorigen){
		// establecer una conexi�n b�sica
		
		
		// subir un archivo
		
		if (!@ftp_nlist($conn_id, $ftpdir."/".$carpeta_destino)) { //Crea el directorio de almacenamiento si no existe
			ftp_mkdir($conn_id, $ftpdir."/".$carpeta_destino);
		}
		echo "<p>Subiendo $rutaorigen en $ftpdir/$carpeta_destino/$file</p>";
		$upload = ftp_put($conn_id, $ftpdir."/".$carpeta_destino."/".$file,$rutaorigen, FTP_BINARY);  //sube el fichero 
		// comprobar el estado de la subida
		
		if (!$upload) {  
			echo "<p>�La subida FTP ha fallado!</p>";
			return false;
		} else {
			echo "<p>Subida exitosa de $rutaorigen</p>";
			return true;
		}

		// cerrar la conexi�n ftp 
		
		
	}
	
	function enviarEmailExitoso($fichero,$numfotos){
		
		  $mensaje = "Hi Umar,
		I hope you are doing great.

		We have uploaded a new Inbox (" . $fichero . "). There are " . $numfotos . " items. 
		Please, let us know when the outbox is finished.

		kind regards,


		Miguel Cirujano
		Graphic design

		 

		Benabola 8  29660 Puerto Banus
		Marbella, M�laga - Spain
		+34 952 82 97 69
		miguel@elitespain.es
		elitestore.es
		frivolidays.com

		   

		";
	  $destinatario      = 'fotografia@elitespain.es';
	  $titulo    = 'inbox';

	  mail($destinatario, $titulo, $mensaje);
	}
	
	function enviarEmailFallido(){
		
		  $mensaje = "La subida de archivos FTP ha fallado


		Miguel Cirujano
		Graphic design

		 

		Benabola 8  29660 Puerto Banus
		Marbella, M�laga - Spain
		+34 952 82 97 69
		miguel@elitespain.es
		elitestore.es
		frivolidays.com

		   

		";
	  $destinatario      = 'fotografia@elitespain.es';
	  $titulo    = 'inbox failed';

	  mail($destinatario, $titulo, $mensaje);
	}
	
}