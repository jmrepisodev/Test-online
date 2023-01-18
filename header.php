
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="José Miguel Repiso García">
    <meta name="description" content="Test">
   
    <title>MyQuizz</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
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

<body class="d-flex flex-column min-vh-100" style="background-color: #eee;">
    <div class="container-fluid bg-dark mb-3">
        <nav class="navbar navbar-expand-lg p-2 navbar-dark">
            <div class="container">
                <!-- Navbar brand -->
                <a class="navbar-brand" href="#">
                    <img src="./img/logo_php.png" alt="logo" style="max-width:40px;" class="rounded-pill">
                    MyQuizz
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <div class="navbar-nav me-auto mb-2 mb-lg-0">
                        <a class="btn btn-primary me-3" href="../index.html"><i class="fas fa-list me-2"></i>Index</a>
                        <a class="btn btn-success me-3" href="./index.php"><i class="fas fa-home me-2"></i>Home</a>
                    </div>
                    <div class="navbar-nav navbar-right ms-auto mb-2 mb-lg-0">
                        <?php if(isset($_SESSION['login'])){
                            
                          echo "<div class='dropdown me-3'>
                                    <a class='btn btn-success nav-link dropdown-toggle text-white' href='#' id='navbarDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                        <i class='fas fa-user-circle me-2'></i>".$_SESSION['username'].' ('.$_SESSION['rol'].')'."</a>
                                    </a>
                                    <ul class='dropdown-menu dropdown-menu-dark' aria-labelledby='navbarDropdown'>
                                        <li><a class='dropdown-item' href='./ver_mis_tests.php'>Ver mis tests</a></li>
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
            </div>
        </nav>
    </div>