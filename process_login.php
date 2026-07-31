<?php
session_start();
require 'dbcon.php';
require 'smtp_config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo "Completa todos los campos.";
    exit;
}

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
        $_SESSION['username'] = $usuario['username'];

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

function enviarCorreoLogin($nombre, $usernameLogin)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->Port = SMTP_PORT;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_PORT === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom(SMTP_USER, 'Mi Empresa - Notificaciones');
        $mail->addAddress(SMTP_USER); // a dónde llega la alerta (tú mismo, como admin)
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