<?php
#edicion privada del script mas puton y util para mi 
#version 1.0beta index III
$uploadDir = 'uploads/';
$activeDir = 'uploads';



#    $carpetap = $_POST['c'];

if (isset($_GET['c'])) {
    $carpetax = $_GET['c'];
    $carpetap = $_GET['c'];
    $carpetaz = $_GET['c'];
    // Sanitización básica (considera usar funciones más robustas)
    $carpetax = filter_var($carpetax, FILTER_SANITIZE_STRING);

    // Crear la ruta completa a la carpeta
#   $uploadDir = 'uploads/' . $carpetax;
    $uploadDir = "uploads$carpetax/";
    $activeDir = "$carpetax";

    // Verificar si la carpeta existe, crearla si es necesario
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Crear directorio recursivamente
    }

    // Resto de tu lógica para subir archivos o realizar otras operaciones
} else {
    // Manejar el caso en que no se proporciona el parámetro 'c'
 #   echo " ⚠️ FALTA el parámetro 'c'";
       }



if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755);
}

// Subir archivo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    $targetFile = $uploadDir . basename($_FILES['fileToUpload']['name']);
    $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);

    if ($fileType != 'php' || (isset($_POST['allowPhpUpload']) && $_POST['allowPhpUpload'] == 'yes')) {
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
            echo "El archivo ". htmlspecialchars(basename($_FILES['fileToUpload']['name'])). " ha sido subido.";
        } else {
            echo " ⚠️Error al subir el archivo.";
        }
    } else {
        echo "⚠️ No se permiten archivos PHP.";
    }
}

// Eliminar archivo
if (isset($_GET['deleteFile'])) {
#    $fileToDelete = $uploadDir . $_GET['deleteFile'];
    $fileToDelete = $_GET['deleteFile'];
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        echo "<h1> ⚠️Archivo eliminado..</h1>";
    } else {
        echo "<h1> ⚠️Archivo no encontrado.</h1>";
    }
}

// Crear carpeta
if (isset($_POST['createFolder'])) {
    $newFolder = $uploadDir . $_POST['createFolder'];
    if (!is_dir($newFolder)) {
        mkdir($newFolder, 0755);
        echo "<h1> ⚠️Carpeta creada. </h1>";
    } else {
        echo "<h1> ⚠️ La carpeta ya existe.</h1>";
    }
}

// Eliminar carpeta
#$elfolder=$_GET['deleteFolder'];
#$elfolder=$_POST['deleteFolder'];

if (isset($_POST['deleteFolder'])) {
    $elfolder=$_POST['deleteFolder'];
#    $folderToDelete = $uploadDir . $_POST['deleteFolder'];
    $folderToDelete = $elfolder;
    if (is_dir($folderToDelete)) {
        rmdir($folderToDelete);
        echo "<h1> ⚠️Carpeta eliminada solo si estaba vacia. </h1>";
    } else {
        echo "<h1> ⚠️ Carpeta no encontrada.</h1>";
    }
}

if (isset($_GET['deleteFolder'])) {
    $elfolder=$_GET['deleteFolder'];
#    $folderToDelete = $uploadDir . $_GET['deleteFolder'];
    $folderToDelete = $elfolder;
    if (is_dir($folderToDelete)) {
        rmdir($folderToDelete);
        echo "<h1> ⚠️Carpeta eliminada solo si estaba vacia. </h1>";
    } else {
        echo "<h1> ⚠️ Carpeta no encontrada.</h1>";
    }
}




// Editar o crear archivo
if (isset($_GET['editFile'])) {
#    $fileToEdit = $uploadDir . $_GET['editFile'];
    $fileToEdit = $_GET['editFile'];
    if (file_exists($fileToEdit)) {
        $fileContent = file_get_contents($fileToEdit);
    } else {
#        echo $fileToEdit ." no encontrado.";
        // Si el archivo no existe, crearlo con contenido vacío
        file_put_contents($fileToEdit, '');
        $fileContent = '';
    }
}

