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

//En este momento solo existe un test. El test de PHP con id=1
$id_test=1;

if(!empty($_SESSION['id_usuario']) && filter_var($_SESSION['id_usuario'],FILTER_VALIDATE_INT)){

    require_once("conectar.php");

    $id_usuario=$_SESSION['id_usuario'];

    //Muestra todos los intentos

    try{
        $stmt = $dbh->prepare("SELECT usuarios.username, test.testname, resultados.id, resultados.nota, SUM(detalle_resultados.es_correcta=true) as aciertos, 
        SUM(detalle_resultados.es_correcta=false) as errores, SUM(detalle_resultados.id_respuesta IS NULL) as enblanco FROM usuarios, test, resultados, detalle_resultados WHERE resultados.id_test=test.id AND resultados.id_usuario=usuarios.id 
        AND resultados.id=detalle_resultados.id_resultado AND resultados.id_test=? AND resultados.id_usuario=? GROUP BY resultados.id");
        $stmt->bindParam(1, $id_test,);
        $stmt->bindParam(2, $id_usuario);
        $stmt->execute();

        $result = $stmt->fetchAll();
    // print_r($result);

        $num_filas=count($result);

    }catch(PDOException $e) {
        $errores[]= $e->getMessage();
    }


    //Muestra las notas finales
    try{
        $stmt1 = $dbh->prepare("SELECT usuarios.username, test.testname, MAX(resultados.nota) as nota_final FROM usuarios, test, resultados
            WHERE resultados.id_test=test.id AND resultados.id_usuario=usuarios.id AND resultados.id_test=? AND resultados.id_usuario=? GROUP BY usuarios.username;");
        $stmt1->bindParam(1, $id_test);
        $stmt1->bindParam(2, $id_usuario);
        $stmt1->execute();

        $result1 = $stmt1->fetchAll();
    // print_r($result);

        $num_filas1=count($result1);

    }catch(PDOException $e) {
        $errores[]= $e->getMessage();
    }

    $dbh=null; //cierra las conexiones

}else{
    $errores[]= "Error: El usuario especificado no existe";
}

    require_once ("header.php"); 
?>

    <div class="container">

   

        <?php 
        if(!empty($errores)){
            echo '<div class="alert alert-danger m-3" role="alert">';
            foreach ($errores as $error){
                echo "* $error"."<br>";
            }
            echo '</div>';

            echo '<a class="btn btn-success m-3" href="./index.html">Volver</a>';

        }    

        ?>

            
        <div class="row p-3 m-3">
            <h3>Nota final</h3>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr class="align-middle text-md-center">
                        <th>Nombre</th>
                        <th>Test</th>
                        <th>Nota final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($stmt1->rowCount()>0 && $result1!==false){foreach($result1 as $row1){ ?>
                    <tr class="align-middle text-md-center bg-white">
                        <td data-titulo="Nombre"><?= $row1['username'] ?></td>
                        <td data-titulo="Test"><?= $row1['testname'] ?></td>
                        <td data-titulo="Nota"><?= $row1['nota_final'] ?></td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>

        <div class="row p-3 m-3">
                <!-- Todos los intentos -->
            <h3>Ver todos los intentos</h3> 
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr class="align-middle text-md-center">
                        <th>id</th>
                        <th>Nombre</th>
                        <th>Test</th>
                        <th>Aciertos</th>
                        <th>Errores</th>
                        <th>En blanco</th>
                        <th>Nota</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if($stmt->rowCount()>0 && $result!==false){foreach($result as $row){  //si hay registros en la base de datos, actualiza la tabla de notas ?> 
                    <tr class="align-middle text-md-center bg-white">
                        <td data-titulo="ID"><?php isset($row['id']) ? print $row['id'] : '' ?></td>
                        <td data-titulo="Nombre"><?php isset($row['username']) ? print $row['username'] : '' ?></td>
                        <td data-titulo="Test"><?php isset($row['testname']) ? print $row['testname'] : '' ?></td>
                        <td data-titulo="Aciertos"><?php isset($row['aciertos']) ? print $row['aciertos'] : print '0' ?></td>
                        <td data-titulo="Errores"><?php isset($row['errores']) ? print $row['errores'] : print '0'  ?></td>
                        <td data-titulo="En blanco"><?php isset($row['enblanco']) ? print $row['enblanco'] : print '0' ?></td>
                        <td data-titulo="Nota"><?php isset($row['nota']) ? print $row['nota'] : print '0' ?></td>
                        <td><a class="btn btn-success" href="./detalle.php?id=<?php isset($row['id']) ? print $row['id'] : '' ?>&nameuser=<?php isset($row['username']) ? print $row['username'] : '' ?>">Ver detalle</a></td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
                        
    </div>

   
       
        
        
<?php  require_once ("footer.php"); ?>
