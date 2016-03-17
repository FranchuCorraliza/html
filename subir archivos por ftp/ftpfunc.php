<?php
# FUNCIONES FTP

# CONSTANTES
# Cambie estos datos por los de su Servidor FTP
define("SERVER","elitestore.es"); //IP o Nombre del Servidor
define("PORT",21); //Puerto
define("USER","completo"); //Nombre de Usuario
define("PASSWORD","&ZEt:W{q/7&"); //Contraseña de acceso
define("MODO",true); //Activa modo pasivo

# FUNCIONES

function ConectarFTP(){
//Permite conectarse al Servidor FTP
$id_ftp=ftp_connect(SERVER,PORT); //Obtiene un manejador del Servidor FTP
ftp_login($id_ftp,USER,PASSWORD); //Se loguea al Servidor FTP
ftp_pasv($id_ftp,MODO); //Establece el modo de conexión
ftp_chdir($id_ftp, "/dev/var/borrar");
return $id_ftp; //Devuelve el manejador a la función
}

function SubirArchivo($archivo_local,$archivo_remoto){
//Sube archivo de la maquina Cliente al Servidor (Comando PUT)
$id_ftp=ConectarFTP(); //Obtiene un manejador y se conecta al Servidor FTP 
ftp_put($id_ftp,$archivo_remoto,$archivo_local,FTP_BINARY);
//Sube un archivo al Servidor FTP en modo Binario
ftp_quit($id_ftp); //Cierra la conexion FTP
}

function ObtenerRuta(){
//Obriene ruta del directorio del Servidor FTP (Comando PWD)
$id_ftp=ConectarFTP(); //Obtiene un manejador y se conecta al Servidor FTP 
$Directorio=ftp_pwd($id_ftp); //Devuelve ruta 
ftp_quit($id_ftp); //Cierra la conexion FTP
return $Directorio; //Devuelve la ruta a la función
}
?>#