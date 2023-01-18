<?php 

session_start();

// Si el usuario no está logueado se redirige a login.php
if (!isset($_SESSION['login']) || !isset($_SESSION['id_usuario'])) {
	header('Location: login.php');
	exit(); // termina la ejecución del script

}else{ //si la sesión está iniciada...

        if( !isset($_SESSION['rol']) || $_SESSION['rol'] != "admin"){
            header('Location: login.php');
            exit(); // termina la ejecución del script
        }
        
    
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

        




?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="José Miguel Repiso García">
    <meta name="description" content="Test - Admin panel">

    <title>MyQuizz</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <style>
        
        @media all and (max-width: 768px) {
            table tbody tr, table thead tr {
                display:flex;
                flex-direction:column;
                margin:1em;
            }

            table thead{
                display:none
            }

            table td[data-titulo]::before{
                content: attr(data-titulo) ": " ;
                color: black;
                font-weight: bold;
            }
        }

    </style>



</head>

<body class="d-flex flex-column min-vh-100">

    <!-- navbar start-->
    <div class="container-fluid bg-dark">
        <nav class="navbar navbar-expand-lg p-2 navbar-dark">
            <!-- Navbar brand -->
            <a class="navbar-brand" href="#">
                <img src="./img/logo_php.png" alt="logo" style="width:40px;" class="rounded-pill">
                MyQuizz
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="navbar-nav me-auto mb-2 mb-lg-0">
                    <a class="btn btn-primary me-3" href="../index.html"><i class="fas fa-list me-2"></i>Index</a>
                    <a class="btn btn-success me-3" href="./admin.php"><i class="fas fa-home me-2"></i>Home</a>
                </div>
                <div class="navbar-nav navbar-right ms-auto mb-2 mb-lg-0">
                    <?php if(isset($_SESSION['login'])){
                            
                          echo "<div class='dropdown me-3'>
                                    <a class='btn btn-success nav-link dropdown-toggle text-white' href='#' id='navbarDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                        <i class='fas fa-user-circle me-2'></i>".$_SESSION['username'].' ('.$_SESSION['rol'].')'."</a>
                                    </a>
                                    <ul class='dropdown-menu dropdown-menu-dark' aria-labelledby='navbarDropdown'>
                                        <li><a class='dropdown-item' href='./logout.php'>Cerrar sesión</a></li>
                                    </ul>
                                </div>";
                            
                        }else{ 
                            echo "<a class='btn btn-success me-3' href='./login.php'><i class='fas fa-sign-in-alt me-2'></i></i>Login</a>";
                            echo "<a class='btn btn-danger me-3' href='./registrar.php'><i class='fas fa-solid fa-user-plus me-2'></i>Registrar</a>";
                        
                        } 

                        ?>


                </div>
            </div>

        </nav>
    </div>
    <!-- navbar end-->

     
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <!-- sidebar start-->
            <div class="col-auto col-sm-3 col-xl-2 px-sm-2 px-0 bg-dark">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <a href="/"
                        class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-5 d-none d-sm-inline">Menu</span>
                    </a>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start"
                        id="menu">
                        <li class="nav-item">
                            <a href="./admin.php" class="nav-link align-middle px-0">
                                <i class="fs-4 fas fa-home"></i> <span class="ms-1 d-none d-sm-inline">Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="./resultados_totales.php" class="nav-link px-0 align-middle">
                                <i class="fs-4 fas fa-table"></i> <span class="ms-1 d-none d-sm-inline">Resultados</span></a>
                        </li>
                        <li>
                            <a href="./estadisticas.php" class="nav-link px-0 align-middle">
                                <i class="fs-4 fas fa-bars"></i> <span class="ms-1 d-none d-sm-inline">Estadisticas</span></a>
                        </li>

                    </ul>
                    
                </div>
            </div>
             <!-- navbar end -->

            <!-- content start -->
            <div class="col-auto col-sm-9 col-xl-10 py-3">