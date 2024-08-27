<!--
           ,______________________________________       
        - |_________________,----------._ [____]  ""-,__  __....-----=====
                        (_(||||||||||||)___________/   ""                |
                           `----------' zIDRAvE[ ))"-,                   |
                     FILE MANAGER V4.1          ""    `,  _,--....___    |
                     https://github.com/zidrave/        `/           """"

-->
<?php
#formato de mensajes de alerta
$alertaini=" <div class='mensajex'> <h2>";
$alertafin="  </h2> </div> ";
$scriptfile="file4";
$scriptfm = $scriptfile;
$scriptfm = strtoupper($scriptfm); #pasar a mayuscula papi

?>




<!DOCTYPE html>
<html>
<head>
    <title>File Manager V4</title>
    <style>
        body {
		    background-color: #f0f0f0; /* Fondo gris claro */
            font-family: Arial, sans-serif; /* Tipo de letra Arial */
        }

        .tabla {
            display: table;
            width: 80%;
            border-collapse: collapse;
            background-color: white; /* Fondo blanco para la tabla */
        }

        .fila {
            display: table-row;
            border-bottom: 1px solid #ddd;
        }

        .celda {
            display: table-cell;
            padding: 3px;
            border: 1px solid #ddd; /* Agrega un borde a cada celda */
        }
        .celda2 {
            display: table-cell;
            width: 190px; /* Ancho fijo para la celda2 */
            padding: 3px;
            border: 1px solid #ddd; /* Agrega un borde a la celda2 */
        }
        .celda3 {
            display: table-cell;
            width: 110px; /* Ancho fijo para la celda2 */
            padding: 3px;
            border: 1px solid #ddd; /* Agrega un borde a la celda2 */
        }
        .celda4 {
            display: table-cell;
            width: 260px; /* Ancho fijo para la celda2 */
            padding: 3px;
            border: 1px solid #ddd; /* Agrega un borde a la celda2 */
        }
        .fila:nth-child(even) {
            background-color: #f2f2f2; /* Color de fondo para filas pares */
        }
    /* Estilo para los botones de formulario */
    button, input[type="submit"] {
        background-color: #FFA500; /* Fondo naranja */
        border: 3px solid #000; /* Borde negro grueso */
        color: #fff; /* Texto blanco */
        padding: 4px 8px; /* Espaciado interno */
        font-size: 16px; /* Tamaño de fuente */
        font-weight: bold; /* Texto en negrita */
        cursor: pointer; /* Puntero en forma de mano al pasar sobre el botón */
        border-radius: 4px; /* Bordes redondeados (opcional) */
    }

    /* Estilo para el botón cuando el mouse está sobre él */
    button:hover, input[type="submit"]:hover {
        background-color: #e69500; /* Fondo naranja más oscuro al pasar el mouse */
    }

    header {
    background-color: #dedfdf; /* Gris oscuro */
    color: #000; /* Texto blanco */
    text-align: left; /* alineacion */
    width: 99%; /* Ocupa todo el ancho */
    padding: 10px; /* Añade un poco de espacio interno */
    }

    footer {
    background-color: #dedfdf; /* Gris  */
    color: #000; /* Texto blanco */
    text-align: left; /* alineacion */
    width: 99%; /* Ocupa todo el ancho */
    padding: 10px; /* Añade un poco de espacio interno */
    }

    .mensajex {
    background-color: #2c4c5e; /* Gris  */
    color: #ffffff; /* Texto blanco */
    text-align: left; /* alineacion */
    width: 99%; /* Ocupa todo el ancho */
    padding: 10px; /* Añade un poco de espacio interno */
    border: 1px solid white; 
    }

   .naranja {
      background-color: #FFA500; /* Color naranja */
      color: #fff; /* Texto blanco */
      padding: 10px 20px; /* Espacio interno */
      text-decoration: none; /* Quita el subrayado */
      border-radius: 5px; /* Bordes redondeados */
      display: inline-block; /* Muestra el elemento como un bloque en línea */
    }

   .azulin {
      background-color: #2c4c5e; /* Color naranja */
      color: #fff; /* Texto blanco */
      padding: 10px 20px; /* Espacio interno */
      text-decoration: none; /* Quita el subrayado */
      border-radius: 5px; /* Bordes redondeados */
      display: inline-block; /* Muestra el elemento como un bloque en línea */
    }



.enlacez {
  background-color: #2c4c5e;  
  color: white;
  padding: 5px 10px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  border-radius: 5%;
  transition-duration: 0.4s;   

}

.enlacez:hover {
  background-color: #000000; 
  box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
}



    .formtext {
     background-color: #c9d4da; /* Azul oscuro */
     color: #0c2b3d; /* Blanco */
     border: 2px dotted black; /* Borde punteado negro de 2px */
     padding: 5px; /* Espacio interno */
     }
    .formtext2 {
     background-color: #a5b9c2; /* Azul oscuro */
     color: #0c2b3d; /* Blanco */
     border: 2px dotted black; /* Borde punteado negro de 2px */
     padding: 5px; /* Espacio interno */
     }

   

        .mensaje {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f0f0f0;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .mensaje:target {
            display: block;
        }
        .cerrar {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .cerrar:hover {
            background-color: #0056b3;
        }
        .trigger {
            cursor: pointer;
            color: blue;
            text-decoration: underline;
        }

    </style>
</head>
<body>












<?php
#edicion privada del script mas puton y util para mi 
#version 1.0beta index III
$uploadDir = 'uploads/';
$activeDir = 'uploads';




// para la lista de carpetas con links
$getruta=$_GET['c'];
$rutax = "/$getruta";
$partes = explode('/', trim($rutax, '/'));
$acumulado = "/";
#foreach ($partes as $parte) {
#    if ($parte !== "") {
#        // Construir la ruta acumulativa
#        $acumulado .= $parte . '/';
#        
#        // Generar el enlace
##        echo ' <a href="$scriptfile.php?c=' . urlencode($acumulado) . '">' . htmlspecialchars($parte) . ' <b>/</b></a> ';
#        echo " <a href='$scriptfile.php?c=" . $acumulado . "'>" . $parte . " <b>/</b></a> ";
#        }
#    }






#    $carpetap = $_POST['c'];

if (isset($_GET['c'])) {





    $carpetax = $_GET['c'];
    $carpetap = $_GET['c'];
    $carpetaz = $_GET['c'];
    $carpetaz = rtrim($carpetaz, '/');
    // Sanitización básica (considera usar funciones más robustas)
    $carpetax = filter_var($carpetax, FILTER_SANITIZE_STRING);

    // Crear la ruta completa a la carpeta
#   $uploadDir = 'uploads/' . $carpetax;
    $uploadDir = "uploads$carpetax";
    $activeDir = "$carpetax";

    // Verificar si la carpeta existe, crearla si es necesario
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Crear directorio recursivamente
    }

    // Resto de tu lógica para subir archivos o realizar otras operaciones
} else {
    // Manejar el caso en que no se proporciona el parámetro 'c'
 #   echo " ⚠️ FALTA el parámetro 'c' ";
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
            echo " $alertaini ⚠️ El archivo  <span style='color:yellow;'>". htmlspecialchars(basename($_FILES['fileToUpload']['name'])). " </span> ha sido subido exitosamente. $alertafin ";
        } else {
            echo " $alertaini⚠️Error al subir el archivo. $alertafin";
        }
    } else {
        echo " $alertaini ⚠️ No se permiten archivos PHP. $alertafin";
    }
}

