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

    $fechaActual = date("d-m-Y H:i:s");
    $score=0;
    $acierto=0;
    $error=0;
    $enBlanco=0;

    require_once 'conectar.php';
	
    if(isset($_POST["submit"]) && $_SERVER["REQUEST_METHOD"] == "POST"){
        
        $id_test=1;

        //Generamos un registro de resultados
        try{
            $stmt = $dbh->prepare("INSERT INTO resultados (id_usuario, id_test) VALUES (?,?)");
            $stmt->bindValue(1, $id_usuario);
            $stmt->bindValue(2, $id_test);
            $stmt->execute();
            //Obtenemos el id de resultados
            $id_resultado= $dbh->lastInsertId();

        }catch(PDOException $e) {
            $errores[]= "Error: " . $e->getMessage();
        } 


        try{
            //Obtenemos las preguntas del test
            $stmt1 = $dbh->prepare("SELECT * FROM preguntas WHERE id_test=?");
            $stmt1->bindParam(1, $id_test);
            $stmt1->execute();
            //devuelve un array bidireccional de preguntas
            $questions = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            //print_r($questions);
            $total_questions= $stmt1->rowCount(); 

        }catch(PDOException $e) {
            $errores[]= "Error: " . $e->getMessage();
        }

    
        if(isset($questions) && $total_questions>0){

            //Recorremos las respuestas del usuario en cada pregunta
            foreach($questions as $question){
                //array empieza por 0, id empieza por 1
                $id_pregunta=$question['id'];
                $pregunta="p".$id_pregunta;
            
                //si la pregunta está definida = ha sido respondida
                if(isset($_POST[$pregunta])) {
                    $id_selected=$_POST[$pregunta];
                
                    try{
                        //Obtenemos la respuesta correcta de cada pregunta 
                        $stmt2 = $dbh->prepare("SELECT id FROM respuestas WHERE id_question=? AND es_correcta=true");
                        $stmt2->bindParam(1, $id_pregunta);
                        $stmt2->execute();
                        //devuelve un array bidireccional de respuestas
                        $answer = $stmt2->fetch(PDO::FETCH_ASSOC);

                        //si hay resultados...
                        if($answer!==false){

                            $id_correcta=$answer["id"];
                        
                            //print_r($answer);
                        
                            //comparamos la respuesta del usuario con la respuesta correcta
                            if($id_selected==$id_correcta){
                            // echo '<span style="color: green;">'.$pregunta." Correcta".'</span><br>';
                                $es_correcta=true;
                                $acierto+=1;
                                $resultado="Correcto";
                                //incrementamos la puntuación del usuario
                                $score+=1;
                            }else{
                                $es_correcta=false;
                                $error+=1;
                                $resultado="Incorrecto";
                            // echo '<span style="color: red;">'.$pregunta." NO correcta".'</span><br>';
                            } 
                        }
                        

                    

                    }catch(PDOException $e) {
                        $errores[]= "Error: " . $e->getMessage();
                    } 

                }else{ //La pregunta no ha sido respondida
                    $id_selected=null;
                    $es_correcta=null;
                    $enBlanco+=1;
                    $resultado="No respondida";
                // echo $pregunta." NO respondida <br>";
                }


                //Generamos un registro detallado de cada resultado
                try {

                    $stmt3 = $dbh->prepare('INSERT INTO detalle_resultados (id_resultado, id_pregunta, id_respuesta, es_correcta) VALUES (?, ?, ?, ?)');
                //option 1:
                // $stmt3->execute(array($id_usuario, $id_pregunta, $id_respuesta, $es_correcta));
            
                    //option 2:
                    $stmt3->bindParam(1, $id_resultado);
                    $stmt3->bindParam(2, $id_pregunta);
                    $stmt3->bindParam(3, $id_selected);
                    $stmt3->bindParam(4, $es_correcta);
            
                    $stmt3 ->execute(); 

                    if($stmt3->rowCount()>0){
    
                /*    
                    
                    */

            } 
                } catch(PDOException $e) {
                    $errores[]= $e->getMessage();
                }
                        
            }

        }
            //Actualiza las puntuaciones del usuario en la base de datos
            try {
                $stmt4 = $dbh->prepare('UPDATE resultados SET nota=? WHERE id=?');
                //option 1:
                // $stmt3->execute(array($id_usuario, $id_test, $score));
        
                //option 2:
                $stmt4->bindParam(1, $score);
                $stmt4->bindParam(2, $id_resultado);
        
                $stmt4 ->execute();
        
            //  echo "score test agregada";
            } catch(PDOException $e) {
                $errores[]= $e->getMessage();
            }
        
            
            }

            //Recupera los resultados del usuario
            try{
                $stmt5 = $dbh->prepare ("SELECT question, answer_explained, answer, detalle_resultados.es_correcta FROM detalle_resultados LEFT JOIN respuestas ON detalle_resultados.id_respuesta=respuestas.id 
                LEFT JOIN preguntas ON detalle_resultados.id_pregunta=preguntas.id WHERE detalle_resultados.id_resultado=?;");
                $stmt5->execute(array($id_resultado)); 
                
                $result = $stmt5->fetchAll();
                // print_r($result);
             
               // $num_filas=count($result);
        
            }catch(PDOException $e) {
                $errores[]= $e->getMessage();
            }

            //cerrar la conexión
            $dbh=null;
            
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

                    echo '<a class="btn btn-success m-3" href="./index.php">Volver</a>';

                }    
            
        ?>
        <div class="row p-3 m-3">
            <div>
                <h3 class="fs-2 fw-bold label-color-2">Test PHP</h3>
                <ul class="list-unstyled my-3">
                    <li class="d-inline-block fs-15 label-color-1 fw-500 me-3 mb-2"><i class="fas fas fa-clipboard-list me-1 label-color-3 fs-18">
                        </i> Número de preguntas: <span class="label-color-2"><?php isset($total_questions) ? print $total_questions: "" ?></span>
                    </li>

                    <li class="d-inline-block fs-15 label-color-1 fw-500 me-3 mb-2">
                        <i class="fas fa-calendar me-1 label-color-3 fs-18"></i> Fecha: <span class="label-color-2"><?php isset($fechaActual) ? print $fechaActual : "" ?></span>
                    </li>
                </ul>
            </div>
            <div class="text-center border">
                <h3 class="fw-bold fs-20 label-color-2 mb-3">Tus resultados</h3>
                <img src="./img/trofeo.png" class="img-fluid my-3" alt="trofeo">
                <ul class="list-unstyled d-flex justify-content-center">
                    <li class="fs-6 fw-bold label-color-1 px-3 px-lg-4 py-1 lh-24 d-flex align-items-center flex-column numbers-border-right">
                        Aciertos <p class="label-color-2 fw-600 mt-1 mb-0"><?php isset($acierto) ? print $acierto : "" ?></p>
                    </li>
                    <li class="fs-6 fw-bold label-color-1 px-3 px-lg-4 py-1 lh-24 d-flex align-items-center flex-column numbers-border-right">
                        Errores <p class="label-color-2 fw-600 mt-1 mb-0"><?php isset($error) ? print $error : "" ?></p>
                    </li>
                    <li class="fs-6 fw-bold label-color-1 px-3 px-lg-4 py-1 lh-24 d-flex align-items-center flex-column">
                         No respondidas <p class="label-color-2 fw-600 mt-1 mb-0"><?php isset($enBlanco) ? print $enBlanco : "" ?></p>
                    </li>
                </ul>
                <p class="fs-2 fw-bold label-color-2 mt-3 mt-lg-4">Nota: <?php isset($score) ? print $score : "" ?></p>
            </div>

            <table class="table table-bordered table-hover my-3">
            <thead class="table-dark">
                <tr class="align-middle text-md-center">
                    <th>Pregunta</th>
                    <th>Respuesta</th>
                    <th>Resultado</th>
                    <th>Solución</th>
                </tr>
            </thead>
                <!--Mostramos los resultados en una tabla-->
                <?php

                if($stmt5->rowCount()>0 && $result!==false){ foreach($result as $row){
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
                } }
                ?>
            </table>
                <br><hr>
                <div class="mb-5">
                    <span class="fs-3 fw-bold m-3 float-end">Nota: <span style="color: blue;"> <?php isset($score) && isset($total_questions) ? print $score."/".$total_questions : ""?> </span></span> 
                    <a class="btn btn-success m-3 float-start" href="./index.php">Volver</a>
                </div>
                <div class="clearfix"></div>
  
            </div> <!-- fin row -->

        
        </div>  <!-- fin container -->

    
        <?php require_once ("footer.php") ?>