// Guardar archivo editado
if (isset($_POST['saveFile'])) {
#    $fileToSave = $uploadDir . $_POST['fileName'];
    $fileToSave = $_POST['fileName'];
    $c = $_POST['c'];
    $newContent = $_POST['fileContent'];
    file_put_contents($fileToSave, $newContent);
    echo "<h1>⚠️Archivo guardado.</h1> <br>";

    $elarchivo = $_GET['editFile'];
    echo "<a href='?editFile=$elarchivo&c=$c'>RECARGAR</a>";
    exit;
}

// Renombrar archivo
if (isset($_POST['renameFile'])) {
#    $oldName = $uploadDir . $_POST['oldName'];
    $oldName = $_POST['oldName'];
#    $newName = $uploadDir . $_POST['newName'];
    $newName = $_POST['newName'];
    if (file_exists($oldName)) {
        if (rename($oldName, $newName)) {
            echo "<h1>⚠️Archivo renombrado. </h1> </br> ";
    echo "<a href='?c=$carpetap'>RECARGAR</a>";
    exit;


        } else {
            echo "Error al renombrar el archivo.";
        }
    } else {
        echo "Archivo no encontrado.";
    }
}

// Listar archivos y carpetas
$items = scandir($uploadDir);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestor de Archivos</title>
    <style>
        .folder {
            font-weight: bold;
        }
        .file {
            font-style: normal;
        }
    </style>
</head>
<body>
    <h2> 🌀 Subir Archivo   -  <a href='index3.php'>Index3 Raiz: / </a></h2>
    <form action="" method="post" enctype="multipart/form-data">
        ✅ Selecciona el archivo:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <label>
            <input type="checkbox" name="allowPhpUpload" value="yes"> Permitir archivos PHP
        </label>
        <input type="submit" value="Subir Archivo" name="submit">
    </form>

    <?php if (isset($fileContent)): ?>
        <h2>Editando: <?php echo htmlspecialchars($_GET['editFile']); ?></h2>
        <form action="" method="post">
            <textarea name="fileContent" rows="30" cols="165"><?php echo htmlspecialchars($fileContent); ?></textarea><br>
            <input type="hiddenx" name="fileName" value="<?php echo htmlspecialchars($_GET['editFile']); ?>">
            <input type="hidden" name="c" value='<?php echo "$carpetaz";?>' >
            <input type="submit" name="saveFile" value="GUARDAR ARCHIVO">
        </form>
    <?php endif; ?>

 

<?php
$archivoacambiarnombre=$_GET['archivoacambiarnombre'];
?>


<?php if (isset($archivoacambiarnombre)): ?>
    <h2>Renombrar o Mover Archivo</h2>
    <form action="" method="post">
        Nombre actual del archivo:
        <input type="text" name="oldName" value="<?php echo "$archivoacambiarnombre";?>" required>
        Nuevo nombre del archivo:
        <input type="text" name="newName" value="<?php echo "$archivoacambiarnombre";?>" required>
        <input type="hidden" name="c" value="<?php echo "$carpetap";?>" >
        <input type="submit" value="Renombrar Archivo" name="renameFile">
    </form>
<?php endif; ?>

    <h2>Contenido de la Carpeta: <?php echo "$carpetaz"; ?></h2>
    <ul>
        <?php
echo "<li class='folder'><a href='index3.php'>📁 / </a></li>";
echo "<li class='folder'><a href='?c=$carpetaz'>📁 . </a></li>";
echo "<li class='folder'><a href='?c=$carpetaz/..'>📁 .. </a></li>";
        foreach ($items as $item) {

            if ($item != '.' && $item != '..') {



                if (is_dir($uploadDir . $item)) {
                    echo "<li class='folder'><a href='?c=$carpetaz/$item'>📁 $item  </a>   [<a href='?deleteFolder=$uploadDir$item&c=$carpetaz'>❌</a>] </li>";
                } else {
                 $fileSize = filesize($uploadDir . $item);
                    echo "<li class='file'><a href='$uploadDir$item'>📄 $item </a> (" . formatSize($fileSize) . ")  [<a href='?editFile=$uploadDir$item&c=$carpetaz'>✏️</a>] [<a href='?archivoacambiarnombre=$uploadDir$item&c=$carpetaz'>🖊️</a>] [<a href='?deleteFile=$uploadDir$item&c=$carpetaz'>❌</a>] </li>";
                }
            }
        }
        ?>
    </ul>




    <h2>Editar o crear Archivo</h2>
    <form action="" method="get">
        Nombre del archivo:
        <input type="text" name="editFile" value='<?php echo "uploads$carpetaz/";?>' required>
        <input type="hidden" name="c" value='<?php echo "$carpetaz";?>' >
        <input type="submit" value="Editar Archivo">
    </form>


   <h2>Crear Carpeta</h2>
    <form action="" method="post">
        Nombre de la carpeta:
        <input type="text" name="createFolder" required>
        <input type="submit" value="Crear Carpeta">
    </form>