// Eliminar archivo
if (isset($_GET['deleteFile'])) {
$cadena = $_GET['deleteFile'];
$archivoname = basename($cadena);
#    $fileToDelete = $uploadDir . $_GET['deleteFile'];
    $fileToDelete = $_GET['deleteFile'];
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        echo "$alertaini ⚠️El archivo <span style='color:red;'> $archivoname </span> a sido eliminado... $alertafin";
    } else {
        echo "$alertaini ⚠️El archivo  <span style='color:red;'> $archivoname </span> no fue encontrado. $alertafin";
    }
}

// Crear carpeta
if (isset($_POST['createFolder'])) {
    $newFolder = $uploadDir . $_POST['createFolder'];
    if (!is_dir($newFolder)) {
        mkdir($newFolder, 0755);
        echo " $alertaini ⚠️ Carpeta creada. $alertafin ";
    } else {
        echo " $alertaini ⚠️ La carpeta no se creo, por que ya existe. $alertafin ";
    }
}

// Eliminar carpeta /////////////////////////////////////////////////////////////////////////////////////////////////////BORRAR FOLDER
#$elfolder=$_GET['deleteFolder'];
#$elfolder=$_POST['deleteFolder'];

if (isset($_POST['deleteFolder'])) {
    $elfolder=$_POST['deleteFolder'];
    $folderToDelete = "$uploadDir$elfolder"; //$uploadDir$item
#    $folderToDelete = "$elfolder";
    if (is_dir($folderToDelete)) {
        rmdir($folderToDelete);
        echo "$alertaini ⚠️Carpeta eliminada solo si estaba vacia. $alertafin";
    } else {
        echo "$alertaini ⚠️ Carpeta no encontrada. $alertafin";
    }
}

