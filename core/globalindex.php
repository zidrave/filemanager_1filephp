<?php
// ==============================
// CONFIGURACIÓN
// ==============================
$password = "1234";
$cookie_name = "file_manager_auth";
$cookie_duration = 3600; // 1 hora

// ==============================
// LOGIN CON COOKIE
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

if (isset($_GET['logout'])) {
    setcookie($cookie_name, '', time() - 3600, '/');
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host   = $_SERVER['HTTP_HOST'];
    $uri    = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $scheme://$host$uri");
    exit;
}

// ==============================
// FORMULARIO LOGIN
// ==============================
if (!$is_authenticated):
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Acceso - Gestor de Archivos</title>
<style>
    body { font-family: Arial; background:#f0f2f5; display:flex; justify-content:center; align-items:center; min-height:100vh; }
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
// DETECTAR DIRECTORIO
// ==============================
$baseDir = realpath($_SERVER['DOCUMENT_ROOT']);
$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestedPath = urldecode($requestedPath);
$requestedPath = trim($requestedPath, "/");
if ($requestedPath === "") $requestedPath = ".";
$targetDir = realpath($baseDir . "/" . $requestedPath);

if ($targetDir === false || strpos($targetDir, $baseDir) !== 0) die("🚫 Acceso denegado");

// Listar archivos
$files = scandir($targetDir);
sort($files);

// Contar elementos
$file_count = 0;
foreach ($files as $f) if ($f !== "." && $f !== "..") $file_count++;

// ==============================
// HTML PRINCIPAL
// ==============================
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Explorador: /<?php echo str_replace($baseDir,"",$targetDir); ?></title>
<style>
/* Copia exacta de los estilos de tu plantilla */
* {margin:0;padding:0;box-sizing:border-box;}
body {font-family:Arial, Helvetica, sans-serif; background:#e8eef7; color:#333; min-height:100vh;}
header {background:linear-gradient(180deg, #3d6fa8 0%, #2d5a8f 100%); padding:15px 30px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 5px rgba(0,0,0,0.2);}
.title {display:flex; align-items:center; gap:12px; font-size:22px; font-weight:bold; color:#fff; text-shadow:1px 1px 2px rgba(0,0,0,0.3);}
.folder-icon {font-size:28px;}
.logout-btn {background:linear-gradient(180deg, #ff6b35 0%, #e55a2b 100%); color:#fff; border:1px solid #d54a1f; padding:10px 20px; font-family:Arial,sans-serif; font-weight:bold; font-size:13px; cursor:pointer; transition: all 0.2s; text-transform:uppercase; border-radius:3px; box-shadow:0 2px 4px rgba(0,0,0,0.2);}
.logout-btn:hover {background:linear-gradient(180deg, #ff7d4d 0%, #f16637 100%); box-shadow:0 3px 6px rgba(0,0,0,0.3); transform:translateY(-1px);}
.logout-btn:active {transform:translateY(0); box-shadow:0 1px 3px rgba(0,0,0,0.2);}
.breadcrumb {background:#fff; padding:12px 30px; border-bottom:1px solid #d5dde5; font-size:13px; color:#666;}
.breadcrumb a {color:#3d6fa8; text-decoration:none;}
.breadcrumb a:hover {text-decoration:underline;}
.main-content {max-width:1200px; margin:20px auto; padding:0 20px;}
.content-box {background:#fff; border:1px solid #d5dde5; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);}
.box-header {background:linear-gradient(180deg, #f5f7fa 0%, #e8eef7 100%); padding:12px 20px; border-bottom:1px solid #d5dde5; font-weight:bold; color:#2d5a8f; border-radius:5px 5px 0 0;}
table {width:100%; border-collapse:collapse;}
thead {background:#f5f7fa; border-bottom:2px solid #d5dde5;}
th {padding:12px 20px; text-align:left; font-weight:bold; color:#2d5a8f; font-size:13px; text-transform:uppercase;}
tbody tr {border-bottom:1px solid #e8eef7; transition:background 0.2s;}
tbody tr:hover {background:#f5f7fa;}
tbody tr:last-child {border-bottom:none;}
td {padding:12px 20px; color:#333; font-size:14px;}
.file-link {color:#3d6fa8; text-decoration:none; display:flex; align-items:center; gap:8px; font-weight:500;}
.file-link:hover {color:#2d5a8f; text-decoration:underline;}
.file-icon {font-size:18px;}
.info-box {background:#fffbea; border:1px solid #f5e6a8; border-left:4px solid #ff6b35; padding:15px 20px; margin-bottom:20px; border-radius:3px;}
.info-box p {margin:8px 0; color:#666; font-size:13px; line-height:1.6;}
.info-box strong {color:#2d5a8f; font-weight:bold;}
footer {background:#2d5a8f; color:#fff; text-align:center; padding:30px; margin-top:40px; border-top:3px solid #3d6fa8;}
   
    footer img { width:120px; margin-top:10px; opacity:0.7; }
.logo {width:100px; height:100px; margin:15px auto; opacity:0.9;}
.copyright {font-size:13px; margin-bottom:15px; opacity:0.9;}
.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px; margin:15px 0;}
.stat-item {background:#f5f7fa; padding:12px; border-radius:3px; border-left:3px solid #3d6fa8;}
.stat-label {font-size:12px; color:#666; text-transform:uppercase; margin-bottom:5px;}
.stat-value {font-size:16px; color:#2d5a8f; font-weight:bold;}
@media (max-width:768px){th:nth-child(4),td:nth-child(4){display:none;}}
</style>
</head>
<body>
<header>
    <div class="title"><span class="folder-icon">📁</span> Explorador de Archivos</div>
    <a href="?logout" class="logout-btn">Cerrar Sesión</a>
</header>

<div class="breadcrumb">
    <strong>Ruta actual:</strong> <?php echo str_replace($baseDir,"",$targetDir) ?: "/"; ?>
</div>

<div class="main-content">
<div class="info-box">
    <p><strong>📂 Directorio actual:</strong> <?php echo str_replace($baseDir,"",$targetDir) ?: "/"; ?></p>
    <p><strong>📊 Elementos encontrados:</strong> <?php echo $file_count; ?> archivos en esta carpeta</p>
</div>

<div class="content-box">
    <div class="box-header">📄 Listado de Archivos</div>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Tamaño</th>
                <th>Modificado</th>
            </tr>
        </thead>
        <tbody>
<?php
// Subir al padre
if ($requestedPath !== "." && $requestedPath !== "") {
    $parent = dirname($requestedPath);
    $parent = $parent === "." ? "/" : "/" . $parent . "/";
    echo "<tr><td colspan='4'><a href='$parent' class='file-link'><span class='file-icon'>↩️</span> Subir al directorio anterior</a></td></tr>";
}

// Archivos
foreach ($files as $file) {
    if ($file === "." || $file === "..") continue;
    $fullPath = $targetDir."/".$file;
    $isDir = is_dir($fullPath);
    $type = $isDir ? "Directorio" : "Archivo";
    $size = $isDir ? "-" : formatBytes(filesize($fullPath));
    $modTime = date("d/m/Y H:i:s", filemtime($fullPath));
    $icon = $isDir ? "📁" : "📄";
    $link = "/".ltrim(($requestedPath==="."?"":$requestedPath."/").$file,"./");
    if($isDir) $link.="/";
    echo "<tr>";
    echo "<td><a href='$link' class='file-link'><span class='file-icon'>$icon</span> $file</a></td>";
    echo "<td>$type</td>";
    echo "<td>$size</td>";
    echo "<td>$modTime</td>";
    echo "</tr>";
}
?>
        </tbody>
    </table>
</div>

<div class="content-box">
    <div class="box-header">📊 Información del Sistema</div>
    <div style="padding:20px;">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-label">Fecha y Hora</div>
                <div class="stat-value"><?php echo date("d/m/Y H:i:s"); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Servidor Web</div>
                <div class="stat-value"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Espacio Usado</div>
                <div class="stat-value">
                <?php $total=disk_total_space("/");$free=disk_free_space("/");$used=$total-$free;echo round($used/1024/1024/1024,2)." GB"; ?>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Espacio Disponible</div>
                <div class="stat-value"><?php echo round($free/1024/1024/1024,2)." GB"; ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Sistema Operativo</div>
                <div class="stat-value"><?php echo PHP_OS; ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Tiempo Activo</div>
                <div class="stat-value">
                <?php 
                if(strtoupper(substr(PHP_OS,0,3))==='WIN'){echo "No disponible";}
                else{
                    $uptime=@file_get_contents("/proc/uptime");
                    if($uptime!==false){$uptime=explode(" ",$uptime)[0];$days=floor($uptime/86400);echo $days." días";}
                    else echo "No disponible";
                }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<footer>
    <p class="copyright">© <?php echo date("Y"); ?> zIDLAB Corporation - Todos los derechos reservados</p>


    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEicRrhs4L2BvhDfxiyrZGCWUYcCiDrKTOskZSwIsjvVZx7AQMNG6huy2DoX0An7ywtr8iOxm26Qo2r03DBLcHNCCMV67sC2e9Cvj5wqQHtibqCBZEC2X-0A9Rh3sb9TTlj8M_lpuZb_4hziIPBE-2Zh54Ie6O1cF5Is-hLHKVeSxSz_tJDc3J0jC_UDkg8/s320/logoskull2.png" alt="Microsoft Logo" />


    <p style="font-size:12px; opacity:0.8;">Explorador de Carpetas</p>
</footer>

<?php
function formatBytes($bytes,$precision=2){$units=['B','KB','MB','GB','TB'];$bytes=max($bytes,0);$pow=floor(($bytes?log($bytes):0)/log(1024));$pow=min($pow,count($units)-1);$bytes/= (1<< (10*$pow));return round($bytes,$precision).' '.$units[$pow];}
?>
</body>
</html>
