<!DOCTYPE html>
<html lang="es">
<body style="font-family: Arial, sans-serif;">
    <p>Hola {{ $usuario->name }},</p>
    <p>Tu código de verificación es:</p>
    <p style="font-size: 28px; letter-spacing: 6px;"><strong>{{ $codigo }}</strong></p>
    <p>Vence en 5 minutos. Si no intentaste iniciar sesión, ignora este correo.</p>
</body>
</html>