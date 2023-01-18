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
	
    // $id_test=$_SESSION['id_test'];
	$id_test=1;
	
	require_once 'conectar.php';
	try{
		//ver motores bbdd disponibles
		//print_r(PDO::getAvailableDrivers());

		//Obtenemos las preguntas del test
		$stmt = $dbh->prepare("SELECT * FROM test, preguntas WHERE test.id=preguntas.id_test AND test.id=?");
		$stmt->bindParam(1, $id_test);
		$stmt->execute();
		//devuelve un array bidireccional de preguntas
		$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//print_r($questions);
		//shuffle($questions); ordena de forma aleatoria las preguntas
		$total_questions= $stmt->rowCount();

	}catch(PDOException $e) {
		$errores[]= "Error: " . $e->getMessage();
	}
	?>

<?php require_once ("header.php") ?>

	<main>
		<div class="container">
			<div class="row">
				<div class="mx-auto">
					<div class="text-center shadow p-3 m-3 bg-body rounded">
						<h1 class="fs-1 fw-bold">EXAMEN <?php isset($questions) ? print $questions[0]['testname'] : "" ?></h1>
						<h3><?php isset($questions) ? print $questions[0]['descripcion'] : "" ?></h3>
					</div>
					
					<form method="POST" action="./resultado.php" class="border rounded-3 bg-white p-5 m-3">

					<?php if(isset($questions) && $total_questions>0){for($i=1; $i<=$total_questions; $i++){ ?>
						<!--rellenamos las preguntas (array empieza por 0, id empieza por 1)-->
						<h3 class="fw-bold"><?php echo $i.". ". $questions[$i-1]['question']; ?> </h3>
						<?php 
						try{
							//Obtenemos las respuestas 
							$stmt2 = $dbh->prepare("SELECT * FROM respuestas where id_question=?"); 
							$stmt2->bindParam(1, $questions[$i-1]['id']);
							$stmt2->execute();
							//devuelve un array bidireccional de respuestas
							$answers = $stmt2->fetchAll(PDO::FETCH_ASSOC);
							//print_r($answers);
						
						
						foreach($answers as $answer){ ?> 
							<div class="form-check">
								<input class="form-check-input" type="radio" id="<?php echo $answer['id']; ?>" name="<?php echo "p".$questions[$i-1]['id']; ?>" value="<?php echo $answer['id']; ?>">
								<label class="form-check-label" for="<?php echo $answer['id']; ?>">
									<?php echo $answer['answer']; ?>
								</label>
							</div>
							
						<?php } 
						}catch(PDOException $e) {
							$errores[]= "Error: " . $e->getMessage();
						}
						?>
						<hr>
	<?php 					
						} 
					}else{
						$errores[]= "Error: No existen preguntas disponibles en la base de datos";
					}

			//cerrar la conexión
			$dbh=null;

	?>
						<input class="btn btn-primary fs-3 p-3 mb-3 text-center" style="width:100%" type="submit" name="submit" value="Enviar respuestas">
					</form>
				</div>
			</div>

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
			
			
		</div>
	</main>

	
	<?php require_once ("footer.php") ?>
	
