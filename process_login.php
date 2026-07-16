<?php
session_start();
require 'dbcon.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE username = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();

    if ($usuario['estatus'] != 1) {
        echo "Tu cuenta está inactiva. Contacta al administrador.";
        exit;
    }

    if (password_verify($password, $usuario['password'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];

        // Enviar correo de notificación de inicio de sesión
        enviarCorreoLogin($usuario['nombre'], $usuario['username']);

        header("Location: dashboard.php");
        exit;
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "No existe un usuario con ese nombre de usuario.";
}

function enviarCorreoLogin($nombre, $usernameLogin) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPAuth = true;
        $mail->Username = 'victormalvarez915@gmail.com';
        $mail->Password = 'uozb tgfz zutn rxet'; // ⚠️ genera una nueva
        $mail->SMTPSecure = 'ssl';

        $mail->setFrom('victormalvarez915@gmail.com', 'Mi Empresa - Notificaciones');
        $mail->addAddress('victormalvarez915@gmail.com'); // a dónde llega la alerta
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

        $mail->Subject = 'ALERTA: Nuevo inicio de sesión';
        $mail->Body = '
            <p>Se detectó un inicio de sesión en el sistema.</p>
            <p><b>Usuario:</b> ' . htmlspecialchars($nombre) . '</p>
            <p><b>Username:</b> ' . htmlspecialchars($usernameLogin) . '</p>
            <p><b>Fecha y hora:</b> ' . date('Y-m-d H:i:s') . '</p>
        ';

        $mail->send();
    } catch (Exception $e) {
        error_log('Error al enviar correo de login: ' . $mail->ErrorInfo);
    }
}
?>