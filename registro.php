<?php
 include('config/db.php');
 if(isset($_SESSION['nombre'])){
   header('location: home.php');
}
 $errores = '';
 
 if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $repitepass = $_POST['repitepass'];

    $ip = $_SERVER['REMOTE_ADDR'];
    $captcha = $_POST['g-recaptcha-response'];
    $secretkey = "6Le2AdclAAAAAMMbYQSNtyslIXHJwUp0QOTFM7c6";

    $url = "https://www.google.com/recaptcha/api/siteverify?";

    $data = array(
        'secret' => $secretkey,
        'response' => $captcha,
        'remoteip' => $ip
    );

    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context = stream_context_create($options);
    $respuesta = file_get_contents($url, false, $context);

    $atributos = json_decode($respuesta, true);
    
    if (!$atributos['success']){
        $errores .= 'Por favor verifica el Captcha';
    }


    if(!empty($nombre) && !empty($email) && !empty($password) && !empty($repitepass)){

      $nombre = filter_var(trim($nombre),FILTER_SANITIZE_STRING);
      $email = filter_var(trim($email),FILTER_SANITIZE_EMAIL);
      $password = trim($password);
      $repitepass = trim($repitepass);
      
 
      $query = "SELECT * from usuarios where email='$email' limit 1";
      $resultado = mysqli_query($coon,$query);

      if(mysqli_num_rows($resultado) > 0){
         $errores .= 'EL Email ya existe </br>';
      }

      if($password != $repitepass){
         $errores .= 'las contrase;as no coinciden';
      }


      if(!$errores){
         $password = md5($password);
         $query = "INSERT INTO usuarios(nombre,email,passwordd) values('$nombre','$email','$password')";
         if(mysqli_query($coon,$query)){
            $_SESSION['nombre'] = $nombre;
            $_SESSION['email'] = $email;
        }
        mysqli_close($coon); 
        header('location: login.php');
      }    
      }else{
         $errores .= 'Todos los datos son obligatorios';
      }      
 }
 
include('views/registro.view.php'); 
?>
