<?php 

require_once ("header_admin.php"); 

require_once 'conectar.php';


//En este momento solo existe un test. El test de PHP con id=1
$id_test=1;

// --------Muestra las estadísticas--------

//Calcula las estadísticas generales en base a los datos almacenados en la base de datos
try{
    //consulta la desviación típica, la varianza, la media, la moda, la nota más alta y la nota más baja
    $stmt2 = $dbh->prepare("SELECT ROUND(STDDEV(nota),3) as desviacion, ROUND(VARIANCE(nota),3) as varianza, ROUND(AVG(nota),2) as media, MAX(nota) as max_nota, 
        MIN(nota) as min_nota, (SELECT nota FROM resultados GROUP BY nota ORDER BY count(nota) DESC LIMIT 1) as moda FROM resultados;");
    $stmt2->execute();

    $result2 = $stmt2->fetchAll();
    // print_r($result);

    $num_filas2=count($result2);

    }catch(PDOException $e) {
        $errores[]= $e->getMessage();
    } 

//Estadísticas detalladas
try{
    //consulta la pregunta con mayor número de aciertos
    $stmt3 = $dbh->prepare("SELECT question, count(id_pregunta) as mas_aciertos FROM preguntas, detalle_resultados WHERE preguntas.id=detalle_resultados.id_pregunta AND es_correcta=true GROUP BY id_pregunta ORDER BY count(id_pregunta) DESC LIMIT 1;");
    $stmt3->execute();
    $result3 = $stmt3->fetch();


    //consulta la pregunta con mayor número de fallos
    $stmt4 = $dbh->prepare("SELECT question, count(id_pregunta) as mas_fallos FROM preguntas, detalle_resultados WHERE preguntas.id=detalle_resultados.id_pregunta AND es_correcta=false GROUP BY id_pregunta ORDER BY count(id_pregunta) DESC LIMIT 1;");
    $stmt4->execute();
    $result4 = $stmt4->fetch();

    /*
    SELECT question, sum(detalle_resultados.es_correcta=true) as num_aciertos, sum(detalle_resultados.es_correcta=false) as mum_fallos FROM preguntas, detalle_resultados 
    WHERE preguntas.id=detalle_resultados.id_pregunta GROUP BY id_pregunta ORDER BY num_aciertos DESC;
    */

    // print_r($result);

    }catch(PDOException $e) {
        $errores[]= $e->getMessage();
    } 
    
    
    //cerramos los cursores
    $stmt2->closeCursor();
    $stmt3->closeCursor();
    $stmt4->closeCursor();
    
    $dbh=null; //cierra las conexiones

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

        <!-- Tabla 1 Estadísticas -->
        <h1 class="text-center">Estadísticas</h1>
        <div class="row mb-3">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Media</th>
                        <th>Desviación estándar</th>
                        <th>Varianza</th>
                        <th>Moda</th>
                        <th>Nota máxima</th>
                        <th>Nota mínima</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php  if($stmt2->rowCount()>0 && $result2!==false){
                        foreach($result2 as $row2){  ?>
                        <tr>
                            <td><?= $row2['media'] ?></td>
                            <td><?= $row2['desviacion'] ?></td>
                            <td><?= $row2['varianza'] ?></td>
                            <td><?= $row2['moda'] ?></td>
                            <td><?= $row2['max_nota'] ?></td>
                            <td><?= $row2['min_nota'] ?></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>

        <!-- Tabla 2 Estadísticas -->          
        <div class="row mb-3">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Pregunta más acertada</th>
                        <th>total aciertos</th>      
                    </tr>
                </thead>
                <tbody>
                        <?php if($stmt3->rowCount()>0 && $result3!==false){ ?>
                        <tr>
                            <td><?= $result3['question'] ?></td>
                            <td><?= $result3['mas_aciertos'] ?></td>
                        </tr>
                        <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Tabla 3 Estadísticas -->
        <div class="row mb-3">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Pregunta más fallada</th>
                        <th>total fallos</th>      
                    </tr>
                </thead>
                <tbody>
                        <?php if($stmt4->rowCount()>0 && $result4!==false){ ?>
                        <tr>
                            <td><?= $result4['question'] ?></td>
                            <td><?= $result4['mas_fallos'] ?></td>
                        </tr>
                        <?php } ?>
                </tbody>
            </table>
        </div>

    </div>


  <?php  require_once ("footer_admin.php"); ?>
                
            