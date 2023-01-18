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



if(!empty($_GET["id"]) && isset($_GET['nameuser']) &&  filter_var($_GET["id"],FILTER_VALIDATE_INT)){

    require_once("conectar.php");

    $id=$_GET["id"];

    $nameuser=$_GET['nameuser'];
 

    try{
        $sth = $dbh->prepare ("SELECT question, answer_explained, answer, detalle_resultados.es_correcta FROM detalle_resultados LEFT JOIN respuestas ON detalle_resultados.id_respuesta=respuestas.id 
        LEFT JOIN preguntas ON detalle_resultados.id_pregunta=preguntas.id WHERE detalle_resultados.id_resultado=?;");
        $sth->execute(array($id)); 
        
        $result = $sth->fetchAll();
        // print_r($result);
     
       // $num_filas=count($result);

    }catch(PDOException $e) {
        $errores[]= $e->getMessage();
    }

    //cerrar la conexión
  $dbh=null;


}else{
    $errores[]= "Error: No se ha encontrado la entrada seleccionada";
   
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

                echo '<a class="btn btn-success m-3" href="./admin.php">Volver</a>';

            }    
           
        ?>

        <div class="row">
            <h1 class="text-center my-3">Detalle resultados:</h1> <br>
            <h3>ID test: <?php isset($id) ? print $id : "" ?>  -   Usuario: <?php isset($nameuser) ? print $nameuser : "" ?></h3>
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr class="align-middle text-md-center">
                            <th>Pregunta</th>
                            <th>Respuesta</th>
                            <th>Resultado</th>
                            <th>Solución</th>
                        </tr>
                    </thead>
                

    <?php

            if($sth->rowCount()>0 && $result!==false){ foreach($result as $row){
    ?>
               <tr class="align-middle bg-white">
                    <td data-titulo="Pregunta"><?php isset($row['question']) ? print $row['question']: print "No disponible" ?></td>
                    <td data-titulo="Respuesta"><?php isset($row['answer']) ? print $row['answer'] : print "No respondida" ?></td>
                    <td data-titulo="Resultado"><?php if(isset($row['es_correcta'])){ 
                        if($row['es_correcta']==true){
                            print "<span style='color: green;''> Correcta </span><br>";
                        }else{
                            print "<span style='color: red;'> Incorrecta </span><br>";
                        }
                    }else{
                        print "<span style='color: red;'> No respondida </span><br>";
                    } ?>
                    </td>
                    <td data-titulo="Solución"><?php isset($row['answer_explained']) ? print $row['answer_explained'] : print "No disponible" ?></td>
                    
                </tr>
    <?php
            }
        }
            
    ?>

                </table> 

        <div class="mb-5">
            <?php if($_SESSION['rol']=="admin" ) { 
                echo "<a class='btn btn-success my-3 float-start' href='./resultados_totales.php'>Volver</a>";
            }else{
                echo "<a class='btn btn-success my-3 float-start' href='./ver_mis_tests.php'>Volver</a>";
            }
                
            ?>
        </div>

                

        </div>

        
<?php require_once ("footer.php") ?>