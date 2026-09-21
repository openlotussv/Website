<?php
// Ocultar avisos para proteger la respuesta AJAX
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

// 1. Importar las clases necesarias de PHPMailer 6
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 2. Requerir los archivos desde la nueva subcarpeta 'phpmailer/'
// Requerir los archivos apuntando correctamente a la subcarpeta 'phpmailer/'
require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar y limpiar los datos
    $nombre  = strip_tags(trim($_POST["name"] ?? ''));
    $email   = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $empresa = strip_tags(trim($_POST["company"] ?? ''));
    $asunto  = strip_tags(trim($_POST["subject"] ?? ''));
    $mensaje = strip_tags(trim($_POST["message"] ?? ''));

    if (empty($nombre) || empty($email) || empty($mensaje) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "MF255";
        exit;
    }

    // 3. Instanciar PHPMailer usando el Namespace
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';     // Cambia por tu servidor SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'openlotus.sv@gmail.com';  // Tu usuario SMTP
        $mail->Password   = 'qszp izbz xvgf ldpz';  // Tu contraseña
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Estándar moderno para TLS (o PHPMailer::ENCRYPTION_SMTPS para 465)
        $mail->Port       = 587;                       

        // Remitente y Destinatario
        $mail->setFrom('openlotus.sv@gmail.com', 'Formulario Web - Open Lotus');
        $mail->addAddress('laura.escobar9491@gmail.com', 'Soporte Open Lotus'); 
        $mail->addReplyTo($email, $nombre);            

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = "Nuevo mensaje de contacto: " . $asunto;
        
        $cuerpoHtml = "<h3>Detalles de la solicitud de contacto:</h3>";
        $cuerpoHtml .= "<p><b>Nombre:</b> {$nombre}</p>";
        $cuerpoHtml .= "<p><b>Correo Electrónico:</b> {$email}</p>";
        $cuerpoHtml .= "<p><b>Empresa:</b> {$empresa}</p>";
        $cuerpoHtml .= "<p><b>Asunto:</b> {$asunto}</p>";
        $cuerpoHtml .= "<p><b>Mensaje:</b><br>{$mensaje}</p>";

        $mail->Body    = $cuerpoHtml;
        $mail->AltBody = "Nombre: {$nombre}\nCorreo: {$email}\nEmpresa: {$empresa}\nAsunto: {$asunto}\nMensaje:\n{$mensaje}";

        // Enviar
        $mail->send();
        
        http_response_code(200);
        echo "MF000"; // Código de éxito para tu JS
    } catch (Exception $e) {
        http_response_code(500);
        echo "MF254"; // Código de error para tu JS
    }
} else {
    http_response_code(403);
    echo "MF255";
}