<!--
    <h2>Eliminar Archivo</h2>
    <form action="" method="get">
        Nombre del archivo:
        <input type="text" name="deleteFile" required>
        <input type="submit" value="Eliminar Archivo">
    </form>
-->

    <h2>Eliminar Carpeta (solo si esta vacia)</h2>
    <form action="" method="get">
        Nombre de la carpeta:
        <input type="text" name="deleteFolder" value='<?php echo "uploads$carpetaz";?>/' required>
        <input type="submit" value="Eliminar Carpeta">
    </form>

 <hr>

<?php
function formatSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

// Obtener información del disco
$diskTotal = disk_total_space("/");
$diskFree = disk_free_space("/");
$diskUsed = $diskTotal - $diskFree;

// Obtener información de memoria
$memInfo = file_get_contents('/proc/meminfo');
preg_match('/MemTotal:\s+(\d+) kB/', $memInfo, $matches);
$memTotal = $matches[1] * 1024;
preg_match('/MemFree:\s+(\d+) kB/', $memInfo, $matches);
$memFree = $matches[1] * 1024;
$memUsed = $memTotal - $memFree;

// Obtener carga del procesador
// Obtener carga del procesador y calcular porcentaje estimado
$loadAvg = sys_getloadavg();
$cpuLoad = $loadAvg[0];
// Suponiendo un máximo de 1 núcleo, ajustar según sea necesario
$maxLoad = 4;
$cpuUsage = round(($cpuLoad / $maxLoad) * 100, 2) . '%';

// Obtener temperatura del núcleo (si disponible)
$coreTemp = 'N/A';
if (file_exists('/sys/class/thermal/thermal_zone0/temp')) {
    $coreTemp = round(file_get_contents('/sys/class/thermal/thermal_zone0/temp') / 1000, 1) . '°C';
}

// Obtener información del sistema operativo
$os = php_uname('s') . ' ' . php_uname('r');

// Mostrar información
echo "<h2>Información del Sistema</h2>\n";
echo "<ul>\n";
echo "<li>Espacio usado: " . formatSize($diskUsed) . "</li>\n";
echo "<li>Espacio disponible: " . formatSize($diskFree) . "</li>\n";
echo "<li>Memoria usada: <b> " . formatSize($memUsed) . " </b></li>\n";
echo "<li>Memoria total: <b>" . formatSize($memTotal) . " </b></li>\n";
#echo "<li>Uso del procesador: " . $cpuLoad . " (carga promedio)</li>\n";
echo "<li>Uso del procesador: <b> " . $cpuLoad . " (carga promedio) - " . $cpuUsage . " </b></li>\n";
echo "<li>Temperatura del núcleo 0: <b> " . $coreTemp . "  </b></li>\n";
echo "<li>Sistema operativo: " . $os . "</li>\n";
echo "</ul>\n";

?>

<hr> 
File manager public! Version publica 1.0b en <a href='https://zidrave.net/' target='_black'>http://zidrave.net</a>
<hr>
notas: el archivo ping.php hace ping a una ip de google y guarda datos en lageos.txt y lageos.php tambien usamnos plantillalageos.txt 
<br>
<a href='?editFile=index3.php'><b>😍 EDIT THIS SCRIPT 🛠️</b></a> - <a href='https://github.com/zidrave/filemanager_1filephp/' target='_black'><b>😍 Ver Proyecto GitHub 🛠️</b></a>
</body>
</html>
