 /**
 @author Francisco Antonio Corraliza García
 Añadimos todos los eventos y funcionesen este archivo y asi separamos la logica de la programación
 el evento se añadirá al cargar el documento
    @function inicio
*/
   window.addEventListener('load', inicio, false);//llamamos a la funcion inicio cuando carge la ventana en el evento onload

    function inicio() {//funcion que se carga con el evento load
        document.getElementById("formulario").addEventListener('submit', validar, false);//añadimos la escucha al formulario cuando se trate de enviar llamando a la funcion validar
    }
 /**
 funcion que va validando el formulario
    @function validar
    @param {object}	evt
*/
function validar(evt) {//funcion que llamamos cuando se trate de enviar el formulario

	        var cla6 = email();//comprobamos el email
		        if(cla6!=true)//si no es correcto para el evento
		    {
		        evt.preventDefault();//para el envio del formulario
		    }     
}//si todo fue bien no hizo falta parar el envio del formulario y entonces lo manda
 
 /**
 funcion para validar el email
    @function email
*/
function email(){

	var patron = /^[0-9a-z_\-\.]+@([a-z0-9\-]+\.?)*[a-z0-9]+\.([a-z]{2,4})$/;
	// ponemos el patron para detectar si esta vaio o si cumple la regla dada
	if (!patron.test(document.getElementById("email").value)){//comprobamos el email si es falso
		alert("email no válido.");
		// mostramos el error
		document.getElementById("email").focus();//ponemos el foco en el email
		return false;//y paramos el formulario
	}
	// si todo fue bien continuamos mandando el formulario	
	return true;
}