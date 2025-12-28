<?php 
#          ,______________________________________       
#   - - - |_________________,----------._ [____]  ""-,__  __....-----=====
#                        (_(||||||||||||)___________/   ""                |
#                           `----------' zIDRAvE[ ))"-,                   |
#                     FILE MANAGER V4.4.1        ""    `,  _,--....___    |
#                     https://github.com/zidrave/        `/           """"
# 2025 y para adelante
//////////////POR SEGURIDAD CAMBIE ESTOS VALORES ///////////
$tokenplus = "pvt0zwwwwuFoewwwCpPZDq"; // cambie este valor es para darle mas seguridad a su script, desde aqui obtenemos el $masterkey para
                                       // acceder sin esperar  ejemplo: file4.php?unlock=pvt0z  para cambiarlo cambia el tokenplus las primeras 5 letras
$pepper = "e%OrrrrpPZDq_U7tXz9#mK2@pL4wN"; // cambie este valor es para darle mas seguridad a su script
////// Cambiar estos valores TOKENPLUS y PEPPER antes de crear tu usuario administrador, si lo cambias despues de configurar tu cuenta
////// admin nunca logeara la unica solución es que borres manualmente el archivo fconfig.json 
$configFile = '.htconfig.json'; //obligatorio cambiar el archivo config pero siempre con .ht al inicio ejemplo: .htconfxx.json
//////////////POR SEGURIDAD CAMBIE ESTOS VALORES ANTES DE GRABAR EL USUARIO///////////

ob_start(); // 1. Siempre primero para evitar Error 500
if (session_status() === PHP_SESSION_NONE) { session_start(); }


$nombreMaquina = gethostname();
$hashCompleto = hash('sha256', $nombreMaquina);
$tokenhost = substr($hashCompleto, 0, 10);
#formato de mensajes de alerta
$fversion="4.4.1";
$alertaini=" <div class='mensajex'> <h2>";
$alertafin="  </h2> </div> ";
$scriptfile="file4"; //no cambiar este nombre por que se decalibran varias cosas
$scriptfm = $scriptfile;
$scriptfm = strtoupper($scriptfm); #pasar a mayuscula papi
$mod = isset($_GET['mod']) ? $_GET['mod'] : ''; // algunas cositas van con mod
$expire_time = time() + 2592000; //valor puesto para 30 dias
#$ippublic = file_get_contents('https://api.ipify.org/'); //solo con internet
$miip = $_SERVER['REMOTE_ADDR'];
$haship = hash('sha256', $miip);
$ihash = hash('sha256', $miip . $pepper); // Usamos el Pepper para mayor seguridad
$archivo_bloqueo = 'bloqueo.lock';
$segundos_bloqueo = 20;
$is_authenticated = false; // Por defecto nadie está autenticado
$master = ""; // Inicializar para evitar errores
$acceso_emergencia = false; //aqui siempre false 
$archivo_registro_unlock = 'unlocks_hist.log'; // Registro de timestamps, no hace falta cambiar
$limite_horas = 24 * 3600; // 24 horas en segundos
$master_key = substr($tokenplus, 0, 5); //estoy servira para el unlock

















////Cookie Reforce
$cookiePath = "/"; // Simplificado para evitar errores de parseo
$cookieParams = "; SameSite=Lax"; // Lax es compatible con HTTPS y redirecciones
//$cookiePath = "/; SameSite=Strict";
$cookieDomain = ""; // Dejar vacío para el host actual
//$isSecure = false;  // Cambiar a true si usas HTTPS (recomendado)
$isSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
$isHttpOnly = true; // ACTIVADO: Protege contra robo por JavaScript
////

//////////////idioma predeterminado ES ////////////////////
$tl = array(
    'home' => 'Inicio',
    'uploadfile' => 'Subir Archivo',
    'name' => 'Nombre',
    'size' => 'Tamaño',
    'modified' => 'Modificado',
    'permissions' => 'Permisos',
    'owner' => 'Propietario',
    'welcome' => 'Bienvenido',
    'exit' => 'Salir',
    'foldercontent' => 'Contenido de la carpeta',
    'allowphpfile' => 'Permitir PHP',
    'uploadmultiplefiles' => 'Subir multiples Archivos',
    'systeminformation' => 'Informacion del Sistema',
    'usedspace' => 'Espacio Usado',
    'availablespace' => 'Espacio disponible',
    'usedmemory' => 'Memoria usada',
    'totalmemory' => 'Memoria total',
    'processorusage' => 'Uso del procesador',
    'coretemperature' => 'Temperatura del núcleo',
    'operatingsystem' => 'Sistema operativo',
    'description' => 'Notas: Utilitario simple y potente para la gestion de archivos en servidores web sin panel.',
    'viewproyect' => 'Repositorio Github',
    'editscript' => 'Editar Script',
    'donatepaypal' => 'Donacion Paypal',
    'averageload' => 'carga promedio',
    'createdby' => 'creado por',
    'folder' => 'Carpeta',
    'system' => 'Sistema',
    'deletenow' => 'Eliminar Ahora',
    'qdelete' => 'Está seguro de eliminar',
    'cancel' => 'Cancelar',
    'thefile' => 'El Archivo',
    'fileaction1' => 'ha sido subido exitosamente',
    'createfile' => 'Crear Archivo',
    'filename' => 'Nombre del archivo',
    'createdby' => 'creado por',
    'createfolder' => 'Crear Carpeta',
    'foldername' => 'Nombre de la Carpeta',
    'deletefolder' => 'Eliminar Carpeta',
    'onlyempty' => 'Solo si esta vacio',
    'configuration' => 'Configuracion',
    'saveconfiguration' => 'Guardar Configuracion',
    'deleteconfiguration' => 'Eliminar Configuracion',
    'user' => 'Usuario',
    'password' => 'Contraseña',
    'email' => 'Correo Electronico',
    'theme' => 'Tema',
    'language' => 'Idioma',
    'msgconfiguration' => 'Zona para configurar este sistema, el cual creara un archivo json para mantener la configuracion, no lo borre por que perdera la seguridad y cambios de esta configuracion',
    'update' => 'Actualizar',
    'cancel' => 'Cancelar',
    'msgupdate' => 'A continuacion procederemos a actualizar este sistema a su ultima version',
    'okupdate' => 'El Sistema se ha actualizado correctamente',
    'reload' => 'Recargar',
    'close' => 'Cerrar',
    'savefile' => 'Guardar Archivo',
    'discardchanges' => 'Descartar Cambios',
    'editing' => 'Editando',
    'delete' => 'Eliminar',
    'edit' => 'Editar',
    'compress' => 'Comprimir',
    'information' => 'Informacion',
    'copyfile' => 'Copiar Archivo',
    'renamefile' => 'Renombrar Archivo',
    'renamemove' => 'Renombrar o Mover',
    'filesize' => 'Tamaño de Archivo',
    'creationdate' => 'Fecha de Creación',
    'lastaccessdate' => 'Fecha de último acceso',
    'lastmodifieddate' => 'Fecha de última modificación',
    'group' => 'Grupo',
    'mimetype' => 'Tipo MIME',
    'currentservertime' => 'Hora actual del servidor',
    'fullpath' => 'Ruta Completa',
    'msgcompress' => 'Si no pone contraseña, no hay problema',
    'msgsavefile' => 'Texto guardado correctamente',
    'activate' => 'Activar',
    'desactivate' => 'Desactivar',
    'createdby' => 'creado por',


    'selectlanguage' => 'Seleccionar Idioma'
);



// Verificar si la cookie 'language' está configurada
if (isset($_COOKIE['language'])) {
    // Si existe, usar el valor de la cookie para definir el idioma
    $lang = $_COOKIE['language'];
} else {
    // Si no existe, usar el idioma por defecto (español)
    $lang = 'es';
    
    // Crear la cookie 'language' con el valor por defecto
    // setcookie('language', $lang, $expire_time, '/');
$options_lang = [
    'expires' => $expire_time,
    'path' => '/',
    'secure' => $isSecure,
    'httponly' => false, // False para que JS pueda leer el idioma si es necesario
    'samesite' => 'Lax'
];
setcookie('language', $lang, $options_lang);

}


// Función para cargar las traducciones desde el archivo JSON solo si se selecciona otro idioma
function loadTranslations($lang) {
    $file = __DIR__ . "/$lang.json";  // Ruta al archivo JSON
    if (file_exists($file)) {
        $json_data = file_get_contents($file);  // Leer el archivo JSON
        return json_decode($json_data, true);   // Convertir JSON a array
    }
    return null;  // Si no existe el archivo, devolver null
}






// Obtener el idioma desde la URL (por defecto español)
//$lang = isset($_GET['lang']) ? $_GET['lang'] : 'es';
if (isset($_GET['lang'])) {

$options_lang = [
    'expires' => $expire_time,
    'path' => '/',
    'secure' => $isSecure,
    'httponly' => false, 
    'samesite' => 'Lax'
];

$lang = $_GET['lang'];

//setcookie('language', "$lang", $expire_time, '/');
setcookie('language', $lang, $options_lang);


} else {
    // Si no existe el parámetro lang en la URL, verificar si la cookie 'language' está configurada
    $lang = isset($_COOKIE['language']) ? $_COOKIE['language'] : 'es';  // Idioma por defecto 'es'
}

// Si el idioma no es español, cargar el archivo JSON correspondiente
if ($lang !== 'es') {
    $loadedTranslations = loadTranslations($lang);
    if ($loadedTranslations !== null) {
        $tl = $loadedTranslations;  // Sobrescribir las traducciones con las del archivo JSON
    }
}







//////////////idioma////////////////////


















///DEFINIR COLOR POR DOMINIO
// Obtener el nombre del dominio actual
$host = $_SERVER['HTTP_HOST'];
// Crear un hash MD5 a partir del dominio
$hash = md5($host);
// Tomar los primeros 6 caracteres del hash como color hexadecimal
$colorHex = '#' . substr($hash, 0, 6);














