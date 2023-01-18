<?php 

session_start();

if(!empty($_SESSION['login'])) { //si la sesión está iniciada...

    //Si la sesión no pertenece a esta app, destruimos la sesión y redirigimos a la página de login
    if(isset($_SESSION['app']) && $_SESSION['app']!='test'){
        session_destroy();
        header('Location: login.php');
        exit();
    }

    $id_usuario=$_SESSION['id_usuario'];
    $username=$_SESSION['username'];
    $rol=$_SESSION['rol'];

    if(isset($_SESSION['rol']) && $_SESSION['rol'] == "admin"){ //si es admin, redirige al panel de administración
        header('Location: admin.php');
        exit(); // termina la ejecución del script
    }


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
        



require_once ('conectar.php');

$id_test=1; // test PHP

if(isset($_SESSION["login"]) && !empty($_SESSION['id_usuario'])){

    $id_usuario=$_SESSION['id_usuario'];

    try{
        $stmt1 = $dbh->prepare("SELECT COUNT(*) AS total_intentos FROM resultados WHERE id_usuario=? and id_test=?");
        $stmt1->execute(array($id_usuario, $id_test));
        $row = $stmt1->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e) {
        $errores[]= $e->getMessage();
    }

    if($row!==false && !empty($row["total_intentos"])){
        $num_intentos=$row["total_intentos"];
    }

}



try{
    $stmt2 = $dbh->prepare("SELECT * FROM preguntas WHERE id_test=?");
    $stmt2->execute(array($id_test));
    $total_questions= $stmt2->rowCount();
}catch(PDOException $e) {
    $errores[]= $e->getMessage();
}


$dbh=null; // cerramos las conexiones


?>

<?php require_once ("header.php") ?>

  
    <div class="container">  

        <div class="row mb-3 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
            <div class="card m-3" style="max-width: 28rem;">
                <div class="mx-auto" style="max-width:320px;">
                    <img src="./img/logo_php.png" class="card-img-top img-fluid p-2"  alt="logo">
                </div>
                <div class="card-body">
                    <ul style="list-style:none;">
                        <li><strong>Número de preguntas: </strong><?php isset($total_questions) ? print $total_questions : "" ?> </li>
                        <li><strong>Tipo:</strong> Test de preguntas de elección múltiple</li>
                        <li><strong>Tiempo estimado:</strong> <?php isset($total_questions) ? print $total_questions*2 : ""; ?> minutos</li>
                    </ul>
                    <?php if(isset($_SESSION['login'])){

                            if(isset($num_intentos) && $num_intentos>=3){
                                $errores[]= "Ha superado el número máximo de intentos";
                            }else{
                                echo "<a class='btn btn-primary p-3 text-center fs-3 mt-3' style='width: 100%;' href='./test.php'><i class='fas fa-play me-2'></i></i>Empezar Test</a>";
                            } 
                                
                        }else{ 
                            echo "<a class='btn btn-success p-3 text-center fs-3 mt-3' style='width: 100%;' href='./login.php'><i class='fas fa-sign-in-alt me-2'></i></i>Login</a>";
                        
                        } 

                    ?>
                </div>
            </div>

            <?php 
            if(!empty($errores)){
                echo '<div class="alert alert-danger m-3" role="alert">';
                foreach ($errores as $error){
                    echo "* $error"."<br>";
                }
                echo '</div>';

              //  echo '<a class="btn btn-success m-3" href="./index.html">Volver</a>';

            }    
           
        ?>
        </div>

        
     
    </div>
	



 <?php require_once ("footer.php") ?>
