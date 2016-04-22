<?php

class ConexionManager{
	
	function getQuery($server,$uid,$pwd,$query){
		$conn = new PDO( "sqlsrv:server=$server ; Database = G001", $uid, $pwd); 
		if( $conn === false ){
			echo "No es posible conectarse al servidor.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		$stmt = $conn->prepare($query); 
		if( $stmt === false ){
			echo "<br/>Error al ejecutar consulta 1.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		$stmt->execute();
		$result = $stmt->fetchall(PDO::FETCH_BOTH);
		return $result;
	}
	
	function updateQuery($server,$uid,$pwd,$query){
		$conn = new PDO( "sqlsrv:server=$server ; Database = G001", $uid, $pwd); 
		if( $conn === false ){
			echo "No es posible conectarse al servidor.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		$stmt = $conn->prepare($query); 
		if( $stmt === false ){
			echo "<br/>Error al ejecutar consulta 1.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		$stmt->execute();
	}
}