#$stylealert = "
$stylealert = <<<EOD
<!-- codigo para crear un style de las alertas y seguridad -->
<style>
        body {
	    background-color: #f0f0f0; /* Fondo gris claro */
            font-family: Arial, sans-serif; /* Tipo de letra Arial */

        }
        a {
            text-decoration: none;
            color: #436074; /* Color azul para enlaces */
        }
        a:hover {fconfiguracion
            color: #FF0000; /* Cambia a rojo al pasar el mouse */
        }

    header {
    background-color: #98a6b0; /* Gris oscuro */
    background-image: linear-gradient(to bottom, #98a6b0, #c0cad1); 
    color: #000; /* Texto blanco */
    text-align: left; /* alineacion */
    width: 99%; /* Ocupa todo el ancho */
    padding: 10px; /* Añade un poco de espacio interno */
    }

    .formtext {
     background-color: #c9d4da; /* Azul oscuro */
     color: #0c2b3d; /* Blanco */
     border: 2px dotted black; /* Borde punteado negro de 2px */
     padding: 5px; /* Espacio interno */
     margin: 3px;
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
</style>


EOD;

 






//     ACCESO DE EMERGENCIA
// --- CONTROL DE ACCESO DE EMERGENCIA ANTI-INTELIGENCIA (filemanager V4.4.0) ---
if (isset($_POST['unlock'])) {
    $ahora = time();
    $registros = [];
    
/////// Cargamos configuración para ver la IP de confianza /////////////////
// 1. DECLARACIÓN FALTANTE: Obtener y hashear la IP actual del visitante
    $mi_ip_actual = $_SERVER['REMOTE_ADDR'];
    $mi_ip_actual_hash = hash('sha256', $mi_ip_actual . $pepper); // Usamos el Pepper para coincidir con 'ihash'


    $configData = json_decode(file_get_contents($configFile), true);
    $ip_confianza = isset($configData['ihash']) ? $configData['ihash'] : '';

    // ¿Es el dueño en su IP de siempre?
    $es_owner_reconocido = ($mi_ip_actual_hash === $ip_confianza);


    // 1. Cargamos el historial existente
    if (file_exists($archivo_registro_unlock)) {
        $registros = explode("\n", trim(file_get_contents($archivo_registro_unlock)));
    }

    // 2. Filtramos los registros de las últimas 24 horas
    $registros_recientes = array_filter($registros, function($timestamp) use ($ahora, $limite_horas) {
        return ($ahora - (int)$timestamp) < $limite_horas;
    });

    $conteo_intentos = count($registros_recientes);

    // LÓGICA: Auto-limpieza de logs para evitar inundacion en el log
    if ($conteo_intentos > 30) { 
        // Si el log se ensucia demasiado, lo reseteamos a los últimos 5 para que no pese megabytes
        $registros_recientes = array_slice($registros_recientes, -5);
    }


    // 3. Verificamos si aún tiene intentos disponibles (Límite de 10)
    // logica maestra q verifica intentos o ip en json
    if ($conteo_intentos < 10 || $es_owner_reconocido) {
        
        // --- ACCIÓN FÍSICA SIEMPRE: Registramos el timestamp en el log ---
        // Esto sucede tanto si el token es "12345" como si es "pvt0z"
        $registros_recientes[] = $ahora;
        file_put_contents($archivo_registro_unlock, implode("\n", $registros_recientes));

        // --- VALIDACIÓN LÓGICA PRIVADA ---
        // Solo si el token coincide exactamente con los primeros 5 caracteres de $tokenplus
        if ($_POST['unlock'] === $master_key) {

       // SOLUCIÓN CAMBIO GET A POST: Guardamos el bypass en la sesión para que dure en la siguiente recarga
            $_SESSION['bypass_active'] = true;
            $_SESSION['bypass_time'] = time();
            $acceso_emergencia = true; 
        }
        // Si no es correcto, $acceso_emergencia se queda en false (valor por defecto)
        
    } else {
        // Bloqueo total si excedió los 10 registros en el log
        die("$stylealert $seguridadcabeza <div class='mensajex' style='background:white;'>
            <h2>🚫 Límite de Emergencia Agotado</h2>
            <p>Se han detectado <b>$conteo_intentos intentos</b> de acceso en las últimas 24 horas.</p>
            <p>Por seguridad, esta función ha sido inhabilitada temporalmente.</p>
            </div>");
    }
}


// 2. VERIFICACIÓN DE SESIÓN PARA EL BYPASS (Esto nose usaba en GET pero POST si necesita recordar $acceso_emergencia=true)
if (isset($_SESSION['bypass_active']) && $_SESSION['bypass_active'] === true) {
    // El bypass dura  (10 segundos) para que te dé tiempo a logearte
    if ((time() - $_SESSION['bypass_time']) < 10) {
        $acceso_emergencia = true;
    } else {
        unset($_SESSION['bypass_active'], $_SESSION['bypass_time']);
    }
}







// 4. MOSTRAR FORMULARIO DE DESBLOQUEO UNLOCK (Micro-Diseño Zidrave V4.4.9)
if (isset($_GET['unlockmode'])) {
    echo "$seguridadcabeza";
    echo "
    <div style='
        max-width: 320px; 
        margin: 40px auto; 
        padding: 15px; 
        background: #f4f4f4; 
        border: 1px solid #ccc; 
        border-radius: 4px; 
        font-family: Arial, sans-serif;'>
        
        <form action='?' method='post' style='display: flex; align-items: center; gap: 8px;'>
            <span style='color: #444; font-size: 13px; font-weight: bold;'>Bypass:</span>
            
            <input type='password' name='unlock' required 
                   style='flex: 1; padding: 6px; border: 1px solid #bbb; border-radius: 3px; font-size: 13px;'>
            
            <input type='submit' value='Enviar' 
                   style='padding: 6px 12px; background: #2c3e50; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 13px; font-weight: bold;'>
        </form>
    </div>";
}





//////// VERIFICAR SEGURIDAD (FLUJO UNIFICADO Y GLOBAL) /////////////////////////

   // if (session_status() === PHP_SESSION_NONE) { session_start(); }

 



if (file_exists($configFile)) {
    $configData = json_decode(file_get_contents($configFile), true);
    $seguridadcabeza = "$stylealert <header> <h1> 🌀 File Manager </h1></header> <br>";

    // --- VARIABLES MAESTRAS ---
    $master = $configData['fuser']; 
    $mastermail = $configData['fmail']; 
    $tokenhash_db = $configData['fpass'];
    $tokenhash_valid = hash('sha256', "$tokenplus$tokenhost$tokenhash_db");

    // 1. AUTO-LOGIN (Sincronizar Cookie con Sesión)
    if (!isset($_SESSION['user_auth']) || $_SESSION['user_auth'] !== true) {
        if (isset($_COOKIE['Hash']) && $_COOKIE['Hash'] === $tokenhash_valid && $haship === $configData['fhash']) {
            $_SESSION['user_auth'] = true;
            $_SESSION['user_name'] = $master;
        }
    }

    $is_authenticated = (isset($_SESSION['user_auth']) && $_SESSION['user_auth'] === true);

    // 2. MURO DE BLOQUEO (Solo para no autenticados)
    if (!$is_authenticated && file_exists($archivo_bloqueo) && !$acceso_emergencia) {
        $intentos = (int)trim(file_get_contents($archivo_bloqueo));
        if ($intentos < 1) $intentos = 1;
        
        $tiempo_creacion = filemtime($archivo_bloqueo);
        $segundos_espera = $segundos_bloqueo * pow(2, $intentos - 1);
        if ($segundos_espera > 86400) $segundos_espera = 86400;

        $tiempo_transcurrido = time() - $tiempo_creacion;

        if ($tiempo_transcurrido < $segundos_espera) {
            $restante = $segundos_espera - $tiempo_transcurrido;
            die("$stylealert $seguridadcabeza <div class='mensajex' style='background:#ffffff;'>
                <h2>⏳ Acceso Restringido</h2>
                <p>Demasiados fallos detectados (Intento #$intentos).</p>
                <p>Por seguridad, espere: <b style='color:red; font-size:1.5em;'>" . gmdate("H:i:s", $restante) . "</b></p>
                <br> 
                </div>");
        }
    }

    // 3. PROCESAR LOGIN POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fuser'], $_POST['fpass'])) {
        // Honeypot check
        if (!empty($_POST['fhemail'])) { die("Bot detected."); }

        $peppered_input = hash_hmac("sha512", $_POST['fpass'], $pepper);

        if ($_POST['fuser'] === $master && password_verify($peppered_input, $configData['fpass'])) {
            // LOGIN EXITOSO
            session_regenerate_id(true);
            $_SESSION['user_auth'] = true;
            $_SESSION['user_name'] = $master;

            if (file_exists($archivo_bloqueo)) { unlink($archivo_bloqueo); }

            $options = [
                'expires' => $expire_time, 'path' => $cookiePath, 'domain' => $cookieDomain,
                'secure' => $isSecure, 'httponly' => $isHttpOnly, 'samesite' => 'Lax'
            ];
            setcookie('loggedin', 'true', $options);
            setcookie('Hash', $tokenhash_valid, $options);

            $configData['fhash'] = $haship;
            $configData['ihash'] = $ihash;
            file_put_contents($configFile, json_encode($configData, JSON_PRETTY_PRINT));
            
            header("Location: $scriptfile.php");
            exit;
        } else {
            // LOGIN FALLIDO
            $intentos = 1;
            if (file_exists($archivo_bloqueo)) {
                $contenido = trim(file_get_contents($archivo_bloqueo));
                $intentos = (is_numeric($contenido)) ? (int)$contenido + 1 : 1;
            }
            file_put_contents($archivo_bloqueo, (string)$intentos, LOCK_EX);
            
            header("Location: $scriptfile.php"); // Redirigimos para que el "Muro" arriba atrape el bloqueo
            exit;
        }
    }

 // 4. MOSTRAR FORMULARIO (Si no está autenticado)
    if (!$is_authenticated) {
        echo "$seguridadcabeza";
        echo '<form action="" method="post">';
        echo ' <b>Usuario </b>: <input type="text" name="fuser" required> ';
        echo ' <b>Contraseña </b>: <input type="password" name="fpass" required placeholder="Ingrese su contraseña"> ';
        echo ' <input type="submit" value="Acceso"> ';
        echo '
    <div style="display:none;">
        <input type="text" name="fhemail" value="">
    </div>
        ';
        echo '</form> <hr> <small>Seguridad '.$scriptfile.' - '.$fversion.' </small>';
        
        exit;
    }


// ELIMINAR DESDE AQUÍ:

// HASTA AQUÍ (Fin de lo que debes borrar)
}



//////// VERIFICAR SEGURIDAD FIN /////////////////////////

















if (isset($_GET['test'])) {
echo "prueba master es $master";
exit;
}


/////
//buscando la ruta real de cada carpeta
$ruta = $_GET['c'];
$uploadDir = 'uploads'.$ruta.'';
$rutarealserver = realpath($uploadDir);
////

///////////////////////////////////////
///      SUBIR VARIOS X AJAX     //////
///////////////////////////////////////
if (isset($_GET['varios'])) {

    // 1. PROTECCIÓN DE SESIÓN: Si no está autenticado, el script muere aquí.
    if (!isset($is_authenticated) || $is_authenticated !== true) {
        header('HTTP/1.1 403 Forbidden');
        exit("Error: Acceso no autorizado.");
    }


$ruta = $_GET['c'];
#echo "subiendo varios test en uploads$ruta";
echo "
<xscript>alert('subiendo varios test en uploads$ruta');</scriptx>
";

if (!empty($_FILES['files']['name'][0])) {
    $uploadDir = 'uploads'.$ruta.'';
    
    // Crear el directorio de subida si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
        $fileName = basename($_FILES['files']['name'][$key]);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $targetFile)) {
            echo "Archivo subido: $fileName\n";
        } else {
            echo "Error al subir el archivo: $fileName\n";
        }
    }
} else {
    echo "No se han recibido archivos.";
}


exit;
}
///////////////////////////////////////
///      SUBIR VARIOS X AJAX     //////
///////////////////////////////////////








      
//////////////////////////////////
///      Guardar X AJAX     //////
//////////////////////////////////
if (isset($_GET['guardax'])) {
     
#echo "guardando en ajax";
    // PROTECCIÓN EXTRA: Si por alguna razón llegó aquí sin sesión, matamos el proceso.
    if (!$is_authenticated) { 
        header('HTTP/1.1 403 Forbidden');
        exit("Acceso denegado."); 
    }
  
  
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    #$texto = filter_var($_POST['texto'], FILTER_SANITIZE_STRING); // Sanitizar el texto
    $texto = $_POST['texto'];
      $filename = filter_var($_POST['miArchivo'], FILTER_SANITIZE_STRING); // Sanitizar el texto
      $carpeta = filter_var($_POST['miCarpeta'], FILTER_SANITIZE_STRING); // Sanitizar el texto

    if (!empty($texto)) {
        $archivo = "uploads$carpeta/$filename";
        $fp = fopen($archivo, 'w');
        fwrite($fp, $texto. "");
        fclose($fp);
        #echo "Texto guardado correctamente en $archivo - ---- $carpeta/$filename ---- el contenido que dice : $texto ";
        //echo "Texto guardado correctamente.";
        echo $tl['msgsavefile']; 
    } else {
        echo "El texto está vacío.";
    }
} else {
    echo "Solicitud no valida.";
} 
  
  

  

  
    exit;
}
//////////////////////////////////
///  FIN Guardar X AJAX     //////
//////////////////////////////////








