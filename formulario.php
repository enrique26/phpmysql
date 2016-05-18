<?php
////descomentar pra activar las opciones de erroes en la pagina, sobre escribe las configuraciones de php 
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

//ejemplo CRUD
//conectar con la base de datos usando la libreria mysql de php; exiten dos comandos para esto mysql_connect y mysqli_connect
//lo recomendable es crear un archivo, externo a la pagina princiapl, generalmente llamada config.php que contiene la siguiente declaracion de variables para realizar la conexion a la bd

//php permite la inclusion o llamadas de modulos mediante la sentnecia include o include_once; de esta forma se pueden llamar variables y funciones desde archivos externos a nuestro codigo central y generar un codigo mas limpio y leguble
//---termina codigo para conexion a la bd--//
$mysql_db_hostname="127.0.0.1";
$mysql_db_user="root";
$mysql_db_password="metali";
$mysql_db_database="test";

//libreria de php para conectarse a base de datos con php
$connect=mysqli_connect($mysql_db_hostname,$mysql_db_user,$mysql_db_password,$mysql_db_database);
if (!$connect) {
	echo "No hubo exito en la conexion a la base de datos";
}
//---termina codigo para conexion a la bd--//

//php nos permite interactuar con peticion  POST,GET  para trabajar con informacion enviada de una pagina a otra, de la misma froma se pueden crear variables de sesion almacenadas en la cache de la maquina para usarse en nuestor codigo

// $_REQUEST[] es una variable "magica" que nos permite obtener valores tanto de get como de post, aunque tambien existen variables apra cada uno de estos metodos - $_POST[] y $_GET[]
$UID = $_REQUEST['UID'];
$respuesta = '';

//php nos permite la declaracion de funciones en cualquier parte del codigo, alser php un lenguaje interpretado por el servidor, el usuario no vera rastros de nuestro codigo ya que este es interpretado antes de enviarse al cliente, asi el usuario solo ve la informacion solicitada siendo transparente para el el proceso para obtenerlo



function altaUsuario($conect,$nombrex,$apellidox,$nickx,$correox){
	//revisar si ya existe el usuario
	//

	//definir el query para mysql
	$sql="SELECT * FROM usersAdmin where user LIKE '%".$user."%'";

	//realizar usar la conexion realizada para ejecutar l el query
	$result=mysqli_query($conect,$sql);

	//formatear el reultado del query para pode usarlo/ fetch_array genera un arreglo de los resultados basados en un valor llave como un json
	$result=mysqli_fetch_array($result);

	//si nickuser en bd es igual al valor enviado del formulario en la variable $nick no se ejecuta el query de insersion
	//para evitar el uso de comando de mysql en un formulario para hacer injeccion de comandos se usa el coamndo mysqli_real_escape_string() para evitar que carcateres especales se interprente como un comando y pasen como texto plano.
	if($result['nickuser']!=$nickx){
		$sql="INSERT INTO usuarios(nombre,apellido,nickuser,correo,fechaReg) 
		VALUES ('".mysqli_real_escape_string($conect,$nombrex)."','".mysqli_real_escape_string($conect,$apellidox)."','".mysqli_real_escape_string($conect,$nickx)."','".mysqli_real_escape_string($conect,$correox)."',NOW())";

		//ejecuta el query para hacer al insercion en db
		$result=mysqli_query($conect,$sql);
		if($result){
			//se creo el registro del usuario
			return 1;
		}else{
			//no se pudo insertar el registro
			return 2;
		}
	}else{
		//el usuario ya existe
		return 3;
	}
}

function consultarUsuarios($conect){
	//consultar listados de usuarios registrados
	$sql="SELECT * FROM usuarios";
	mysqli_query($conect,"SET NAMES 'utf8'");
	$result=mysqli_query($conect,$sql);
	return $result;
}

function borrarUsuario($conect,$idx){
	$sql="DELETE FROM usuarios WHERE id=".$idx;
	$result=mysqli_query($conect,$sql);
}

