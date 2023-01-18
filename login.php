<?php
   session_start();
   // Si el usuario ya está logueado se redirige a la página principal
   if (isset($_SESSION['login'])) {
       header('Location: index.php');
       exit();
   }

   require_once ("conectar.php");
   
   if(isset($_POST["submit"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

        $errores=array();
      
        function filtrado($datos){
            $datos = trim($datos); // Elimina espacios antes y después de los datos
            $datos = stripslashes($datos); // Elimina backslashes \
            $datos = htmlspecialchars($datos); // Traduce caracteres especiales en entidades HTML
            return $datos;
        }

        
        //el email es obligatorio. Si está vacío se lanza un error
        if (empty($_POST["email"])) {
            $errores[] = "Email es obligatorio";
        } else {
            //comprobamos que el email está correctamente formado
            if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $errores[] = "Formato de email no válido";
            }
        }

        if (empty($_POST["password"])) {
            $errores[] = "No ha introducido ninguna contraseña";
        }

        //Si no hay errores se procesan los datos
        if(count($errores)==0){
            $email = filtrado($_POST["email"]);
            $password = filtrado($_POST["password"]);

            try{
                //consultamos si existe el usuario en la base de datos
                $stmt = $dbh->prepare("SELECT * FROM usuarios WHERE email= ?");
                $stmt->bindParam(1, $email);
                $stmt->execute();
                //devuelve un array bidireccional 
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $count= $stmt->rowCount();
                
                //si la cuenta existe, verificamos que la contraseña sea correcta y existe un rol con esa cuenta
                if($count>0 && $row!==false){

                    $id_usuario=$row["id"];
                    $username=$row["username"];
                    $password_bbdd=$row["password"];
                    $rol=$row["rol"];

                     //comprobamos si coincide la contraseña introducida con la contraseña almacenada en la base de datos
                     if (password_verify($password, $password_bbdd)) {
                        // Verification success! 

                        // crea un nuevo ID único para representar la sesión actual del usuario. Elimina la sesión anterior
                        session_regenerate_id(true);
                         //Almacena la hora actual de inicio de sesión
                        $_SESSION["timeout"] = time();

                        $_SESSION['login'] = TRUE;
                        $_SESSION['id_usuario'] = $id_usuario;
                        $_SESSION['username'] = $username;

                        //Doble verificación: asegura que el inicio de sesión pertenece a la app test
                        $_SESSION['app']="test"; 
                        
                        switch($rol){
                            case "admin":
                                $_SESSION['rol'] = "admin";
                                header("location: admin.php");
                                break;
                            default:
                                 $_SESSION['rol'] = "user";
                                 header("location: index.php");
                        }
                       

                        
                    } else {
                        // Incorrect password
                        $errores[] = "La contraseña no es correcta";
                    }

                }else{
                    $errores[] = "No existe una cuenta con esos datos";
                }

                // Note: remember to use password_hash in your registration file to store the hashed passwords.
            
            }catch(PDOException $e) {
                $errores[]= "Error: " . $e->getMessage();
            }   

        }
   

   }

   $dbh=null; //cierra las conexiones
?>

<?php require_once ("header.php") ?>


       
        <!-- Sign In Start -->
        <div class="container-fluid p-3">
            <div class="row d-flex align-items-center justify-content-center" style="min-height: 80vh;">
                <div class="col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="card rounded-3 m-3">
                        <div class="row g-0">
                            <div class="card-header text-center text-white fw-bold fs-3 p-3 bg-dark">Login</div>
                            <div class="col-md-4 mx-auto mt-5 mb-5" style="max-width:220px;">
                                <img src="./img/logo_php.png" class="img-fluid rounded mx-auto d-block p-sm-3" alt="logo">
                            </div>
                            <div class="col-md-8 border-start">
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="floatingInput" name="email" placeholder="Email" required>
                                            <label for="floatingInput">Email address</label>
                                        </div>
                                        <div class="form-floating mb-4">
                                            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                                            <label for="floatingPassword">Password</label>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                            <label class="form-check-label" for="remember">Recuérdame</label>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-success p-3 mb-3" style="width:100%;">Login</button>
                                        <p>¿Aún no tienes una cuenta? <a href="./registrar.php">Registrar</a></p>
                                    </form>
                                    <?php 
                                        if(isset($errores)){
                                        echo '<div class="alert alert-danger" role="alert">';
                                            foreach ($errores as $error){
                                                echo "* $error"."<br>";
                                            }
                                            echo '</div>';

                                        }    
                                    ?>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sign In End -->
  
    




<?php require_once ("footer.php") ?>