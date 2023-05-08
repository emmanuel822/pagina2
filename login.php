<?php
include('config/db.php');

if(isset($_SESSION['nombre'])){
  header('location: home');
}

$errores = '';

function contarIntentos($email, $ipv4, $ipv6) {
  global $coon;
  $query = "SELECT COUNT(*) as intentos FROM logs WHERE usuario = '$email' AND ipv4 = '$ipv4' AND ipv6 = '$ipv6' AND estatus = 'Fracaso'";
  $resultado = mysqli_query($coon, $query);
  $fila = mysqli_fetch_assoc($resultado);
  return $fila['intentos'];
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  if(!empty($_POST['email']) && !empty($_POST['password'])){

    $email = $_POST['email'];
    $pass = md5($_POST['password']);
  
    $query = "SELECT * from usuarios where (email='$email' and passwordd = '$pass')";

    $ip = $_SERVER['REMOTE_ADDR'];
    $ip6 = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $browser = $_SERVER['HTTP_USER_AGENT'];
    $os = php_uname('s');
    $user = $_POST['email'];
    date_default_timezone_set('America/Mexico_City');
    $date = date('Y-m-d H:i:s');

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
    } else {
      if(mysqli_num_rows($resultado = mysqli_query($coon,$query)) > 0){
        foreach($resultado as $row){
          $_SESSION['nombre'] = $row['nombre'];
          $_SESSION['email'] = $row['email'];
        } 
        $estatus = "Exitoso";
        $intentos = contarIntentos($email, $ip, $ip6) +1;
        $query_log = "INSERT INTO logs (fecha_hora, ipv4, ipv6, browser, os, usuario, estatus, intentos) VALUES ('$date', '$ip', '$ip6', '$browser', '$os', '$user', '$estatus', '$intentos1')";
        mysqli_query($coon, $query_log);
        header('location: home');
      } else {
        $errores .= 'Correo Electronico o contraseña incorrecto';
        $estatus = "Fracaso";
        $intentos = contarIntentos($email, $ip, $ip6) + 1;
        $query_log = "INSERT INTO logs (fecha_hora, ipv4, ipv6, browser, os, usuario, estatus, intentos) VALUES ('$date', '$ip', '$ip6', '$browser', '$os', '$user', '$estatus', '$intentos')";
        mysqli_query($coon, $query_log);
      }
    }

  }else{
    $errores .= 'Todos los datos son necesarios';
  }
}
mysqli_close($coon);


include('views/login.view.php');
?>