//comprobar que nuestra variable $UID contiene de un post o get el parametro cargarUsuario
if($UID=='cargarUsuario'){

	
	$nombre=$_REQUEST['nombre'];
	$apellidos=$_REQUEST['apellido'];
	$correo=$_REQUEST['correo'];
	$nickUsuario=$_REQUEST['nick'];

	//llamar la funcion altaUsuario() en una variable para almacenar la respuesta si se esta retornando una
	$respuestaBD=altaUsuario($connect,$nombre,$apellidos,$correo,$nickUsuario);

	if($respuestaBD==1){
		echo "<script type='text/javascript'>alert('usuario registrado correctamente')</script>";
	}else if($respuestaBD==2){
		echo "<script type='text/javascript'>alert('no se pudo insertar el registro')</script>";
	}else if($respuestaBD==3){
		echo "<script type='text/javascript'>alert('el nick ya esta en uso')</script>";
	}


}else if($UID=='borrarU'){
	$delete=$_REQUEST['idB'];
	borrarUsuario($connect,$delete);
}else{
		//no se hace ninguna accion

}
//subir archivos a base de datos

?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	
<h3>Funcion basica CRUD</h3>
	
	<h5>la tabla para el registor esta compuesta las siguientes columnas:</h5>
	<table style="border: double;">
		<thead>
			<tr>
				<th>id</th>
				<th>Nombre</th>
				<th>Apellidos</th>
				<th>Correo</th>
				<th>Nick</th>
				<th>fechaReg</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>tipo int</td>
				<td>tipo VARCHAR</td>
				<td>tipo VARCHAR</td>
				<td>tipo VARCHAR</td>
				<td>tipo VARCHAR</td>
				<td>tipo DATE</td>
			</tr>
		</tbody>
	</table>
	</br>
	</br>
	<h3>Cargar nuevos usuarios a nuestra tabla de registros</h3>
	<form action="formulario.php" method="POST" accept-charset="utf-8">
		
		<input type="hidden" name="UID" value="cargarUsuario"></input>
		<h4>nombre 
		<input type="text" name="nombre" value="" placeholder="Nombre del usuario"></h4></br>
		<h4>apellidos
		<input type="text" name="apellido" value="" placeholder="apellido del usuario"></h4></br>
		<h4>nick 
		<input type="text" name="nick" value="" placeholder="nickuser"></h4></br>
		<h4>correo 
		<input type="text" name="correo" value="" placeholder="direccion correo"></h4></br>

		<button class='button small success' type='submit'>Generar usuario</button>
	</form>
	</br>
	<div>
	<h3>Leer los usuarios registrados y cargar en nuestra tabla</h3>
	<table class="display" id="usersR" style="border: double;">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Apellidos</th>
					<th>Nick</th>
					<th>Correo</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php

				//generar dinamicamenta los row de una tabla
				//
				$tablaResultados=consultarUsuarios($connect);
				while ($usuarios=mysqli_fetch_Assoc($tablaResultados)) {
					$idusuario=$usuarios['id'];
				echo "
					<tr>
						<input type='hidden' name='id' value='$idusuario'></input>
						<td>".$usuarios['nombre']."</td>
						<td>".$usuarios['apellido']."'</td>
						<td>".$usuarios['correo']."</td>
						<td>".$usuarios['nickuser']."</td>
						<td><button onclick='alert($idusuario)'>Ver id del usuario</button></td>
						<td>
							<form action='formulario.php' method='POST'>
							<input type='hidden' name='UID' value='borrarU'>
							<input type='hidden' name='idB' value='".$idusuario."'>
							<button type='submit'>Borrar registro</button>
							</form></td>
					</tr>";
				}
			 ?>	
			</tbody>
		</table>

	</div>
	<!-- con esta base nos resta anadir una opcion para poder actualizar los datos de cada registro mostrado -->


</body>
</html>