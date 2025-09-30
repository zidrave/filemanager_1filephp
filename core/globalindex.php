<?php
// Este archivo no se actualiza pero tiene la propiedad de leerse entre carpetas si lo declaraste como : FallbackResource /globalindex.php
// 🔹 Forzar que el working directory sea el DocumentRoot real del subdominio
if (isset($_SERVER['DOCUMENT_ROOT'])) {
    chdir($_SERVER['DOCUMENT_ROOT']);
}

// ==============================
// 🔹 CONFIGURACIÓN
// ==============================
$password = "1234";
$cookie_name = "file_manager_auth";
$cookie_duration = 3600; // 1 hora

// ==============================
// 🔹 LOGIN CON COOKIE
// ==============================
if (isset($_POST['password'])) {
    if ($_POST['password'] === $password) {
        setcookie($cookie_name, hash('sha256', $password), time() + $cookie_duration, '/');
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        $login_error = "Contraseña incorrecta";
    }
}

$is_authenticated = isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] === hash('sha256', $password);

// 🔹 Cerrar sesión
if (isset($_GET['logout'])) {
    setcookie($cookie_name, '', time() - 3600, '/');
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host   = $_SERVER['HTTP_HOST'];
    header("Location: $scheme://$host/");
    exit;
}

// ==============================
// 🔹 FORMULARIO LOGIN
// ==============================
if (!$is_authenticated):
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Acceso - Gestor de Archivos</title>
<style>
    body { font-family: Arial, sans-serif; background: #f0f2f5; display:flex; justify-content:center; align-items:center; min-height:100vh; }
    .login-container { background:#fff; padding:40px; border-radius:8px; box-shadow:0 0 20px rgba(0,0,0,0.1); text-align:center; width:350px; }
    .btn { background:#0078D7; color:#fff; padding:10px; border:none; border-radius:4px; cursor:pointer; width:100%; font-weight:bold; }
    .btn:hover { background:#106ebe; }
    .error { color:#d32f2f; margin-top:15px; }
</style>
</head>
<body>
<div class="login-container">
    <h1>🔒 Acceso Requerido</h1>
    <form method="post">
        <input type="password" name="password" placeholder="Contraseña" required autofocus style="width:100%;padding:10px;margin-bottom:10px;">
        <button type="submit" class="btn">Entrar</button>
    </form>
    <?php if (isset($login_error)) echo "<div class='error'>$login_error</div>"; ?>
</div>
</body>
</html>
<?php
exit;
endif;

// ==============================
// 🔹 DETECTAR DIRECTORIO DESDE LA URL
// ==============================
$baseDir = realpath($_SERVER['DOCUMENT_ROOT']);

// Ruta pedida (ej: /xxx/pedos/)
$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestedPath = urldecode($requestedPath);

// Normalizar (si está vacío → raíz)
$requestedPath = trim($requestedPath, "/");
if ($requestedPath === "") {
    $requestedPath = ".";
}

$targetDir = realpath($baseDir . "/" . $requestedPath);

if ($targetDir === false || strpos($targetDir, $baseDir) !== 0) {
    die("🚫 Acceso denegado");
}

// Listar archivos
$files = scandir($targetDir);
sort($files);

// ==============================
// 🔹 HTML LISTADO
// ==============================
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Gestor de Archivos</title>
<style>
    body { font-family: Arial, sans-serif; background:#f0f2f5; margin:20px auto; max-width:95%; color:#333; }
    h1 { text-align: center; margin-bottom: 20px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 15px; }
    .logout-btn { background:#d32f2f; color:white; padding:8px 16px; border:none; border-radius:4px; cursor:pointer; text-decoration:none; font-size:14px; font-weight:600; transition: background-color 0.3s; }
    .logout-btn:hover { background:#b71c1c; text-decoration:none; }
    table { border-collapse: collapse; width: 100%; background:#fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    th, td { padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left; }
    th { background:#0078D7; color:white; text-transform: uppercase; font-weight:600; font-size:14px; }
    tr:hover { background:#e9f0fb; }
    a { color:#0078D7; text-decoration:none; }
    a:hover { text-decoration:underline; }
    .icon { font-size:18px; margin-right:8px; }
    footer { text-align:center; margin-top:40px; padding-top:15px; border-top:1px solid #ccc; color:#666; }
    footer img { width:120px; margin-top:10px; opacity:0.7; }
</style>
</head>
<body>

<div class="header">
    <h2>📂 Explorando: <b><?php echo str_replace($baseDir, "", $targetDir) ?: "/"; ?></b></h2>
    <a href="?logout" class="logout-btn">Cerrar Sesión</a>
</div>

<table>
<thead>
<tr><th>Nombre</th><th>Tipo</th><th>Tamaño</th><th>Modificado</th></tr>
</thead>
<tbody>
<?php
// Botón para subir al padre
if ($requestedPath !== "." && $requestedPath !== "") {
    $parent = dirname($requestedPath);
    $parent = $parent === "." ? "/" : "/" . $parent . "/";
    echo "<tr><td colspan='4'><a href='" . $parent . "'>⬅️ Subir</a></td></tr>";
}

// Archivos y carpetas
foreach ($files as $file) {
    if ($file === "." || $file === "..") continue;
    $fullPath = $targetDir . "/" . $file;
    $isDir = is_dir($fullPath);

    $type = $isDir ? "Directorio" : "Archivo";
    $size = $isDir ? "-" : formatBytes(filesize($fullPath));
    $modTime = date("d/m/Y H:i:s", filemtime($fullPath));
    $icon = $isDir ? "📁" : "📄";

    $link = "/" . ltrim(($requestedPath === "." ? "" : $requestedPath . "/") . $file, "./");
    if ($isDir) $link .= "/";

    echo "<tr>";
    echo "<td><a href='$link'><span>$icon</span> $file</a></td>";
    echo "<td>$type</td>";
    echo "<td>$size</td>";
    echo "<td>$modTime</td>";
    echo "</tr>";
}
?>
</tbody>
</table>

<footer>
    <p>© zIDLAB Corporation</p>
    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEicRrhs4L2BvhDfxiyrZGCWUYcCiDrKTOskZSwIsjvVZx7AQMNG6huy2DoX0An7ywtr8iOxm26Qo2r03DBLcHNCCMV67sC2e9Cvj5wqQHtibqCBZEC2X-0A9Rh3sb9TTlj8M_lpuZb_4hziIPBE-2Zh54Ie6O1cF5Is-hLHKVeSxSz_tJDc3J0jC_UDkg8/s320/logoskull2.png" alt="Microsoft Logo" />
</footer>

</body>
</html>

<?php
// ==============================
// 🔹 FUNCIONES AUXILIARES
// ==============================
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>