if (isset($_GET['deleteFolder'])) {
    $elfolder=$_GET['deleteFolder'];
    $folderToDelete = $uploadDir . $_GET['deleteFolder'];
#     $folderToDelete = $elfolder;
    if (is_dir($folderToDelete)) {
        rmdir($folderToDelete);
        echo "$alertaini ⚠️Carpeta eliminada solo si estaba vacia. $alertafin";
    } else {
        echo "$alertaini ⚠️ Carpeta no encontrada. $alertafin";
    }
}




// Editar o crear archivo
if (isset($_GET['editFile'])) {
#    $fileToEdit = $uploadDir . $_GET['editFile']; //uploads$carpetaz/
#    $fileToEdit = $_GET['editFile'];
    $fileToEdit = $_GET['editFile'];
    $fileToEdit = "uploads$carpetaz/$fileToEdit";
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
    $fileToSave = "uploads$carpetaz/$fileToSave";
    $c = $_POST['c'];
    $newContent = $_POST['fileContent'];
    file_put_contents($fileToSave, $newContent);
    echo "$alertaini  ⚠️ Archivo Guardado. $alertafin";

    $elarchivo = $_GET['editFile'];
    echo "<a href='?editFile=$elarchivo&c=$c/' class='naranja' role='button'> <b>RECARGAR </b></a>";
    exit;
}

// Renombrar archivo
if (isset($_POST['renameFile'])) {
    $oldName = $uploadDir . $_POST['oldName'];
#    $oldName = $_POST['oldName'];
    $newName = $uploadDir . $_POST['newName'];
#    $newName = $_POST['newName'];
    if (file_exists($oldName)) {
        if (rename($oldName, $newName)) {
            echo "$alertaini ⚠️ Archivo renombrado. $alertafin ";
    echo "<a href='?c=$carpetap' class='naranja' role='button'><b>RECARGAR </b></a>";
    exit;


        } else {
            echo " $alertaini ⚠️ Error al renombrar el archivo. $alertafin ";
        }
    } else {
        echo " $alertaini ⚠️ Archivo no encontrado. $alertafin  ";
    }
}








