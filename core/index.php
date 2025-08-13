<?php

if (isset($_GET['fupdate'])) {
#$furl = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/file4.php';
$furl = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/core/index.php';

// Ruta del archivo local que se va a reemplazar
$rutaArchivoLocal = 'index.php';

$fcontenido = file_get_contents($furl);
if ($fcontenido === FALSE) {
    die("  ⚠️No se pudo descargar el archivo desde GitHub.  <br>");
}

file_put_contents("index.php", $fcontenido);
echo " ⚠️ Actualizacion Terminada  </br>";
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

<h2>Viendo Archivos de: <b> <?php echo basename(realpath($path)); ?> </b></h2>
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
    <p>© zIDLAB Corporation</p>
    <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg" alt="Microsoft Logo" />
</footer>

</body>
</html>