/////// fexit (Cierre de Sesión Seguro) ////////////////////////
if (isset($_GET['fexit'])) {
    // 1. Limpiar variables de sesión y destruirla
   // if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $_SESSION = array();
    session_destroy();

    // 2. Preparar expiración en el pasado
    $past = time() - 3600;

    // 3. Borrado de cookies usando array de opciones (Igual que en el Login)
    $logoutOptions = [
        'expires' => $past,
        'path' => $cookiePath,
        'domain' => $cookieDomain,
        'secure' => $isSecure,
        'httponly' => $isHttpOnly,
        'samesite' => 'Lax'
    ];

    setcookie('loggedin', '', $logoutOptions);
    setcookie('Hash', '', $logoutOptions);
    setcookie('PTMx', '', $logoutOptions);
    
    // 4. Redirigir al archivo limpio
    header("Location: $scriptfile.php");
    exit;
}



///////EDITOR PLUS COOKIEr////////////////////////

$cokiruta=$_GET['c'];
$cokifile=$_GET['editFile'];

///////EDITOR PLUS COOKIEr////////////////////////
$options_editor = [
    'expires' => $expire_time,
    'path' => '/',
    'secure' => $isSecure,
    'httponly' => true,
    'samesite' => 'Lax'
];

if (isset($_GET['oneditor'])) {
 setcookie('editor', 'true', $options_editor);
//setcookie('editor', 'true', $expire_time, '/'); 

#usleep(500000);
header("Location: $scriptfile.php?editFile=$cokifile&c=$cokiruta/");
exit;
}

///////EDITOR PLUS COOKIEr////////////////////////
if (isset($_GET['offeditor'])) {

 //setcookie('editor', '', $expire_time, '/'); 
   setcookie('editor', '', $options_editor);
#usleep(500000);
header("Location: $scriptfile.php?editFile=$cokifile&c=$cokiruta/");
exit;
}
///////EDITOR PLUS COOKIEr////////////////////////


















////// BORRAR Configuración (CON PROTECCIÓN DE IDENTIDAD) /////////////////////////////////
if (isset($_GET['fborrarconfiguracion'])) {
    
    // Si ya envió la contraseña de confirmación vía POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_pass'])) {
        $peppered_confirm = hash_hmac("sha512", $_POST['confirm_pass'], $pepper);
        
        // Verificamos contra la clave actual en el JSON
        if (password_verify($peppered_confirm, $configData['fpass'])) {
            
            // 1. DESTRUCCIÓN TOTAL DE SESIÓN
          //  if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $_SESSION = array(); // Limpiar variables
            
            // Destruir la cookie de sesión (PHPSESSID) en el navegador
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy(); // Matar sesión en el servidor

            // 2. Limpiar cookies de persistencia (Zidrave Tokens)
            $past = time() - 3600;
            $logoutOptions = [
                'expires' => $past,
                'path' => $cookiePath,
                'domain' => $cookieDomain,
                'secure' => $isSecure,
                'httponly' => $isHttpOnly,
                'samesite' => 'Lax'
            ];

            setcookie('loggedin', '', $logoutOptions);
            setcookie('Hash', '', $logoutOptions);
            setcookie('PTMx', '', $logoutOptions);

            // 3. ELIMINACIÓN DEL ARCHIVO FÍSICO
            if (file_exists($configFile)) {
                unlink($configFile);
                header("Location: $scriptfile.php");
                exit;
            }
        } else {
            echo "$seguridadcabeza <div class='mensajex' style='background:red;'><h2>❌ Contraseña de confirmación incorrecta.</h2></div>";
        }
    }

    // Interfaz de confirmación
    echo "$seguridadcabeza";
    echo "<div class='mensajex'>
            <h2>⚠️ Confirmar Acción Crítica</h2>
            <p>Para borrar la configuración y el usuario administrador, ingrese su contraseña actual:</p>
            <form method='POST'>
                <input type='password' name='confirm_pass' required placeholder='Tu contraseña' class='formtext'>
                <input type='submit' value='BORRAR TODO' style='background:red;'>
                <a href='?c=$carpetaz/' class='verde'>CANCELAR</a>
            </form>
          </div>";
    exit;
}








/////// Guardar Configuración /////////////////////////////////
// Detectamos el parámetro GET que envía tu formulario
if (isset($_GET["fconfiguracion"])) {

//echo "VERIFICAR GUARDANDO DATOS";

    // 1. Verificamos que se haya enviado por POST para procesar datos
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Cargamos la configuración actual para no perder lo que no se cambie
        $configActual = json_decode(file_get_contents($configFile), true);

        // 2. Recoger datos del formulario
        $fuser = $_POST['afuser'];
        $mastermail = $_POST['fmail'];
        $fskin = $_POST['fskin'];
        $flanguaje = $_POST['flanguaje'];

        // 3. LÓGICA INTELIGENTE DE CONTRASEÑA
        if (!empty($_POST['afpass'])) {
            // Si el usuario escribió una nueva clave, la hasheamos con Pepper
            $peppered_pass = hash_hmac("sha512", $_POST['afpass'], $pepper);
            $fpass_final = password_hash($peppered_pass, PASSWORD_DEFAULT);
        } else {
            // Si el campo llegó vacío, mantenemos la contraseña que ya estaba guardada
            $fpass_final = $configActual['fpass'];
        }

        // 4. Actualizar Identificadores de IP (Huella Digital Secreta)
        //$ihash_actual = hash('sha256', $_SERVER['REMOTE_ADDR'] . $pepper);
          $ihash_actual = $ihash;

        // 5. Crear el array final
        $config = [
            'fuser'     => $fuser,
            'fpass'     => $fpass_final,
            'fmail'     => $mastermail,
            'fskin'     => $fskin,
            'fhash'     => $haship, // Hash para el auto-login (IP plana o con sal)
            'flanguaje' => $flanguaje,
            'ihash'  => $ihash_actual // Hash para la inmunidad del unlock
        ];


// =========================
// 2. Procesar skin por POST
// =========================
$themex = $_POST['fskin'];
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $themex)) {
    die("Nombre de theme inválido");
}


$theme_options = [
    'expires' => time() + (30 * 24 * 60 * 60),
    'path' => '/',
    'secure' => $isSecure,  // ✅ Usar tu variable existente
    'httponly' => false,     // Puede ser false porque no es sensible
    'samesite' => 'Lax'
];
setcookie('fm_theme', $themex, $theme_options);
 


        // 6. Guardado Atómico con Bloqueo
        if (file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT), LOCK_EX)) {
            echo "$seguridadcabeza $alertaini ✅ Configuración guardada correctamente. $alertafin";
        } else {
            echo "$seguridadcabeza $alertaini ❌ Error crítico: No se pudo escribir en el archivo JSON. $alertafin";
        }

        echo "<br><a href='$scriptfile.php?mod=config' class='naranja'> <b>VOLVER AL INICIO</b></a>";
        exit;
    }
}







?>
<!DOCTYPE html>
<html>
<head>
    <title>File Manager V4</title>

<?php
// Definimos la ruta del archivo de estilo externo
$themeActivo = $_COOKIE['fm_theme'] ?? '';
// Esto elimina puntos (.), barras (/) y caracteres especiales
$themeActivo = preg_replace('/[^a-zA-Z0-9_-]/', '', $themeActivo);
$externalStyle = 'fmstyle_'.$themeActivo.'.css';

if (file_exists($externalStyle)) {
    // 1. Si el archivo existe, cargamos el link externo (Ignora el estilo interno)
  //echo '<link rel="stylesheet" type="text/css" href="' . $externalStyle . '?v=' . filemtime($externalStyle) . '">';
    echo '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($externalStyle, ENT_QUOTES, 'UTF-8') . '?v=' . filemtime($externalStyle) . '">';
} else {
    // 2. Si NO existe, cargamos tu style predeterminado (Softpedia Style)
?>

<style>
        body {
	    background-color: #f0f0f0; /* Fondo gris claro */
            font-family: Arial, sans-serif; /* Tipo de letra Arial */

        }
        a {
            text-decoration: none;
            color: #436074; /* Color azul para enlaces */
        }
        a:hover {
            color: #FF0000; /* Cambia a rojo al pasar el mouse */
        }

        .tabla {
            display: table;
            width: 1000px;
            border-collapse: collapse;
            background-color: white; /* Fondo blanco para la tabla */
        }


















 .filasinfx {
    display: table-row;
    border-bottom: 1px solid #ddd;
 }

.fila {
    display: table-row;
    border-bottom: 1px solid #ddd;
    position: relative; /* Necesario para pseudo-elementos */
    overflow: hidden;
    z-index: 1; /* Asegura que el contenido de la fila esté por encima del fondo */
}

.fila::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    height: 100%;
    width: 100%;
    background-color: #d5dfe2; /* Color de relleno */
    transition: left 0.4s ease-in-out;
    z-index: 0; /* El pseudo-elemento está por debajo del contenido */
}