////////////////// Comprimir  archivo o carpeta 🚀 🚀🚀🚀🚀🚀🚀🚀
if (isset($_POST['compressFile'])) {

 $namefilec=$_POST['archivoacomprimir'];
 $namefilepass=$_POST['password'];
 $descripcion=$_POST['descripcion'];
#  echo " $alertaini 
# ⚠️ Archivo en compresion.<br>
# $alertafin  ";





 





$nombreZip = isset($_POST['archivoacomprimir']) ? $_POST['archivoacomprimir'] . '.zip' : 'archivo_protegido.zip';
$namefilepass = isset($_POST['password']) ? $_POST['password'] : '';
$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

// Instanciar ZipArchive
$zip = new ZipArchive();

if ($zip->open($nombreZip, ZipArchive::CREATE) === TRUE) {
    // Establecer la contraseña solo si no está vacía
    if (!empty($namefilepass)) {
        $zip->setPassword($namefilepass);
    }

    // Función para añadir archivos y carpetas recursivamente al ZIP
    function agregarAlZip($carpeta, $zip, $rutaDentroDelZip = '') {
        $archivos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($carpeta),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($archivos as $archivo) {
            if (!$archivo->isDir()) {
                // Obtener la ruta relativa dentro del ZIP
                $rutaRelativa = $rutaDentroDelZip . substr($archivo->getRealPath(), strlen($carpeta) + 1);
                // Añadir el archivo al ZIP
                $zip->addFile($archivo->getRealPath(), $rutaRelativa);
                // Aplicar cifrado si se estableció una contraseña
                if (!empty($zip->getPassword())) {
                    $zip->setEncryptionName($rutaRelativa, ZipArchive::EM_AES_256);
                }
            }
        }
    }

    // Verificar si se pasa un archivo o carpeta mediante POST
    if (isset($_POST['archivoacomprimir'])) {
        $ruta = $_POST['archivoacomprimir'];

        if (is_file($ruta)) {
            // Añadir un único archivo
            $zip->addFile($ruta, basename($ruta));
            if (!empty($namefilepass)) {
                $zip->setEncryptionName(basename($ruta), ZipArchive::EM_AES_256);
            }
        } elseif (is_dir($ruta)) {
            // Añadir una carpeta completa
            agregarAlZip($ruta, $zip, basename($ruta) . '/');
        } else {
            echo " $alertaini ⚠️ La ruta especificada no es válida. $alertafin ";
            exit;
        }
    } else {
        echo "No se especificó ningún archivo o carpeta.";
        exit;
    }

        // Agregar un comentario al archivo ZIP
        if (!empty($descripcion)) {
            $zip->setArchiveComment($descripcion);
        }

    // Cerrar el archivo ZIP
    $zip->close();
    echo " $alertaini ⚠️ El archivo $nombreZip se ha creado correctamente. $alertafin ";
} else {
    echo " $alertaini ⚠️ No se pudo crear el archivo ZIP. $alertafin ";
}







    echo "<a href='?c=$carpetap' class='naranja' role='button'><b>RECARGAR </b></a>";
    exit;
}
////////////////// Comprimir  archivo o carpeta 🚀 🚀🚀🚀🚀🚀🚀🚀


// Listar archivos y carpetas
$items = scandir($uploadDir);
?>





    <header>
        <h1> 🌀 File Manager   -  <?php echo "$scriptfm";?> <a href='<?php echo "$scriptfile";?>.php' class='enlacez' role='button'> Inicio:  </a> / 

<?php
foreach ($partes as $parte) {
    if ($parte !== "") {
        // Construir la ruta acumulativa
        $acumulado .= $parte . '/';
        #$acumulado = rtrim($acumulado, '/');
        
        // Generar el enlace

        echo " <a href='$scriptfile.php?c=" . $acumulado . "' class='enlacez' role='button'>" . $parte . " </a> <b>/</b> ";
        }
    }
