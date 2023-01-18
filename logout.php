<?php
   session_start();
   //Elimina el contenido de las sesiones
   session_unset();
   //Destruye todas las sesiones
   session_destroy();
   // Redirige a la página principal
   header('Location: index.php');
?>