.fila:hover::before {
    left: 0; /* Efecto de relleno desde la izquierda */
}

.fila:nth-child(even):hover::before {
    background-color: #d5dfe2 !important; /* Asegura el mismo color en filas pares */
}

/* Asegura que el contenido de la fila esté en el nivel superior */
.fila * {
    position: relative;
    z-index: 1; /* Asegura que el texto, íconos, etc. se muestren por encima del fondo */
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
            width: 85px; /* Ancho fijo para la celda2 */
            padding: 3px;
            border: 1px solid #ddd; /* Agrega un borde a la celda2 */
        }
        .celda4 {
            display: table-cell;
            width: 360px; /* Ancho fijo para la celda2 */
            padding: 2px;
            border: 1px solid #ddd; /* Agrega un borde a la celda2 */
        }
        .fila:nth-child(even) {
            background-color: #f1f6f9; /* Color de fondo para filas pares */
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
    background-color: #98a6b0; /* Gris oscuro */
    background-image: linear-gradient(to bottom, #98a6b0, #c0cad1); /
    color: #000; /* Texto blanco */
    text-align: left; /* alineacion */
    width: 99%; /* Ocupa todo el ancho */
    padding: 10px; /* Añade un poco de espacio interno */
    }

    footer {
    background-color: #b9cad4; /* Gris  */
    background-image: linear-gradient(to bottom,  #dee4e8 , #b9cad4); /
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

   .rojito {
      background-color: #a60000;  
      color: #fff;  
      padding: 10px 20px; /* Espacio interno */
      text-decoration: none; /* Quita el subrayado */
      border-radius: 5px; /* Bordes redondeados */
      display: inline-block; /* Muestra el elemento como un bloque en línea */
    }
   .verde {
      background-color: #04ab8a;  
      color: #fff;  
      padding: 10px 20px; /* Espacio interno */
      text-decoration: none; /* Quita el subrayado */
      border-radius: 5px; /* Bordes redondeados */
      display: inline-block; /* Muestra el elemento como un bloque en línea */
    }
   .naranja {
      background-color: #FFA500; /* Color naranja */
      color: #fff; /* Texto blanco */
      padding: 10px 20px; /* Espacio interno */
      text-decoration: none; /* Quita el subrayado */
      border-radius: 5px; /* Bordes redondeados */
      display: inline-block; /* Muestra el elemento como un bloque en línea */
    }
   .snaranja {
      background-color: #F99600; /* Color naranja */
      color: #fff; /* Texto blanco */
      padding: 5px 10px; /* Espacio interno */
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

   .azulin2 {
      background-color: #2c4c5e; /* Color naranja */
      color: #fff; /* Texto blanco */
      padding: 5px 10px; /* Espacio interno */
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
     margin: 3px;
     }
    .formtext2 {
     background-color: #a5b9c2; /* Azul oscuro */
     color: #0c2b3d; /* Blanco */
     border: 2px dotted black; /* Borde punteado negro de 2px */
     padding: 5px; /* Espacio interno */
     margin: 3px;
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
            z-index: 2;
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



        .editor-wrapper {
            display: flex;
            border: 1px solid #ccc;
            overflow: hidden;
            height: 450px; /* Altura ajustada */
            width: 1200px; /* Anchura ajustada */
        }
        .line-numbers {
            background-color: #5e737d;
            font-family: Fira Code, Consolas, Courier New, monospace;
            padding: 8px 10px;
            line-height: 1.25;
            text-align: right;
            user-select: none;
            overflow: hidden;
            color: #ffffff;
        }
        .code-editor {
           font-family: Fira Code, Consolas, Courier New, monospace;
            width: 100%;
            border: none;
            outline: none;
            padding: 8px;
            resize: none;
            line-height: 1.5;
            overflow-y: scroll;
            white-space: nowrap;
            background-color: #d7dfe0;
        }
        .editor-container {
            display: flex;
            width: 100%;
            height: 100%;
        }

</style>

<?php 
} // Cierre del else style external
?>


</head>
<body>




<?php
/////////verificar que exista algun usuario creado
if (empty($master)) {
    #echo "La variable \$master está vacía.";
?>



<table style="width: 100%; background-color: red;">
    <tr>
        <td style="text-align: left; padding: 10px; color: white;">
            <b> ⚠️ Modo Inseguro </b>: Por favor crea una Contraseña en: <b> ⚙️ <a href="?mod=config" class='snaranja' role='button'>Configurar</a></b>
        </td>
    </tr>
</table>
<?php
} /////////verificar que exista algun usuario creado
?>




<?php
#edicion privada del script mas puton y util para mi 
#version 1.0beta index III
$uploadDir = 'uploads/';
$activeDir = 'uploads';




// para la lista de carpetas con links
#$getruta=$_GET['c'];
$getruta = isset($_GET['c']) ? $_GET['c'] : '/';
$rutax = "/$getruta";
$partes = explode('/', trim($rutax, '/'));
$acumulado = "/";








#    $carpetap = $_POST['c'];

#if (isset($_GET['c'])) {
if (isset($getruta)) {

    #$carpetax = $_GET['c'];
    #$carpetap = $_GET['c'];
    #$carpetaz = $_GET['c'];
    $carpetax = $getruta;
    $carpetap = $getruta;
    $carpetaz = $getruta;
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
   echo " ⚠️ FALTA el parámetro 'c' ";
       }
////correccion en caso que alguien ponga "c=" sin nada mas
if ($uploadDir === "uploads") {
    $uploadDir .= "/";
}
#echo "test mensaje: el valor de c es [$uploadDir]";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755);
}

/////////// Subir archivo basico ////////////////////////////////////////
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



































/////// ACTUALIZAR SISTEMA (CON PROTECCIÓN DE IDENTIDAD Y PARCHEO DINÁMICO) /////////////////////////////////
if (isset($_GET['fupdate'])) {

    // 1. Interfaz de Confirmación (Prevención CSRF)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['confirm_update_pass'])) {
        echo "$seguridadcabeza";
        echo "<div class='mensajex'>
                <h2>🚀 Actualizar Sistema ($fversion)</h2>
                <p>Esta acción descargará la última versión desde GitHub y sobrescribirá el archivo actual.</p>
                <p style='color:#ffeb3b;'>⚠️ <b>Nota:</b> El sistema parchará automáticamente el nuevo archivo para mantener tus archivos de Configuración y Seguridad actuales.</p>
                <form method='POST'>
                    <input type='password' name='confirm_update_pass' required placeholder='Tu contraseña de admin' class='formtext'>
                    <input type='submit' value='INICIAR ACTUALIZACIÓN SEGURA' style='background:#04ab8a;'>
                    <a href='?c=$carpetaz/' class='naranja'>CANCELAR</a>
                </form>
              </div>";
        exit;
    }

    // 2. Validación de Identidad
    $peppered_confirm = hash_hmac("sha512", $_POST['confirm_update_pass'], $pepper);
    if (!password_verify($peppered_confirm, $configData['fpass'])) {
        die("$seguridadcabeza <div class='mensajex' style='background:red;'><h2>❌ Contraseña incorrecta. Operación cancelada por seguridad.</h2></div>");
    }

    // 3. Proceso de Descarga
    $furl = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/file4.php';
    $furlicon = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/favicon.ico';
    $furlidioma = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/en.json';
    $furlidioma2 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/de.json';

    $furlskin1 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_dark-zidrave.css';
    $furlskin2 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_original.css';
    $furlskin3 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_taringa.css';
    $furlskin4 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_dark-red.css';
    $furlskin5 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_dark-leonardo.css';

    $rutaArchivoLocal = isset($_GET['updatefile']) ? $_GET['updatefile'] . ".php" : "$scriptfile.php";

    $fcontenido = @file_get_contents($furl);
    $fcontenidoicon = @file_get_contents($furlicon);
    $fcontenidolang = @file_get_contents($furlidioma);
    $fcontenidolang2 = @file_get_contents($furlidioma2);

    $fcontenidoskin1 = @file_get_contents($furlskin1);
    $fcontenidoskin2 = @file_get_contents($furlskin2);
    $fcontenidoskin3 = @file_get_contents($furlskin3);
    $fcontenidoskin4 = @file_get_contents($furlskin4);
    $fcontenidoskin5 = @file_get_contents($furlskin5);

    if ($fcontenido === FALSE) {
        die(" $alertaini ⚠️ No se pudo descargar el archivo desde GitHub. Revisa la conexión del servidor. $alertafin ");
    }

    // --- 4. EL TRUCO MÁGICO: Parcheo Dinámico con Regex Robusta ---
    // Esta regex busca $variable = "..." o '...' sin importar los espacios y mantiene tus valores actuales.
    
    $patrones = [
        '/\$tokenplus\s*=\s*(["\']).*?\1;/'  => '$tokenplus = "' . $tokenplus . '";',
        '/\$pepper\s*=\s*(["\']).*?\1;/'     => '$pepper = "' . $pepper . '";',
        '/\$configFile\s*=\s*(["\']).*?\1;/' => '$configFile = "' . $configFile . '";'
    ];

    $fcontenido = preg_replace(array_keys($patrones), array_values($patrones), $fcontenido);

    // 5. Reemplazo de Archivos en Disco
    if (file_put_contents($rutaArchivoLocal, $fcontenido) === FALSE) {
        die(" $alertaini ⚠️ Error crítico: No se pudo escribir en $rutaArchivoLocal. Verifica permisos de escritura. $alertafin ");
    }

    if ($fcontenidoicon) file_put_contents("favicon.ico", $fcontenidoicon);
    if ($fcontenidolang) file_put_contents("en.json", $fcontenidolang);
    if ($fcontenidolang2) file_put_contents("de.json", $fcontenidolang2);

    if ($fcontenidoskin1) file_put_contents("fmstyle_dark-zidrave.css", $fcontenidoskin1);
    if ($fcontenidoskin2) file_put_contents("fmstyle_original.css", $fcontenidoskin2);
    if ($fcontenidoskin3) file_put_contents("fmstyle_taringa.css", $fcontenidoskin3);
    if ($fcontenidoskin4) file_put_contents("fmstyle_dark-red.css", $fcontenidoskin4);
    if ($fcontenidoskin5) file_put_contents("fmstyle_dark-leonardo.css", $fcontenidoskin5);
    

    echo " $alertaini ⚠️ " . $tl['okupdate'] . " $alertafin";
    echo "<a href='?c=$carpetaz/' class='naranja' role='button'> <b> " . $tl['reload'] . " </b></a>";
    exit;
}






























// Eliminar archivo (Versión con Protección de Configuración)
if (isset($_GET['deleteFile'])) {

    if (!isset($is_authenticated) || $is_authenticated !== true) {
        header('HTTP/1.1 403 Forbidden');
        exit("Error: Acceso no autorizado.");
    }

    $fileToDelete = $_GET['deleteFile'];
    $archivoname = basename($fileToDelete);

    // --- PROTECCIÓN ---
    // 1. No se puede borrar el archivo definido en $configFile
    // 2. No se puede borrar el propio script ejecutable (file4.php)
    if ($archivoname === $configFile || $archivoname === "$scriptfile.php") {
        echo "$alertaini ❌ ERROR: El archivo <span style='color:yellow;'>$archivoname</span> es un archivo de sistema y NO puede ser eliminado. $alertafin";
    } 
    else {
        // Proceder con el borrado si no es un archivo protegido
        if (file_exists($fileToDelete)) {
            if (unlink($fileToDelete)) {
                echo "$alertaini ⚠️ El archivo <span style='color:red;'>$archivoname</span> ha sido eliminado... $alertafin";
            } else {
                echo "$alertaini ❌ Error al intentar eliminar el archivo. $alertafin";
            }
        } else {
            echo "$alertaini ⚠️ El archivo <span style='color:red;'>$archivoname</span> no fue encontrado. $alertafin";
        }
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
    echo "<a href='?editFile=$elarchivo&c=$c/' class='naranja' role='button'> <b> RECARGAR </b></a>";
    exit;
}

// Renombrar archivo
if (isset($_POST['renameFile'])) {
    $oldName = $uploadDir . $_POST['oldName'];
    $newName = $uploadDir . $_POST['newName'];

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


// copiar archivo
if (isset($_POST['copyFile'])) {
    $oldName = $uploadDir . $_POST['oldName'];
    $newName = $uploadDir . $_POST['newName'];

    if (file_exists($oldName)) {
        if (copy($oldName, $newName)) {
            echo "$alertaini ⚠️ Archivo Copiado. $alertafin ";
    echo "<a href='?c=$carpetap' class='naranja' role='button'><b>RECARGAR </b></a>";
    exit;


        } else {
            echo " $alertaini ⚠️ Error al copiar el archivo. $alertafin ";
        }
    } else {
        echo " $alertaini ⚠️ Archivo no encontrado. $alertafin  ";
    }
}














////////////////// Comprimir archivo o carpeta 🚀 🚀🚀🚀🚀🚀🚀🚀
if (isset($_POST['compressFile'])) {
    $namefilec = $_POST['archivoacomprimir'];
    $namefilepass = $_POST['password'];
    $descripcion = $_POST['descripcion'];

    $nombreZipa = isset($_POST['archivoacomprimir']) ? $_POST['archivoacomprimir'] . '.zip' : 'archivo_protegido.zip';
    $nombreZip = "uploads$getruta$nombreZipa";
    $namefilepass = isset($_POST['password']) ? $_POST['password'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

    // Instanciar ZipArchive
    $zip = new ZipArchive();

    // Función para comprimir carpeta con contraseña
    function comprimirCarpetaConContrasena($origen, $destino, $excluir = [], $contrasena) {
        $zip = new ZipArchive();

        if ($zip->open($destino, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            die("No se pudo crear el archivo ZIP.\n");
        }

        $archivos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($origen, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($archivos as $archivo) {
            $rutaRelativa = str_replace($origen . '/', '', $archivo->getPathname());

            $excluirArchivo = false;
            foreach ($excluir as $itemExcluido) {
                if (strpos($rutaRelativa, $itemExcluido) === 0) {
                    $excluirArchivo = true;
                    break;
                }
            }

            if (!$excluirArchivo) {
                if ($archivo->isDir()) {
                    $zip->addEmptyDir($rutaRelativa);
                } else {
                    $zip->addFile($archivo->getPathname(), $rutaRelativa);

                    if ($contrasena) {
                        $zip->setEncryptionName($rutaRelativa, ZipArchive::EM_AES_256, $contrasena);
                    }
                }
            }
        }

        $zip->close();

        if (!file_exists($destino)) {
            echo "$alertaini ⚠️Error al comprimir la carpeta con contraseña.\n $alertafin";
        } else {
            #echo "$alertaini ⚠️ Carpeta comprimida y con Password en:\n $destino\n $alertafin ";
        }
    }

    // Verificar si se pasa un archivo o carpeta mediante POST
    if (isset($_POST['archivoacomprimir'])) {
        $ruta = "uploads$getruta" . $_POST['archivoacomprimir'];
        if (is_file($ruta)) {
            // Añadir un único archivo
            $zip->open($nombreZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $zip->addFile($ruta, basename($ruta));
            if (!empty($namefilepass)) {
                $zip->setEncryptionName(basename($ruta), ZipArchive::EM_AES_256, $namefilepass);
            }
            $zip->close();
            echo "$alertaini ⚠️El archivo <b>$namefilec.zip</b> se ha creado correctamente. $alertafin";
        } elseif (is_dir($ruta)) {
            // Añadir una carpeta completa
            comprimirCarpetaConContrasena($ruta, $nombreZip, [], $namefilepass);
            echo "$alertaini ⚠️ La carpeta <b>$namefilec.zip</b> se ha creado correctamente. $alertafin";
        } else {
            echo " $alertaini ⚠️ La ruta especificada no es válida $ruta . $alertafin ";
            exit;
        }
    } else {
        echo "No se especificó ningún archivo o carpeta.";
        exit;
    }

    // Agregar un comentario al archivo ZIP
    if (!empty($descripcion)) {
        $zip = new ZipArchive();
        if ($zip->open($nombreZip) === TRUE) {
            $zip->setArchiveComment($descripcion);
            $zip->close();
        } else {
            echo " $alertaini ⚠️ No se pudo abrir el archivo ZIP para agregar el comentario. $alertafin ";
        }
    }

    echo "<a href='?c=$carpetap' class='naranja' role='button'><b>RECARGAR </b></a>";
    exit;
}
////////////////// Comprimir archivo o carpeta 🚀 🚀🚀🚀🚀🚀🚀🚀














// Listar archivos y carpetas
$items = scandir($uploadDir);






?>





    <header>
        <h1> 🌀 File Manager   -  <?php echo "$scriptfm";?>   



 <a href='?'>🏠</a>   <a href='?c=<?php echo "$carpetaz";?>/../'>↩️</a>   <a href='?mod=creartexto&c=<?php echo "$carpetaz";?>/'>📝</a> <a href='?mod=crearcarpeta&c=<?php echo "$carpetaz";?>/'> 🗂️ </a>  <a href='?mod=eliminarcarpeta&c=<?php echo "$carpetaz";?>/'>❌</a> <a href='?mod=config&c=<?php echo "$carpetaz";?>/'>⚙️ </a> <a href='?mod=update&c=<?php echo "$carpetaz";?>/'> 🔄 </a></h1>
    </header>




<div style="width:100%; height:5px; background-color:<?php echo "$colorHex";?>;"></div>

<a href='<?php echo "$scriptfile";?>.php' class='enlacez' role='button'>
<?php echo $tl['home'];?>:  </a> /

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
<br>
<?php
//echo realpath(__FILE__);
//echo __DIR__;
//echo "$rutarealserver ";
?>
<hr>

<?php
///////////////////////////// SUBIR ARCHIVOS AL SISTEMA 2 MODOS CLASICO Y MULTIPLE ////////////////////
if (isset($_GET['uploadmultiple']) && $_GET['uploadmultiple'] === '1') {
#echo "subir muchos files";
?>





    <style>
        #drop-area {
            width:85%;

            padding: 30px;
            border: 2px dashed #ccc;
            text-align: center;
            font-family: Arial, sans-serif;
            margin: 40px auto;
        }
        #drop-area.highlight {
            border-color: #06c;
        }
        #file-list {
            margin-top: 20px;
        }
        #progress-bar {
            width: 75%;
            background-color: #f3f3f3;
            margin: 20px auto; /* Centrar horizontalmente */
            height: 30px;
            border-radius: 5px;
            overflow: hidden;
        }
        #progress-bar-fill {
            height: 100%;
            width: 0;
            background-color: #06c;
            text-align: center;
            line-height: 30px;
            color: white;
        }
    </style>

	<div class="tabla">
		<div class="filasinfx">
			<div class="celda">  


    <div id="drop-area">
        <h3>Arrastra y suelta tus archivos aquí</h3>
        <p>O haz clic para seleccionarlos</p>
        <input type="file" id="fileElem" multiple accept="*" style="display:none">
        <button id="fileSelect">Seleccionar archivos</button>
        <div id="file-list"></div>
        <div id="progress-bar">
            <div id="progress-bar-fill">0%</div>


        </div>
<center>   <a href="?c=<?php echo "$carpetaz";?>/" class="azulin2"> Cerrar </a>   </center>
    </div>



			</div>
		</div>
	</div> 


    <script>
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('fileElem');
        const fileList = document.getElementById('file-list');
        const progressBarFill = document.getElementById('progress-bar-fill');

        dropArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropArea.classList.add('highlight');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('highlight');
        });

        dropArea.addEventListener('drop', (event) => {
            event.preventDefault();
            dropArea.classList.remove('highlight');
            const files = event.dataTransfer.files;
            handleFiles(files);
        });

        document.getElementById('fileSelect').addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            const files = fileInput.files;
            handleFiles(files);
        });

        function handleFiles(files) {
            const formData = new FormData();
            for (const file of files) {
                formData.append('files[]', file);
                const li = document.createElement('li');
                li.textContent = file.name;
                fileList.appendChild(li);
            }

            // Enviar archivos al servidor con barra de progreso
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'file4.php?varios=1&c=<?php echo "$carpetax";?>', true);

            xhr.upload.addEventListener('progress', (event) => {
                if (event.lengthComputable) {
                    const percentComplete = (event.loaded / event.total) * 100;
                    progressBarFill.style.width = percentComplete + '%';
                    progressBarFill.textContent = Math.round(percentComplete) + '%';
                }
            });

            xhr.onload = () => {
                if (xhr.status === 200) {
                    alert('Archivos subidos con éxito!');
                    console.log(xhr.responseText);
                } else {
                    alert('Error al subir los archivos.');
                }
            };

            xhr.send(formData);
        }
    </script>