?>

 <a href='?'>🏠</a>   <a href='?c=<?php echo "$carpetaz";?>/../'>↩️</a>   <a href='?creartexto=txt&c=<?php echo "$carpetaz";?>/'>📝</a> <a href='?crearelfolder=folder&c=<?php echo "$carpetaz";?>/'> 🗂️ </a>  <a href='?eliminarcarpeta=eliminar&c=<?php echo "$carpetaz";?>/'>❌</a></h1>
    </header>



    <form action="" method="post" enctype="multipart/form-data">
        ✅ <b>Subir Archivo :</b>
        <input type="file" name="fileToUpload" id="fileToUpload" class="formtext2">
        <label>
            <input type="checkbox" name="allowPhpUpload" value="yes"> Permitir archivos PHP
        </label>
        <input type="submit" value="Subir Archivo" name="submit">
    </form>

    <?php if (isset($fileContent)): ?>
        <h2>Editando: <?php echo htmlspecialchars($_GET['editFile']); ?></h2>
        <form action="" method="post">
            <textarea name="fileContent" rows="30" cols="165"  class="formtext" ><?php echo htmlspecialchars($fileContent); ?></textarea><br>
            <input type="hiddenx" name="fileName" value="<?php echo htmlspecialchars($_GET['editFile']); ?>" class="formtext">
            <input type="hidden" name="c" value='<?php echo "$carpetaz";?>' >
            <input type="submit" name="saveFile" value="GUARDAR ARCHIVO">
        </form>
    <?php endif; ?>

<?php
///////////////////////////////////////// CREAR FOLDER ////////////
$crearelfolder=$_GET['crearelfolder'];
?>

<?php if (isset($crearelfolder)): ?>

       <br>
	<div class="tabla">
		<div class="fila">
			<div class="celda"> 

   <h2> 🗂️ Crear Carpeta</h2>
    <form action="" method="post">
        Nombre de la carpeta:
        <input type="text" name="createFolder" required class="formtext">
        <input type="submit" value="Crear Carpeta">
    </form>


     <br>
			</div>
		</div>
	</div> <br>


<?php endif; ?>



 
<?php
///////////////////////////////////////// CREAR TEXTO ////////////
$creartexto=$_GET['creartexto'];
?>

<?php if (isset($creartexto)): ?>

       <br>
	<div class="tabla">
		<div class="fila">
			<div class="celda"> 

    <h2> 📝 Crear Archivo</h2>
    <form action="" method="get">
        Nombre del archivo:
<!--        <input type="text" name="editFile" value='<?php echo "uploads$carpetaz/";?>' required class="formtext"> -->
        <input type="text" name="editFile" value='' required class="formtext"> 
        <input type="hidden" name="c" value='<?php echo "$carpetaz";?>' >
        <input type="submit" value="Crear Archivo">
    </form>


     <br>
			</div>
		</div>
	</div> <br>


<?php endif; ?>






<?php
//////////////////////////////////// ELIMINAR CARPETA PREGUNTANDO  ////////////
$eliminarcarpeta=$_GET['eliminarcarpeta'];
?>

<?php if (isset($eliminarcarpeta)): ?>

       <br>
	<div class="tabla">
		<div class="fila">
			<div class="celda"> 
       <h2> ❌ Eliminar Carpeta (solo si esta vacia)</h2>
    <form action="" method="get">
        Nombre de la carpeta:
        <input type="text" name="deleteFolder" value='' required class="formtext">
        <input type="hidden" name="c" value="<?php echo "$carpetap";?>" >
        <input type="submit" value="Eliminar Carpeta">
    </form>
     <br>
			</div>
		</div>
	</div> <br>


<?php endif; ?>




<?php
//////////////////////////////////// cambiar nombre  ////////////
$archivoacambiarnombre=$_GET['archivoacambiarnombre'];
 if (isset($archivoacambiarnombre)): ?>

       <br>
	<div class="tabla">
		<div class="fila">
			<div class="celda"> 
    <h2>Renombrar o Mover Archivo</h2>
    <form action="" method="post">
        Nombre actual del archivo:
        <input type="text" name="oldName" value="<?php echo "$archivoacambiarnombre";?>" required class="formtext">
        Nuevo nombre del archivo:
        <input type="text" name="newName" value="<?php echo "$archivoacambiarnombre";?>" required class="formtext">
        <input type="hidden" name="c" value="<?php echo "$carpetap";?>" >
        <input type="submit" value="Renombrar Archivo" name="renameFile">
    </form><br>



			</div>
		</div>
	</div> <br>
<?php endif; ?>





<?php
$comprimir=$_GET['comprimir'];
?>

<?php if (isset($comprimir)): ?>

