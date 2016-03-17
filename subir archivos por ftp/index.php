<?php echo "<?xml version='1.0' encoding='iso-8859-1'?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>::..Funciones FTP..::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>
<p align="center"><font size="5" face="Verdana, Tahoma, Arial"><strong><em>
Funciones FTP
</em></strong></font></p>
<p><font face="Verdana, Tahoma, Arial">

<?php
include('ftpfunc.php'); //Incluye el archivo de funciones
if(!empty($_FILES["archivo"]))
{
$file = $_FILES["archivo"]["tmp_name"];
$base_archivo = basename($_FILES["archivo"]["name"]);
$id_ftp=ConectarFTP();
ftp_chdir($id_ftp, "www/ftp_upload");
$upload = ftp_put($id_ftp, $base_archivo, $file, FTP_BINARY);
if (!$upload) {
$status = "Error al guardar: " . $base_archivo;
} else {
$status = "Exito al gaurdar: " . $base_archivo;
}
unset($_FILES["archivo"]);
ftp_quit($id_ftp);
}
/*if(!empty($HTTP_POST_FILES["archivo"])){ //Comprueba si la variable "archivo" se ha definido
SubirArchivo($HTTP_POST_FILES["archivo"],basename($HTTP_POST_FILES["archivo"]));
//basename obtiene el nombre de archivo sin la ruta
unset($HTTP_POST_FILES["archivo"]); //Destruye la variable "archivo"
}*/
?>
<strong><font color="#000000" size="3">Subir Archivo</font></strong></font></p>
<hr />

<!--Formulario para elejir el archivo a subir -->
<form action="" method="post" name="form_ftp" id="form_ftp" enctype="multipart/form-data">
<p><font size="2" face="Verdana, Tahoma, Arial"> Elegir archivo :
<input name="archivo" type="file" id="archivo" />
<input name="Submit" type="submit" value="Subir Archivo" />
</font><font size="2" face="Verdana, Tahoma, Arial"> </font> </p>
</form>

<hr />
<p><font face="Verdana, Tahoma, Arial"><strong><font color="#000000" size="3">
Lista de Archivos
</font></strong></font></p>
<table width="69%" border="1" cellspacing="0" cellpadding="0">
<tr>
<td width="48%"><div align="center"><font size="2" face="Verdana, Tahoma, Arial"><strong>Nombre</strong></font></div></td>
<td width="22%"><div align="center"><font size="2" face="Verdana, Tahoma, Arial"><strong>Tama&ntilde;o</strong></font></div></td>
<td width="30%"><div align="center"><font size="2" face="Verdana, Tahoma, Arial"><strong>Fec.
Modificaci&oacute;n</strong></font></div></td>
</tr>
<?php
$id_ftp=ConectarFTP(); //Obtiene un manejador y se conecta al Servidor FTP
$ruta=ObtenerRuta(); //Obtiene la ruta actual en el Servidor FTP
echo "<b>El directorio actual es: </b> ".$ruta;
$lista=ftp_nlist($id_ftp,$ruta); //Devuelve un array con los nombres de ficheros
$lista=array_reverse($lista); //Invierte orden del array (ordena array)
while ($item=array_pop($lista)) //Se leen todos los ficheros y directorios del directorio
{
$tamano=number_format(((ftp_size($id_ftp,$item))/1024),2)." Kb";
//Obtiene tamaño de archivo y lo pasa a KB
if($tamano=="-0.00 Kb") // Si es -0.00 Kb se refiere a un directorio
{
$item="<i>".$item."</i>";
$tamano="&nbsp;";
$fecha="&nbsp;";
}else{
$fecha=date("d/m/y h:i:s", ftp_mdtm($id_ftp,$item));
//Filemtime obtiene la fecha de modificacion del fichero; y date le da el formato de salida
}
?>

<tr>
<td><font size="2" face="Verdana, Tahoma, Arial"><?php echo $item ?></font></td>
<td align="right"><font size="2" face="Verdana, Tahoma, Arial"><?php echo $tamano ?></font></td>
<td align="right"><font size="2" face="Verdana, Tahoma, Arial"><?php echo $fecha ?></font></td>
</tr>
<?php } ?>
</table>
</body>
</html>