<?php
} else {
?>
<div class="upload-section">
    <form action="" method="post" enctype="multipart/form-data" class="upload-form">
        
<div class="file-input-wrapper">
        <input type="file" name="fileToUpload" id="fileToUpload"  >
</div>
        <label>
            <input type="checkbox" name="allowPhpUpload" value="yes"> <?php echo $tl['allowphpfile'];?>
        </label>

        <input type="submit" value=" ⬆️ <?php echo $tl['uploadfile'];?>" name="submit" class="btn btn-primary">
      <a href="?c=<?php echo "$carpetaz/";?>&uploadmultiple=1" class="btn btn-warning"> <?php echo $tl['uploadmultiplefiles'];?> </a>
    </form>
</div>

<br>
<?php
}
/////////////// FIN ////////////// SUBIR ARCHIVOS AL SISTEMA 2 MODOS CLASICO Y MULTIPLE ////////////////////
?>














<?php if (isset($fileContent)): 
 $textarea="ready";
?>




<?php
////////////condicion para el boton editor plus ////////////////////////////////////
if (isset($_COOKIE['editor']) && $_COOKIE['editor'] === 'true') {
?>
<b>  <a href="?offeditor=1&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetaz";?>" class="azulin2"> <?php echo $tl['desactivate'];?> Editor Plus </a> </b>


<?php

  } else {
?>

<b>  <a href="?oneditor=1&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetaz";?>" class="snaranja"> <?php echo $tl['activate'];?> Editor Plus </a> </b>

<?php 
}
?>

 <?php endif; ?>
