<!--💦💦💦💦💦💦💦-->



       <br>
	<div class="tabla">
		<div class="fila">
			<div class="celda"> 
    <h2> 🗃️ Comprimir ZIP (beta)</h2>
    <form action="" method="post">
        Nombre del archivo o carpeta:
        <input type="text" name="archivoacomprimir" value="<?php echo "$comprimir";?>" required class="formtext" readonly>
        contraseña:
        <input type="text" name="password" value=""  class="formtext">
        <input type="hidden" name="c" value="<?php echo "$carpetap";?>" >
        <input type="hidden" name="descripcion" value="
           ,______________________________________       
          |_________________,----------._ [____]  ''-,__  __....-----====
                        (_(||||||||||||)___________/   ''                |
                           `----------' zIDRAvE[ ))'-,                   |
                     FILE MANAGER V4.0          ''    `,  _,--....___    |
                     https://github.com/zidrave/        `/           ''''
...................................................................................
2024
" >
        <input type="submit" value="Comprimir" name="compressFile">
    </form><br>



			</div>
		</div>
	</div> <br>


<?php endif; ?>







        <br>
	<div class="tabla">
		<div class="fila">
			<div class="celda"> Contenido de la Carpeta: <b><?php echo "$carpetaz/"; ?></b>
			</div>
		</div>
	</div> <br>

  
   <div class="tabla">
  
    <div class="fila">
        <div class="celda4"><b>Nombre </b></div>
        <div class="celda3"><b>Tamaño</b></div>
        <div class="celda2"><b>Modificado</b></div>
        <div class="celda3"><b>Permisos</b></div>
        <div class="celda3"><b>Propietario</b></div>
		<div class="celda"> </div>
    </div>



        <?php
/////////// inicio del bucle
echo " 


 ";





        foreach ($items as $item) {

            if ($item != '.' && $item != '..') {

            $filePath = $uploadDir . $item;
            $filePerms = substr(sprintf('%o', fileperms($filePath)), -4);
            $fileOwner = posix_getpwuid(fileowner($filePath))['name'];
            $fileModTime = date("Y-m-d / H:i:s", filemtime($filePath));



                if (is_dir($uploadDir . $item)) {

echo " 
    <div class='fila'>
        <div class='celda'> 📂 <a href='?c=$carpetaz/$item/'><b>$item</b>  </a> </div>
        <div class='celda'>Carpeta</div>
        <div class='celda'>  $fileModTime </div>
        <div class='celda'>  $filePerms </div>
        <div class='celda'>  $fileOwner </div>
<!--	<div class='celda'>  [<a href='?archivoacambiarnombre=$uploadDir$item&c=$carpetaz/'>🖊️</a>] [<a href='?deleteFolder=$uploadDir$item&c=$carpetaz/'>❌</a>] --> 
	<div class='celda'>  [<a href='?archivoacambiarnombre=$item&c=$carpetaz/'>🖊️</a>] [<a href='?deleteFolder=$item&c=$carpetaz/'>❌</a>] 
<!-- [<a href='?comprimir=$uploadDir$item&c=$carpetaz/'>🗃️</a>] --> </div>
    </div>
 ";

                } else {
                 $fileSize = filesize($uploadDir . $item);



echo " 
    <div class='fila'>
        <div class='celda'> 📄 <a href='$uploadDir$item' target='_black'>$item </a> </div>
        <div class='celda'> " . formatSize($fileSize) . " </div>
        <div class='celda'>  $fileModTime </div>
        <div class='celda'>  $filePerms </div>
        <div class='celda'>  $fileOwner </div>
	<div class='celda'>  [<a href='?editFile=$item&c=$carpetaz/'>✏️</a>] [<a href='?archivoacambiarnombre=$item&c=$carpetaz/'>🖊️</a>] [<a href='#eliminar_$item'>❌</a>] [<a href='?comprimir=$uploadDir$item&c=$carpetaz/'>🗃️</a>] </div>
    </div>
 ";

//zona de confirmaciones para eliminacion 

 echo "
        <div id='eliminar_$item' class='mensaje'>
            <center>
                <p><b>¿Está seguro de eliminar?</b></p><br>
                <p><h2>$item</h2></p><br>
                <a class='cerrar' href='?deleteFile=$uploadDir$item&c=$carpetaz/'>Eliminar Ahora</a> 
                <a class='cerrar' href='#'>Cancelar</a>
            </center>
        </div>";
 echo "
        <div id='null' class='mensaje'>
        <!-- Esto solo es para que no se desordene los colores de las filas-->
        </div>";


                }
            }
        }
        


echo " <!--
    <div class='fila'>
        <div class='celda'> <a href='?'>📁 <b> / </b></a> </div>
        <div class='celda'>Carpeta</div>
        <div class='celda'>  Null </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  Sistema </div>
	<div class='celda'>   </div>
    </div>  -->
 ";

echo " <!--
    <div class='fila'>
        <div class='celda'> <a href='?c=$carpetaz/'>📁 <b> . </b></a> </div>
        <div class='celda'>Carpeta</div>
        <div class='celda'>  Null </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  Sistema </div>
	<div class='celda'>   </div>
    </div>
    -->
 ";

echo " 
    <div class='fila'>
        <div class='celda'> <a href='?c=$carpetaz/../'>📁 <b>.. </b></a> </div>
        <div class='celda'>Carpeta</div>
        <div class='celda'>  Null </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  Sistema </div>
	<div class='celda'>   </div>
    </div>
   
 ";

?>
    <!-- fin del bucle -->
</div> <hr>

<!--
   <h2>Crear Carpeta</h2>
    <form action="" method="post">
        Nombre de la carpeta:
        <input type="text" name="createFolder" required class="formtext">
        <input type="submit" value="Crear Carpeta">
    </form>

    <h2>Editar o crear Archivo</h2>
    <form action="" method="get">
        Nombre del archivo:
        <input type="text" name="editFile" value='<?php echo "uploads$carpetaz/";?>' required class="formtext">
        <input type="hidden" name="c" value='<?php echo "$carpetaz";?>' >
        <input type="submit" value="Editar Archivo">
    </form>


  

    <h2>Eliminar Archivo</h2>
    <form action="" method="get">
        Nombre del archivo:
        <input type="text" name="deleteFile" required class="formtext">
        <input type="submit" value="Eliminar Archivo">
    </form>


    <h2>Eliminar Carpeta (solo si esta vacia)</h2>
    <form action="" method="get">
        Nombre de la carpeta:
        <input type="text" name="deleteFolder" value='<?php echo "uploads$carpetaz";?>/' required class="formtext">
        <input type="hidden" name="c" value="<?php echo "$carpetap";?>" >
        <input type="submit" value="Eliminar Carpeta">
    </form>
-->






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
?>


        <br>
	<div class="tabla">
		<div class="fila">
			<div class="celda"> 

<?php
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



			</div>
		</div>
	</div> <br>


<hr> 
File manager public! Version publica 1.0b en <a href='https://zidrave.net/' target='_black'>http://zidrave.net</a>
<hr>
<footer> 
Notas: Utilitario simple y potente para la gestion de archivos en servidores web sin panel. 
<br>
<a href='?editFile=/../<?php echo "$scriptfile";?>.php'  class='naranja' role='button'><b>😍 EDIT THIS SCRIPT 🛠️</b></a>   
<a href='https://github.com/zidrave/filemanager_1filephp/' target='_black' class='azulin' role='button'><b>😍 View Proyect GitHub 🛠️</b></a>
<a href='https://www.youtube.com/@zidrave' target='_black2' class='naranja' role='button'><b>▶️ Youtube 🔴</b></a> 
<a href='https://www.tiktok.com/@zidrave' target='_black3' class='azulin' role='button'><b>▶️ Tiktok 🟣</b></a> 
<a href='https://www.paypal.com/donate?business=zidravex@gmail.com&currency_code=USD' target='_black4' class='naranja' role='button'><b>💲 Donate Paypal 💲</b></a>     
</footer> 
</body>
</html>
