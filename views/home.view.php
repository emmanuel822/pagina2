<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
</head>
<style>
    body {
            animation-name: animacion;
            animation-duration: 2s;
            animation-delay: 0s;
            animation-fill-mode: forwards;
        }

        @keyframes animacion {
            from {
                opacity: 0;
                transform: scale(0.5);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
</style>
<body>
    <h1>Bienvendo <?php echo $_SESSION['nombre'];?> </h1>
    <a href="logout.php" style="outline: none;">
        <button type="submit" class="btn btn-danger" name="btn">Salir</button>
    </a>
</body>
</html>