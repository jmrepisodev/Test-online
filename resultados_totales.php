<?php 

require_once ("header_admin.php"); 

require_once 'conectar.php';

//En este momento solo existe un test. El test de PHP con id=1
$id_test=1;

//Muestra todos los intentos

try{
    $stmt = $dbh->prepare("SELECT usuarios.username, test.testname, resultados.id, resultados.nota, SUM(detalle_resultados.es_correcta=true) as aciertos, 
    SUM(detalle_resultados.es_correcta=false) as errores, SUM(detalle_resultados.id_respuesta IS NULL) as enblanco FROM usuarios, test, resultados, detalle_resultados WHERE resultados.id_test=test.id AND 
    resultados.id_usuario=usuarios.id AND resultados.id=detalle_resultados.id_resultado AND resultados.id_test=? GROUP BY resultados.id;");
    $stmt->bindParam(1, $id_test);
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
         WHERE resultados.id_test=test.id AND resultados.id_usuario=usuarios.id AND resultados.id_test=? GROUP BY usuarios.username;");
    $stmt1->bindParam(1, $id_test);
    $stmt1->execute();

    $result1 = $stmt1->fetchAll();
   // print_r($result);

    $num_filas1=count($result1);

}catch(PDOException $e) {
    $errores[]= $e->getMessage();
}

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


                

                
            <div class="row mb-3">
                <h3>Notas finales</h3>
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
            
            <div class="row mb-3">
                    <!-- Todos los intentos -->
                <h3>Resultados detallados</h3> 
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
                            <td><a class="btn btn-danger" href="./delete.php?id=<?php isset($row['id']) ? print $row['id'] : '' ?>">Eliminar</a></td>
                            <td><a class="btn btn-success" href="./detalle.php?id=<?php isset($row['id']) ? print $row['id'] : '' ?>&nameuser=<?php isset($row['username']) ? print $row['username'] : '' ?>">Ver detalle</a></td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
                            
        </div>
        
        
<?php  require_once ("footer_admin.php"); ?>