<?php
if (isset($textarea) && !empty($textarea)) {



if (isset($_COOKIE['editor']) && $_COOKIE['editor'] === 'true') {

#echo "opcion textarea1";
?>
<br>
<h2> 📝 <?php echo $tl['editing'];?>: <?php echo htmlspecialchars($_GET['editFile']); ?> [Editor Plus]</h2>

    <style>


    </style>
 
<div class="editor-wrapper">
    <div class="editor-container">
        <div class="line-numbers" id="lineNumbers">1</div>
        <textarea id="codeEditor" class="code-editor" oninput="updateLineNumbers()" onscroll="syncScroll()"><?php echo htmlspecialchars($fileContent); ?></textarea>
    </div>
</div>





            <input id="miArchivo" type="" name="miArchivo" value="<?php echo htmlspecialchars($_GET['editFile']); ?>" class="formtext">
            <input id="miCarpeta"  type="hidden" name="miCarpeta" value='<?php echo "$carpetaz";?>' >
            <button onclick="guardarTexto()"> <?php echo $tl['savefile'];?></button> <a href="?mod=oneditor&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetaz";?>/" class="azulin2"> <?php echo $tl['discardchanges'];?> </a>  <a href="?c=<?php echo "$carpetaz";?>/" class="azulin2"> <?php echo $tl['close'];?> </a> <br>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>



 <script>
    function guardarTexto() {
      var texto = $('#codeEditor').val();
      var miArchivo = $('#miArchivo').val();
      var miCarpeta = $('#miCarpeta').val(); 

      $.ajax({
        type: "POST",
        url: "<?php echo "$scriptfile";?>.php?guardax=1", // Aquí va la ruta a tu script PHP 
        data: {
          texto: texto,
          miArchivo: miArchivo,
          miCarpeta: miCarpeta
        },
        success: function(response) {
          alert(response); // Puedes mostrar un mensaje de éxito o error
        }
      });
    }
  </script>

<script>
    function updateLineNumbers() {
        const editor = document.getElementById('codeEditor');
        const lines = editor.value.split('\n').length;
        const lineNumbers = document.getElementById('lineNumbers');
        
        lineNumbers.innerHTML = Array.from({length: lines}, (_, i) => i + 1).join('<br>');
    }

    function syncScroll() {
        const editor = document.getElementById('codeEditor');
        const lineNumbers = document.getElementById('lineNumbers');
        lineNumbers.scrollTop = editor.scrollTop;
    }

    // Inicializar line numbers en el caso de que textarea tenga contenido precargado
    window.onload = updateLineNumbers;
</script>



<?php
  } else {
//aca debe ir el textareaviejo
#echo "opcion textarea2";
?>

        <h2> 📝 <?php echo $tl['editing'];?>: <?php echo htmlspecialchars($_GET['editFile']); ?> [Editor Simple]</h2>
        <form action="" method="post">
            <textarea name="fileContent" rows="30" cols="165"  class="formtext" ><?php echo htmlspecialchars($fileContent); ?></textarea><br>
            <input type="hiddenx" name="fileName" value="<?php echo htmlspecialchars($_GET['editFile']); ?>" class="formtext">
            <input type="hidden" name="c" value='<?php echo "$carpetaz";?>' >
            <input type="submit" name="saveFile" value="<?php echo $tl['savefile'];?>"> <a href="?mod=oneditor&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetaz";?>/" class="azulin2"><?php echo $tl['discardchanges'];?> </a>  <a href="?c=<?php echo "$carpetaz";?>/" class="azulin2"> <?php echo $tl['close'];?> </a>
        </form>

<?php
}

} //zona para cualquier textarea


?>











 <?php if (isset($nullfileContent)): ?>

        <h2> 📝 Editando: <?php echo htmlspecialchars($_GET['editFile']); ?></h2>
        <form action="" method="post">
            <textarea name="fileContent" rows="30" cols="165"  class="formtext" ><?php echo htmlspecialchars($fileContent); ?></textarea><br>
            <input type="hiddenx" name="fileName" value="<?php echo htmlspecialchars($_GET['editFile']); ?>" class="formtext">
            <input type="hidden" name="c" value='<?php echo "$carpetaz";?>' >
            <input type="submit" name="saveFile" value="GUARDAR ARCHIVO"> <a href="?mod=oneditor&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetaz";?>" class="azulin2">Descartar Cambios </a>  <a href="?c=<?php echo "$carpetaz";?>/" class="azulin2"> Cerrar </a>
        </form>
    <?php endif; ?>













































<?php

///////////////////////////////////////// CONFIGURAR SISTEMA /////////////////////////⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️
#$mod=$_GET['mod'];
$mod = isset($_GET['mod']) ? $_GET['mod'] : '';
?>
















<?php if ($mod == "update"): ?>

       <br>
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> 

   <h2> 🔄 <?php echo $tl['update'];?>: </h2>

<br>
<form action="?fupdate=ok&c=<?php echo "$carpetaz/";?>&updatefile=<?php echo "$scriptfile";?>" method="post">
        <?php echo $tl['msgupdate'];?>. <br><br>
        <input type="submit" value=" <?php echo $tl['update'];?> "> 
        <a href='?c=<?php echo "$carpetaz";?>/' class='azulin'> <?php echo $tl['cancel'];?></a><br>


    </form>


     <br>
			</div>
		</div>
	</div> <br>


<?php endif; ?>










<?php if ($mod == "config"): ?>

       <br>
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> 

   <h2> ⚙️ <?php echo $tl['configuration'];?> </h2>
    <form action="?fconfiguracion=ok" method="POST">
        <?php echo $tl['msgconfiguration'];?>. <br><br>
        <input type="text" name="afuser" required class="formtext" value="<?php echo "$master";?>"> <?php echo $tl['user'];?> <br>
        <input type="text" name="afpass"  class="formtext"> <?php echo $tl['password'];?> <br>
        <input type="text" name="fmail" required class="formtext" value="<?php echo "$mastermail";?>"> <?php echo $tl['email'];?> <br>



<?php
$themeActivo = $_COOKIE['fm_theme'] ?? '';

// ========================
// 1. Buscar themes reales
// ========================
$skindirectorio = __DIR__;
$skinarchivos = glob($skindirectorio . '/fmstyle*.css');
$skinpalabras = [];

foreach ($skinarchivos as $archivo) {
    $nombre = basename($archivo);

    if (!preg_match('/^fmstyle_[a-zA-Z0-9_-]+\.css$/', $nombre)) {
        continue; // Saltar archivos con nombres inválidos
    }

    $palabra = str_replace(['fmstyle', '.css'], '', $nombre);
    $palabra = ltrim($palabra, '-_');

    if ($palabra !== '') {
        $skinpalabras[] = $palabra;
    }
}

$skinpalabras = array_unique($skinpalabras);
sort($skinpalabras);

?>


<select name="fskin" class="formtext">
    <option value="">🎨 Seleccionar tema _______</option>
    <?php foreach ($skinpalabras as $theme): ?>
        <option value="<?= htmlspecialchars($theme) ?>"
            <?= $theme === $themeActivo ? 'selected' : '' ?>>
            <?= htmlspecialchars(ucfirst($theme)) ?>
        </option>
    <?php endforeach; ?>
</select> <?php echo $tl['theme'];?> <br>

        <input type="text" name="flanguaje" required class="formtext" value="spanish" readonly> <?php echo $tl['language'];?> <br><br>

        <input type="submit" value="<?php echo $tl['saveconfiguration'];?>"> <br><br>
        <a href='?fborrarconfiguracion=1&c=<?php echo "$carpetaz";?>/' class='azulin'> <?php echo $tl['deleteconfiguration'];?> </a><br>


    </form>


     <br>
			</div>
		</div>
	</div> <br>


<?php endif; ?>





<?php

///////////////////////////////////////// CREAR FOLDER ////////////
#$crearelfolder=$_GET['crearelfolder'];
?>

<?php if ($mod == "crearcarpeta"): ?>

       <br>
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> 

   <h2> 🗂️  <?php echo $tl['createfolder'];?></h2>
    <form action="" method="post">
         <?php echo $tl['foldername'];?>:
        <input type="text" name="createFolder" required class="formtext">
        <input type="submit" value="<?php echo $tl['createfolder'];?>">
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

<?php if ($mod == "creartexto"): ?>

       <br>
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> 

    <h2> 📝 <?php echo $tl['createfile'];?></h2>
    <form action="" method="get">
        <?php echo $tl['filename'];?>:
        <input type="text" name="editFile" value='' required class="formtext"> 
        <input type="hidden" name="c" value='<?php echo "$carpetaz";?>' >
        <input type="submit" value="<?php echo $tl['createfile'];?>">
    </form>


     <br>
			</div>
		</div>
	</div> <br>


<?php endif; ?>






<?php
//////////////////////////////////// ELIMINAR CARPETA PREGUNTANDO  ////////////
#$eliminarcarpeta=$_GET['eliminarcarpeta'];
?>


