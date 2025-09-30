<?php
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
    $uri    = strtok($_SERVER['REQUEST_URI'], '?'); // quita parámetros como ?logout
    header("Location: $scheme://$host$uri");
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
<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- 🔹 Adaptar a móvil -->


<style>
    body { font-family: Arial, sans-serif; background:#f0f2f5; margin:20px auto; max-width:95%; color:#333; }
    h1 { text-align: center; margin-bottom: 20px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 15px; flex-wrap: wrap; }
    .logout-btn { background:#d32f2f; color:white; padding:8px 16px; border:none; border-radius:4px; cursor:pointer; text-decoration:none; font-size:14px; font-weight:600; transition: background-color 0.3s; }
    .logout-btn:hover { background:#b71c1c; text-decoration:none; }



    table { border-collapse: collapse; width: 100%; background:#fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    th, td { padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: left; font-size:14px; }
    th { background:#0078D7; color:white; text-transform: uppercase; font-weight:600; font-size:14px; }
    tr:hover { background:#e9f0fb; }
    a { color:#0078D7; text-decoration:none; }
    a:hover { text-decoration:underline; }
    .icon { font-size:18px; margin-right:8px; }
    footer { text-align:center; margin-top:40px; padding-top:15px; border-top:1px solid #ccc; color:#666; }
    footer img { width:120px; margin-top:10px; opacity:0.7; }

 /* Vista de tabla normal */
table {
  width: 100%;
  table-layout: fixed; /* mantiene proporción de anchos */
  border-collapse: collapse;
}

/* Distribución de columnas */
thead th:nth-child(1),
tbody td:nth-child(1) {
  width: 60%;   /* Nombre más grande */
}

thead th:nth-child(2),
tbody td:nth-child(2) {
  width: 15%;   /* Tipo */
}

thead th:nth-child(3),
tbody td:nth-child(3) {
  width: 10%;   /* Tamaño */
}

thead th:nth-child(4),
tbody td:nth-child(4) {
  width: 15%;   /* Modificado */
}



td:first-child a {
  display: inline-block;
  max-width: 100%;
  white-space: normal;     /* ✅ permite saltos de línea */
  word-wrap: break-word;   /* ✅ corta palabras largas */
  overflow-wrap: anywhere; /* ✅ corta en cualquier parte si es muy largo */
  vertical-align: top;
}

/* --- Vista móvil --- */
@media (max-width: 768px) {
  thead th:nth-child(4),
  tbody td:nth-child(4) {
    display: none; /* se elimina la columna completa */
  }

  /* Repartimos ancho entre las 3 columnas restantes */
  thead th, tbody td {
    width: auto; 
  }

  thead th:nth-child(1),
  tbody td:nth-child(1) { width: 60%; }  /* Nombre */
  thead th:nth-child(2),
  tbody td:nth-child(2) { width: 22%; }  /* Tipo */
  thead th:nth-child(3),
  tbody td:nth-child(3) { width: 17%; }  /* Tamaño */

  thead th, tbody td {
    text-align: left; /* todo alineado a la izquierda */
  }

}
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
echo "<td data-label='Nombre'><a href='$link'><span>$icon</span> $file</a></td>";
echo "<td data-label='Tipo'>$type</td>";
echo "<td data-label='Tamaño'>$size</td>";
echo "<td data-label='Modificado'>$modTime</td>";
    echo "</tr>";
}
?>
</tbody>
</table>
 
<footer>



  <p style="font-size:13px; color:#555; text-align:left;">
<?php
echo count(array_diff(scandir($targetDir), ['.', '..'])) . " Elementos en esta carpeta <br>";
//date_default_timezone_set("America/Lima"); // Ajusta tu zona horaria
echo "Fecha y hora del sistema: 🕒  " . date("d/m/Y H:i:s");
echo "<br>";
?>
        Servidor Web: <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br>
        Espacio usado: 
        <?php 
            $total = disk_total_space("/");
            $free  = disk_free_space("/");
            $used  = $total - $free;
            echo round($used / 1024 / 1024 / 1024, 2) . " GB";
        ?><br>
        Espacio disponible: 
        <?php 
            echo round($free / 1024 / 1024 / 1024, 2) . " GB";
        ?><br>
        Uptime: 
        <?php 
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                echo "No disponible en Windows";
            } else {
                $uptime = @file_get_contents("/proc/uptime");
                if ($uptime !== false) {
                    $uptime = explode(" ", $uptime)[0];
                    $days = floor($uptime / 86400);
                    echo $days . " días";
                } else {
                    echo "No disponible";
                }
            }
        ?>
    </p>




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
