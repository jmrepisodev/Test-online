<?php require_once ("header_admin.php") ?>


<div class="container">

        <?php 
        
        if(!empty($errores)){
            echo '<div class="alert alert-danger" role="alert">';
            foreach ($errores as $error){
                echo "* $error"."<br>";
            }
            echo '</div>';

        }    
    ?>



    <h1 class="text-center fs-1 fw-bold shadow p-3 mt-3 bg-body rounded">Panel de administración</h1> <br>
    <p>Bienvenido al panel de administración de QUIZZ TEST</p>



 
</div>

   

<?php require_once ("footer_admin.php") ?>