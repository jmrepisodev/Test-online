<?php 

session_start();

// Si el usuario no está logueado se redirige a login.php
if (!isset($_SESSION['login']) || !isset($_SESSION['id_usuario'])) {
	header('Location: login.php');
	exit(); // termina la ejecución del script

}else{ //si la sesión está iniciada...

        $id_usuario=$_SESSION['id_usuario'];
        $username=$_SESSION['username'];
        $rol=$_SESSION['rol'];


        // Establece tiempo de vida de la sesión en segundos (10 minutos)
        $tiempoLimite = 600; 
        // Comprueba si $_SESSION["timeout"] está establecida
        if(isset($_SESSION["timeout"])){
            // Calcula el tiempo de vida de la sesión (TTL = Time To Live)= hora actual - hora inicio
            $sessionTTL = time() - $_SESSION["timeout"];
            if($sessionTTL > $tiempoLimite){
                session_unset();
                session_destroy();
                header("Location: logout.php");
                //Termina la ejecución del script
                exit(); 
            }
        }

        //Actualiza la hora de inicio de sesión
        $_SESSION["timeout"] = time();
  
}



//si se ha enviado el id resultado y es correcto...
if(!empty($_GET["id"]) && filter_var($_GET["id"],FILTER_VALIDATE_INT)){

    require_once("conectar.php");
    $id=$_GET["id"];

    
    try{
        $sth = $dbh->prepare ("DELETE FROM resultados WHERE id=?");
        $sth->execute(array($id));

        if($sth->rowCount()>0){
           
           //Recargamos la página
            header('Location: resultados_totales.php');
            exit;

           // echo "Se ha eliminado correctamente";
        }
        

    }catch(PDOException $e) {
         echo $e->getMessage();
    }

    //cerrar la conexión
    $dbh=null;

}else{
    $errores[]= "Error: No se ha encontrado la entrada seleccionada";
    
}


?>

<?php require_once ("header.php") ?>

<div class="container"> 
    <?php 
            if(!empty($errores)){
                echo '<div class="alert alert-danger m-3" role="alert">';
                foreach ($errores as $error){
                    echo "* $error"."<br>";
                }
                echo '</div>';

                echo '<a class="btn btn-success m-3" href="./admin.php">Volver</a>';

            }    
           
    ?>
</div>

<?php require_once ("footer.php") ?>
