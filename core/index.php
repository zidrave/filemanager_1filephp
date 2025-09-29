<?php
// Configuración de autenticación
$password = "1234";
$cookie_name = "file_manager_auth";
$cookie_duration = 3600; // 1 hora

// Verificar si se envió la contraseña
if (isset($_POST['password'])) {
    if ($_POST['password'] === $password) {
        setcookie($cookie_name, hash('sha256', $password), time() + $cookie_duration, '/');
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = "Contraseña incorrecta";
    }
}

// Verificar autenticación
$is_authenticated = isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] === hash('sha256', $password);

// Cerrar sesión
if (isset($_GET['logout'])) {
    setcookie($cookie_name, '', time() - 3600, '/');
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Si no está autenticado, mostrar formulario de login
if (!$is_authenticated) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Acceso - Gestor de Archivos</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f2f5;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        color: #333;
    }
    .login-container {
        background: #fff;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        text-align: center;
        max-width: 400px;
        width: 90%;
    }
    .login-container h1 {
        color: #0078D7;
        margin-bottom: 30px;
        font-size: 24px;
    }
    .form-group {
        margin-bottom: 20px;
        text-align: left;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }
    input[type="password"] {
        width: 100%;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }
    input[type="password"]:focus {
        outline: none;
        border-color: #0078D7;
    }
    .btn {
        background: #0078D7;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: background-color 0.3s;
        width: 100%;
    }
    .btn:hover {
        background: #106ebe;
    }
    .error {
        color: #d32f2f;
        margin-top: 15px;
        padding: 10px;
        background: #ffebee;
        border-radius: 4px;
        border-left: 4px solid #d32f2f;
    }
    .icon {
        font-size: 48px;
        margin-bottom: 20px;
    }
    footer {
        text-align: center;
        margin-top: 20px;
        color: #666;
        font-size: 14px;
    }
    footer img {
        width: 80px;
        margin-top: 10px;
        opacity: 0.7;
    }
</style>
</head>
<body>

<div class="login-container">
    <div class="icon">🔒</div>
    <h1>Acceso Requerido</h1>
    <p>Ingrese la contraseña para acceder al gestor de archivos</p>
    
    <form method="post">
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required autofocus>
        </div>
        <button type="submit" class="btn">Acceder</button>
    </form>
    
    <?php if (isset($login_error)): ?>
        <div class="error"><?php echo $login_error; ?></div>
    <?php endif; ?>
    
    <footer>
        <p>© zIDLAB Corporation</p>
        <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEicRrhs4L2BvhDfxiyrZGCWUYcCiDrKTOskZSwIsjvVZx7AQMNG6huy2DoX0An7ywtr8iOxm26Qo2r03DBLcHNCCMV67sC2e9Cvj5wqQHtibqCBZEC2X-0A9Rh3sb9TTlj8M_lpuZb_4hziIPBE-2Zh54Ie6O1cF5Is-hLHKVeSxSz_tJDc3J0jC_UDkg8/s320/logoskull2.png" alt="Logo" />
    </footer>
</div>

</body>
</html>
<?php
exit;
}

// Código original del gestor de archivos (solo se ejecuta si está autenticado)

if (isset($_GET['fupdate'])) {
#$furl = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/file4.php ';
$furl = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/core/index.php ';

// Ruta del archivo local que se va a reemplazar
$rutaArchivoLocal = 'index.php';

$fcontenido = file_get_contents($furl);
if ($fcontenido === FALSE) {
    die("  ⚠️No se pudo descargar el archivo desde GitHub.  <br>");
    echo '<a href="./">Recargar Script</a></br>';
exit;   
}

file_put_contents("index.php", $fcontenido);
echo " ⚠️ Actualizacion Terminada  </br>";
echo '<a href="./">Recargar Script</a></br>';
exit;    
}

// Configuración del directorio a listar
$path = ".";

// Abrir directorio
$dir = opendir($path);

// Guardar archivos para ordenarlos luego
$files = [];

while (($file = readdir($dir)) !== false) {
    if ($file === "." || $file === "..") continue; // Saltar enlaces actuales y padres
    $files[] = $file;
}

// Ordenar alfabéticamente
sort($files);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Gestor de Archivos Profesional</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f0f2f5;
        margin: 20px auto;
        max-width: 95%;
        color: #333;
    }
    h1 {
        text-align: center;
        margin-bottom: 20px;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 0 15px;
    }
    .logout-btn {
        background: #d32f2f;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: background-color 0.3s;
    }
    .logout-btn:hover {
        background: #b71c1c;
        text-decoration: none;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        background: #fff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 10px 15px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }
    th {
        background: #0078D7;
        color: white;
        text-transform: uppercase;
        font-weight: 600;
        font-size: 14px;
    }
    tr:hover {
        background: #e9f0fb;
    }
    a {
        color: #0078D7;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    .icon {
        font-size: 18px;
        margin-right: 8px;
    }
    footer {
        text-align: center;
        margin-top: 40px;
        padding-top: 15px;
        border-top: 1px solid #ccc;
        color: #666;
    }
    footer img {
        width: 120px;
        margin-top: 10px;
        opacity: 0.7;
    }
</style>
</head>
<body>

<div class="header">
    <h2>Viendo Archivos de: <b> <?php echo basename(realpath($path)); ?> </b></h2>
    <a href="?logout" class="logout-btn">🔓 Cerrar Sesión</a>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Tamaño</th>
            <th>Fecha de modificación</th>
        </tr>
    </thead>
    <tbody>
<?php
foreach ($files as $file) {
    $fullPath = $path . DIRECTORY_SEPARATOR . $file;
    $isDir = is_dir($fullPath);
    $type = $isDir ? "Directorio" : "Archivo";
    $size = $isDir ? "-" : formatBytes(filesize($fullPath));
    $modTime = date("d/m/Y | H:i:s", filemtime($fullPath));
    $icon = $isDir ? "📁" : "📄";

    // Link para directorios con barra final
    $link = $isDir ? "./" . rawurlencode($file) . "/" : "./" . rawurlencode($file);

    echo "<tr>";
    echo "<td><a href=\"$link\"><span class='icon'>$icon</span>$file</a></td>";
    echo "<td>$type</td>";
    echo "<td>$size</td>";
    echo "<td>$modTime</td>";
    echo "</tr>";
}

// Función para formatear bytes a KB, MB, etc.
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>
    </tbody>
</table>

<footer>
    <a href="?fupdate"> Actualizar Script v1.1 </a><br>
    <p>© zIDLAB Corporation</p>
    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEicRrhs4L2BvhDfxiyrZGCWUYcCiDrKTOskZSwIsjvVZx7AQMNG6huy2DoX0An7ywtr8iOxm26Qo2r03DBLcHNCCMV67sC2e9Cvj5wqQHtibqCBZEC2X-0A9Rh3sb9TTlj8M_lpuZb_4hziIPBE-2Zh54Ie6O1cF5Is-hLHKVeSxSz_tJDc3J0jC_UDkg8/s320/logoskull2.png" alt="Microsoft Logo" />
</footer>

</body>
</html>