<?php if ($mod == "eliminarcarpeta"): ?>

       <br>
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> 
       <h2> ❌ <?php echo $tl['deletefolder'];?> (<?php echo $tl['onlyempty'];?>)</h2>
    <form action="" method="get">
        <?php echo $tl['foldername'];?>:
        <input type="text" name="deleteFolder" value='' required class="formtext">
        <input type="hidden" name="c" value="<?php echo "$carpetap";?>" >
        <input type="submit" value="<?php echo $tl['deletefolder'];?>">
    </form>
     <br>
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
		<div class="filasinfx">
			<div class="celda"> 
    <h2> 📚 <?php echo $tl['compress'];?> ZIP (<?php echo "$comprimir";?>)</h2>
    <form action="" method="post">
       <?php echo $tl['msgcompress'];?>:<br>
        <input type="hidden" name="archivoacomprimir" value="<?php echo "$comprimir";?>" required class="formtext" readonly>
        <?php echo $tl['password'];?>:
        <input type="text" name="password" value=""  class="formtext">
        <input type="hidden" name="c" value="<?php echo "$carpetap";?>" >
        <input type="hidden" name="descripcion" value="
           ,______________________________________       
          |_________________,----------._ [____]  ''-,__  __....-----====
                        (_(||||||||||||)___________/   ''                |
                           `----------' zIDRAvE[ ))'-,                   |
                     FILE MANAGER V<?php echo "$fversion";?>        ''    `,  _,--....___    |
                     https://github.com/zidrave/        `/           ''''
...................................................................................
2024
" >
        <input type="submit" value="<?php echo $tl['compress'];?>" name="compressFile">  <a href='?c=<?php echo "$carpetap";?>' class='azulin2'> <?php echo $tl['close'];?> </a>
    </form><br>



			</div>
		</div>
	</div> <br>


<?php endif; ?>











<?php
//////////////////////////////////// cambiar nombre  ////////////
$archivoacambiarnombre=$_GET['archivoacambiarnombre'];
$archivoacambiarnombre2 = "uploads$carpetap$archivoacambiarnombre";
 if (isset($archivoacambiarnombre)):

function xformatSize2($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
    return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

 ?>









<?php


#///agregando iconos personalzados para info archivos////
        if (is_dir($archivoacambiarnombre2)) {
            $fileType = 'folder';
            $icon = '📂';
        } else {
            $fileType = 'file';          
            // Asignar iconos basados en la extensión del archivo
            $extension = pathinfo($archivoacambiarnombre, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'jpg':
                case 'jpeg':
                case 'webp':
                case 'jfif':
                case 'bmp':
                case 'png':
                case 'gif': //
                    $icon = '🖼️'; // Icono para imágenes
                    break;
                case 'php':
                case 'exe':
                case 'py':
                case 'sh':
                    $icon = '⚙️'; // Icono para ejecutables
                    $editable = 'ok';
                    break;
                case 'txt':
                case 'json':
                case 'rtf':
                case 'ini':
                case 'js':
                case 'htm':
                case 'html':
                    $icon = '📝'; // Icono para archivos de texto
                    $editable = 'ok';
                    break;
                case 'pdf':
                    $icon = '📕'; // Icono para archivos PDF
                    break;
                case 'doc':
                case 'docx':
                    $icon = '📘'; // Icono para archivos PDF
                    break;
                case 'zip':
                case 'rar':
                    $icon = '📚'; // Icono para archivos comprimidos
                    $comprimible = 'ok';
                    break;
                case 'mp3':
                case 'wav':
                    $icon = '🎵'; // Icono para archivos de audio
                    break;
                case 'mp4':
                case 'mkv':
                    $icon = '🎥'; // Icono para archivos de video
                    break;
                default:
                    $icon = '📜'; // Icono genérico para otros archivos
                    break;
            }
        }

#///agregando iconos personalzados ////



?>
      


	<div class="tabla">
		
		<div class="filasinfx">
			<div class="celda"> 

<center> <h1> <?php echo " $icon $archivoacambiarnombre";?> </h1> </center> 
    <style>
        .containerx {
            width: 100%;
            padding: 0px;
            display: flex;
            border: 1px solid black;
        }
        .column {
            width: 50%;
            padding: 5px;
            border: 0px solid black;
            box-sizing: border-box;
        }
    </style>
<div class="containerx">
    <div class="column">

<?php

$fileinfo="uploads$carpetap$archivoacambiarnombre";

        // Obtener tamaño del archivo
        $sizer = filesize($fileinfo);
        $serverTime = date('d-m-Y H:i:s');
        $fileType = filetype($fileinfo);
        $md5Hash = md5_file($fileinfo);
        // Obtener fechas importantes
        $creationTimee = filectime($fileinfo);
        $lastAccessTimee = fileatime($fileinfo);
        $lastModificationTimee = filemtime($fileinfo);

        // Obtener permisos del archivo
        $permissions = substr(sprintf('%o', fileperms($fileinfo)), -4);

        // Obtener el propietario y el grupo
        $ownerID = fileowner($fileinfo);
        $groupID = filegroup($fileinfo);
        $ownerInfo = posix_getpwuid($ownerID);
        $groupInfo = posix_getgrgid($groupID);

        // Obtener el tipo MIME del archivo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileinfo);
        finfo_close($finfo);



        // Mostrar la información del archivo

        echo "<h3> 🖊️ ".$tl['information']." </h3>";
        echo "<p><strong>▶️ ".$tl['fullpath'].":</strong> <br><input type='text' id='campo' name='campo' value='$fileinfo' style='width: 460px;' class='formtext'><br>";
        echo "<p><strong>▶️ ".$tl['filesize'].":</strong> " . xformatSize2($sizer) . "</p>";
        echo "<p><strong>▶️ ".$tl['creationdate'].":</strong> " . date('d-m-Y H:i:s', $creationTimee) . "</p>";
        echo "<p><strong>▶️ ".$tl['lastaccessdate'].":</strong> " . date('d-m-Y H:i:s', $lastAccessTimee) . "</p>";
        echo "<p><strong>▶️ ".$tl['lastmodifieddate'].":</strong> " . date('d-m-Y H:i:s', $lastModificationTimee) . "</p>";
        echo "<p><strong>▶️ ".$tl['permissions'].":</strong> " . $permissions . "</p>";
        echo "<p><strong>▶️ ".$tl['owner'].":</strong> " . $ownerInfo['name'] . " (UID: $ownerID)</p>";
        echo "<p><strong>▶️ ".$tl['group'].":</strong> " . $groupInfo['name'] . " (GID: $groupID)</p>";
        echo "<p><strong>▶️ ".$tl['mimetype'].":</strong> " . $mimeType . "</p>";
        echo "<p><strong>▶️ ".$tl['currentservertime'].":</strong> " . $serverTime . "</p>";
#        echo "<p><strong>▶️ Tipo de archivo:</strong> " . $fileType . "</p>";
        echo "<p><strong>▶️ Hash MD5:</strong> " . $md5Hash . "</p>";


 

?>
    </div>
    <div class="column">

    <h3> 🖊️  <?php echo $tl['renamemove'];?></h3>
    <form action="" method="post">
        
        <input type="hidden" name="oldName" value="<?php echo "$archivoacambiarnombre";?>"  readonly required class="formtext">
         
        <input type="text" name="newName" value="<?php echo "$archivoacambiarnombre";?>" required class="formtext" style='width: 250px;'>
        <input type="hidden" name="c" value="<?php echo "$carpetap";?>" >
        <input type="submit" value="<?php echo $tl['renamefile'];?>" name="renameFile">
    </form>
<hr>
    <h3> 🖊️ <?php echo $tl['copyfile'];?>  </h3>
    <form action="" method="post">
        <input type="hidden" name="oldName" value="<?php echo "$archivoacambiarnombre";?>"  readonly required class="formtext">
        
        <input type="text" name="newName" value="<?php echo "$archivoacambiarnombre";?>" required class="formtext" style='width: 250px;'>
        <input type="hidden" name="c" value="<?php echo "$carpetap";?>" >
        <input type="submit" value="<?php echo $tl['copyfile'];?>" name="copyFile">
    </form>
<hr>

<?php

$archivoimagen = "$archivoacambiarnombre"; // Cambia esta variable según sea necesario

// Obtiene la extensión del archivo
$extension = strtolower(pathinfo($archivoimagen, PATHINFO_EXTENSION));

// Verifica si la extensión es una de las deseadas webp
if (in_array($extension, ['jpg', 'bmp', 'tiff', 'gif', 'jfif', 'jpeg', 'png', 'webp'])) {
#    echo "La extensión del archivo es .jpg, .bmp, .tiff o .gif";
 echo "<a href='$fileinfo' target='_black69'><img src='$fileinfo' height='250' ></a>";
} else {
#    echo "La extensión del archivo no es .jpg, .bmp, .tiff o .gif";
}
#echo "<br><a href='' class='snaranja'>Eliminar</a>";
?>
    </div>
</div>


<hr>
  
 





<br>

<center>   
<?php if ($editable == "ok"): ?>

 <a href="?editFile=<?php echo "$archivoacambiarnombre";?>&c=<?php echo "$carpetap";?>" class='naranja'>  <?php echo $tl['edit'];?> </a> 

<?php endif; ?>


<?php if (!$comprimible == "ok"): ?>
             <a href="?comprimir=<?php echo "$archivoacambiarnombre";?>&c=<?php echo "$carpetap";?>" class='verde'>   <?php echo $tl['compress'];?> </a>     
<?php endif; ?>

  <a href="?c=<?php echo "$carpetap";?>" class='azulin'>  <?php echo $tl['close'];?>  </a>       <a href='?deleteFile=uploads<?php echo "$carpetap";?><?php echo "$archivoacambiarnombre";?>&c=<?php echo "$carpetap";?>' class='rojito' onclick="return confirm('¿Estás seguro de que deseas eliminar este archivo?');">  <?php echo $tl['delete'];?> </a> </center>

<br>



			</div>
		</div>
	</div> <br>
<?php endif; ?>






 

<?php
/////// USUARIO LOGEADO MENSAJE  ////////// 🙋‍♂️ 🙋‍♂️ 🙋‍♂️ 🙋‍♂️ 🙋‍♂️ 🙋‍♂️ 🙋‍♂️ 🙋‍♂️ 🙋‍♂️ 🙋‍♂️ 🙋‍♂️ 

if (isset($is_authenticated) && $is_authenticated === true) {
    echo "🙋‍♂️ ".$tl['welcome']." <b>$master / [<a href=\"?fexit=1\">".$tl['exit']."</a>]</b>";
  }

//preparando parceo de ruta real del server//////////////////
// 1. Definiciones de ruta absoluta (Mantenemos la lógica de base)
$baseReal = realpath(__DIR__ . "/uploads"); 
$rutaExplorada = realpath($rutarealserver);

// Preparamos los arrays
$arrBase = explode(DIRECTORY_SEPARATOR, trim($baseReal, DIRECTORY_SEPARATOR));
$arrExplo = explode(DIRECTORY_SEPARATOR, trim($rutaExplorada, DIRECTORY_SEPARATOR));
// AGREGAMOS EL ELEMENTO VACÍO AL INICIO para representar la raíz "/"
array_unshift($arrExplo, "");


?>

        <br>
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> <?php echo $tl['foldercontent']; ?>: <b> 
<?php 

//echo "$rutarealserver/"; 
//parceador///
$totalPartes = count($arrExplo);

            foreach ($arrExplo as $indice => $nombre) {
                // Reconstruimos la ruta física para este punto
                // Si el nombre es vacío, es la raíz "/"
                if ($nombre === "") {
                    $rutaDestino = DIRECTORY_SEPARATOR;
                } else {
                    // Quitamos el primer elemento vacío para reconstruir la ruta de carpetas reales
                    $carpetasReales = array_filter(array_slice($arrExplo, 0, $indice + 1));
                    $rutaDestino = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $carpetasReales);
                }

                $arrDestino = explode(DIRECTORY_SEPARATOR, trim($rutaDestino, DIRECTORY_SEPARATOR));

                // --- CÁLCULO DE RELATIVIDAD ---
                $maxComun = 0;
                $minLen = min(count($arrBase), count($arrDestino));
                for ($i = 0; $i < $minLen; $i++) {
                    if (isset($arrBase[$i], $arrDestino[$i]) && $arrBase[$i] === $arrDestino[$i]) {
                        $maxComun = $i + 1;
                    } else {
                        break;
                    }
                }

                $subidas = count($arrBase) - $maxComun;
                $relativo = "";
                for ($i = 0; $i < $subidas; $i++) {
                    $relativo .= "../";
                }

                $bajadas = array_slice($arrDestino, $maxComun);
                if (!empty($bajadas)) {
                    $relativo .= implode("/", $bajadas);
                }

                // Normalización de Trail Slash y Prefijo
                $enlaceLimpio = trim($relativo, "/");
                if ($enlaceLimpio !== "") {
                    $enlaceLimpio = "/" . $enlaceLimpio . "/";
                } else {
                    $enlaceLimpio = "/";
                }

                // --- DIBUJAR LINK ---
                // Si el nombre está vacío, mostramos el símbolo de Raíz "/"
                $label = ($nombre === "") ? " / " : $nombre;
                
                echo "<a href='?c=" . htmlspecialchars($enlaceLimpio) . "' style='color:var(--navigation); font-weight:bold;'>📂$label</a> ";
                
                if ($indice < $totalPartes - 1) {
                    echo " <span style='color:#ccc;'> ➡︎ </span> ";
                }
            }
///fin de parceador ////            


?>

                 </b>
			</div>
		</div>
	</div> <br>

  
   <div class="tabla">
  
    <div class="fila">
        <div class="celda4"><b><?php echo $tl['name'];?></b></div>
        <div class="celda3"><b><?php echo $tl['size'];?></b></div>
        <div class="celda2"><b><?php echo $tl['modified'];?></b></div>
        <div class="celda3"><b><?php echo $tl['permissions'];?></b></div>
        <div class="celda3"><b><?php echo $tl['owner'];?></b></div>
	<div class="celda"> _ </div>
         
    </div>



        <?php
/////////// inicio del bucle
echo " 


 ";





        foreach ($items as $item) {

            if ($item != '.' && $item != '..') {
$uploadDir = empty($uploadDir) ? '/' : $uploadDir; //arreglito aer
            $filePath = $uploadDir . $item;
            $filePerms = substr(sprintf('%o', fileperms($filePath)), -4);
            $fileOwner = posix_getpwuid(fileowner($filePath))['name'];
#            $fileModTime = date("Y-m-d / H:i", filemtime($filePath)); //d-m-Y H:i:s
            $fileModTime = date("d-m-Y / H:i", filemtime($filePath)); //d-m-Y H:i:s






#///agregando iconos personalzados ////
        if (is_dir($uploadDir . $item)) {
            $fileType = 'folder';
            $icon = '📂';
        } else {
            $fileType = 'file';
            $fileSize = filesize($fullPath);
            
            // Asignar iconos basados en la extensión del archivo
            $extension = pathinfo($item, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'jpg':
                case 'jpeg':
                case 'webp':
                case 'jfif':
                case 'bmp':
                case 'png':
                case 'gif': //
                    $icon = '🖼️'; // Icono para imágenes
                    break;
                case 'php':
                case 'exe':
                case 'py':
                case 'sh':
                    $icon = '⚙️'; // Icono para ejecutables
                    break;
                case 'txt':
                case 'json':
                case 'rtf':
                case 'ini':
                case 'js':
                case 'htm':
                case 'html':
                    $icon = '📝'; // Icono para archivos de texto
                    break;
                case 'pdf':
                    $icon = '📕'; // Icono para archivos PDF
                    break;
                case 'doc':
                case 'docx':
                    $icon = '📘'; // Icono para archivos PDF
                    break;
                case 'zip':
                case 'rar':
                    $icon = '📚'; // Icono para archivos comprimidos
                    break;
                case 'mp3':
                case 'wav':
                    $icon = '🎵'; // Icono para archivos de audio
                    break;
                case 'mp4':
                case 'mkv':
                    $icon = '🎥'; // Icono para archivos de video
                    break;
                default:
                    $icon = '📜'; // Icono genérico para otros archivos
                    break;
            }
        }

#///agregando iconos personalzados ////




                if (is_dir($uploadDir . $item)) {

echo " 
    <div class='fila'>
        <div class='celda'> ◽ $icon <a href='?c=$carpetaz/$item/'><b>$item</b>  </a> </div>
        <div class='celda'> ".$tl['folder']." </div>
        <div class='celda'>  $fileModTime </div>
        <div class='celda'>  $filePerms </div>
        <div class='celda'>  $fileOwner </div>
<!--	<div class='celda'>  [<a href='?archivoacambiarnombre=$uploadDir$item&c=$carpetaz/'>🖊️</a>] [<a href='?deleteFolder=$uploadDir$item&c=$carpetaz/'>❌</a>] --> 
	<div class='celda'>  [<a href='?archivoacambiarnombre=$item&c=$carpetaz/'>🖊️</a>] [<a href='?deleteFolder=$item&c=$carpetaz/'>❌</a>] [<a href='?comprimir=$item&c=$carpetaz/'>📚</a>]
     </div>
    </div>
 ";

                } else {
                 $fileSize = filesize($uploadDir . $item);


$itemr = $item;
if (strlen($itemr) > 33) {
    $itemr = substr($itemr, -33);
    $itemr = "➰".$itemr;
}

echo " 
    <div class='fila'>
        <div class='celda'> ◽ $icon <a href='$uploadDir$item' target='_black'>$itemr </a> </div>
        <div class='celda'> " . formatSize($fileSize) . " </div>
        <div class='celda'>  $fileModTime </div>
        <div class='celda'>  $filePerms </div>
        <div class='celda'>  $fileOwner </div>
	<div class='celda'>  [<a href='?editFile=$item&c=$carpetaz/'>✏️</a>] [<a href='?archivoacambiarnombre=$item&c=$carpetaz/'>🖊️</a>] [<a href='#eliminar_$item'>❌</a>] [<a href='?comprimir=$item&c=$carpetaz/'>📚</a>] </div>
    </div>
 ";

//zona de confirmaciones para eliminacion 

 echo "
        <div id='eliminar_$item' class='mensaje'>
            <center>
                <p><b>¿ ".$tl['qdelete']." ?</b></p><br>
                <p><h2>$item</h2></p><br>
                <a class='cerrar' href='?deleteFile=$uploadDir$item&c=$carpetaz/'>".$tl['deletenow']."</a> 
                <a class='cerrar' href='#'>".$tl['cancel']."</a>
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
        <div class='celda'> <a href='?'> ◽ 📁 <b> / </b></a> </div>
        <div class='celda'> ".$tl['folder']." </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  ".$tl['system']." </div>
	<div class='celda'>   </div>
    </div>  -->
 ";

echo " <!--
    <div class='fila'>
        <div class='celda'>  ◽ 📁 <a href='?c=$carpetaz/'> <b> . </b></a> </div>
        <div class='celda'> ".$tl['folder']." </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  ".$tl['system']." </div>
	<div class='celda'>   </div>
    </div>
    -->
 ";

echo " 
    <div class='fila'>
        <div class='celda'> ◽  <a href='?c=$carpetaz/../'>📁 <b>.. </b></a> </div>
        <div class='celda'> ".$tl['folder']." </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  ".$tl['system']." </div>
	<div class='celda'>   </div>
    </div>
   
 ";

?>
    <!-- fin del bucle -->
</div> <hr>







<?php

function getUptime() {
    $uptime = @file_get_contents('/proc/uptime');
    if ($uptime === false) {
        return 'No disponible';
    }

    $uptime = explode(' ', $uptime);
    $totalSeconds = (int) floatval($uptime[0]);

    $days = floor($totalSeconds / 86400);
    $hours = floor(($totalSeconds % 86400) / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;

    return "{$days}d - {$hours}h {$minutes}m {$seconds}s";
}



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


        
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> 

<?php
// Mostrar información  

echo "  <h2> 🖥️ ".$tl['systeminformation']." </h2>\n";
echo " \n";
echo " ✅ ".$tl['usedspace'].": " . formatSize($diskUsed) . "<br>\n";
echo " ✅ ".$tl['availablespace'].": " . formatSize($diskFree) . "<br>\n";
echo " ✅ ".$tl['usedmemory'].": <b> " . formatSize($memUsed) . " </b><br>\n";
echo " ✅ ".$tl['totalmemory'].": <b>" . formatSize($memTotal) . " </b><br>\n";
#echo "<li>Uso del procesador: " . $cpuLoad . " (carga promedio)<br>\n";
echo " ✅ ".$tl['processorusage'].": <b> " . $cpuLoad . " (".$tl['averageload'].") - " . $cpuUsage . " </b><br>\n";
echo " ✅ ".$tl['coretemperature'].": <b> " . $coreTemp . "  </b><br>\n";
//echo " ⏱️ ".$tl['uptime'].": <b>" . getUptime() . "</b><br>\n";
echo " ⏱️ Online: <b>" . getUptime() . "</b><br>\n";
echo " ✴️ ".$tl['operatingsystem'].": " . $os . "</li>\n";
echo " \n";

?>



			</div>
		</div>
	</div> <br>


<div class="upload-section"> 
FILE MANAGER | Full Version <b><?php echo "$fversion";?> </b> <?php echo $tl['createdby'];?> <a href='https://zidrave.net/' target='_black'>http://zidrave.net</a><br>
</div>

<hr>

<?php echo $tl['selectlanguage'];?> [<a href="?lang=es">Español</a> | <a href="?lang=en">Ingles</a> | <a href="?lang=de">Aleman</a>] <br><br>


<footer> 
 <?php echo $tl['description'];?>
<br><br>

<?php
if ($master === 'zidrave') {
echo "<a href='?editFile=/../$scriptfile.php'  class='naranja' role='button'><b>😍 ".$tl['editscript']." 🛠️</b></a>   ";
}
?>

<a href='https://github.com/zidrave/filemanager_1filephp/' target='_black' class='azulin' role='button'><b>😍 <?php echo $tl['viewproyect'];?> 🛠️</b></a>
<a href='https://www.youtube.com/@zidrave' target='_black2' class='naranja' role='button'><b>▶️ Youtube 🔴</b></a> 
<a href='https://www.tiktok.com/@zidrave' target='_black3' class='azulin' role='button'><b>▶️ Tiktok 🟣</b></a> 
<a href='https://www.paypal.com/donate?business=zidravex@gmail.com&currency_code=USD' target='_black4' class='naranja' role='button'><b>💲 <?php echo $tl['donatepaypal'];?> 💲</b></a>     
</footer> 
</body>
</html>
