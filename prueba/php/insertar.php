<?php
/**
* Verifica si una direccion de correo es correcta o no.
*
*Si el registro es correcto y no está repetido, añadirá el nuevo email, en cualquier caso redireccionará de nuevo a la web de inicio
* @author Francisco Antonio Corraliza García
* @return boolean true si la direccion es correcta
* @param string $email direccion de correo
*/

function verificaremail($email){ 
  if (!ereg("^[0-9a-z_\-\.]+@([a-z0-9\-]+\.?)*[a-z0-9]+\.([a-z]{2,4})$",$email)){ //si no cumple con el parametro
      return FALSE; //devuelve falso
  } else { //sino
       return TRUE;//devuelve correcto 
  } 
}
if (isset($_GET['email']))//comprobamos que este definido el email
{
	$email=$_GET['email']; //lo guardamos en la variable
	if (verificaremail($email)) //llamamos a la funcion para validar el email
	{
		try {
			$opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");//utilizamos utf8 para los registros
			$dwes = new PDO('mysql:host=localhost;dbname=emails', 'root', '', $opciones);//estos parametros son para local

			$resultado = $dwes->query("SELECT * FROM usuarios WHERE email = '" . $_GET['email']. "';");//comprobamos que el email no está repetido
			$row = $resultado->fetch();//obtenemos los registros
			if ($row == null)//si no está
				{
					$resultado = $dwes->exec("INSERT INTO usuarios (email) VALUES ('". $_GET['email'] . "');");//insertamos el registro
					echo "<meta content='0;URL=http://localhost/prueba/' http-equiv='REFRESH'> </meta>";//redireccionamos a la web de inicio

				}
			else//si no
				{
					echo "el email ya se encuentra en la base de datos";//el registro ya está y lo escribimos
					echo "<meta content='0;URL=http://localhost/prueba/' http-equiv='REFRESH'> </meta>";//redireccionamos a la web de inicio
				}
			}
		catch (PDOException $p) {    echo "Error ".$p->getMessage()."<br />";}//capturamos si hay algun error

	}
	else//si no cumple con las normas de que sea un email
	{ 
	        echo "Email no válido"; //escribimos que el email no es valido
	        echo "<meta content='0;URL=http://localhost/prueba/' http-equiv='REFRESH'> </meta>";//redireccionamos a la web de inicio
	}
}
else
{
	echo "Email no definido";//si no está definido escribimos el texto
	echo "<meta content='0;URL=http://localhost/prueba/' http-equiv='REFRESH'> </meta>";//redireccionamos a la web de inicio
}
?>