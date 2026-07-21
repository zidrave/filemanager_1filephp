<?php
#          ,______________________________________       
#   - - - |_________________,----------._ [____]  ""-,__  __....-----=====
#                        (_(||||||||||||)___________/   ""                |
#                           `----------' zIDRAvE[ ))"-,                   |
#                     FILE MANAGER V4.4.7.5       ""    `,  _,--....___    |
#                     https://github.com/zidrave/        `/           """"
# 07/21/2026
# public_key_inmutable: 3JBT7LrYkydYPS3upQhJwB8pEi12nEfi2rbSTVIw/cs=

////////////// POR SEGURIDAD CAMBIE ESTOS VALORES ///////////
////////////// ANTES DE GUARDAR LA PRIMERA CONFIGURACION ///////////////////
$tokenplus = 'pvt0zwwwwuFoewwwCpPZDq'; // Cambie este valor es para darle mas seguridad a su script, desde aqui obtenemos el masterkey 
                                       // En caso de DDOS al login, acceder sin esperar: file4.php?bypass y la clave seria pvt0z,las primeras 5 letras del tokenplus
$pepper = 'e%OrrrrpPZDq_U7tXz9#mK2@pL4wN'; // Cambie este valor es para darle mas seguridad a su script

$configFile = '.htconfig.php'; //obligatorio cambiar el archivo config pero siempre con .ht al inicio ejemplo: .htconfx9x.php

////// Cambiar estos valores TOKENPLUS y PEPPER antes de crear tu usuario administrador, si lo cambias despues de configurar tu cuenta
////// admin nunca ingresara,la unica solución es que borres manualmente el archivo .htconfig.php (segun el nombre q le pusiste)

////////////// EOF - VALORES DE SEGURIDAD ///////////


//-- LISTA DE VARIABLES GENERALES --
$fversion="4.4.7.5";
$nombreMaquina = gethostname();
$hashCompleto = hash('sha256', $nombreMaquina);
$tokenhost = substr($hashCompleto, 0, 10);
#formato de mensajes de alerta
$alertaini=" <div class='mensajex'> <h2>";
$alertafin="  </h2> </div> ";
$scriptfile="file4"; //no cambiar este nombre por que se decalibran varias cosas
$scriptfm = $scriptfile;
$scriptfm = strtoupper($scriptfm); #pasar a mayuscula
$mod = isset($_GET['mod']) ? $_GET['mod'] : ''; // algunas cositas van con mod
$expire_time = time() + 2592000; //valor puesto para 30 dias
#$ippublic = file_get_contents('https://api.ipify.org/'); //solo con internet
//$miip = $_SERVER['REMOTE_ADDR'];
//mod ip real
$theip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
$miip = explode(',', $theip)[0];
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
$publicKeyBase64 = '3JBT7LrYkydYPS3upQhJwB8pEi12nEfi2rbSTVIw/cs='; //codigo inmutable


$totalArchivos = 0;
$totalCarpetas = 0;
$totalPesoCarpeta = 0;


$furlVersionCheck    = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/file4.php';
$version_cache_file  = __DIR__ . '/.htversion_cache'; // prefijo .ht = bloqueado por Apache automáticamente
$version_cache_segundos = 6 * 3600; // revisa como máximo cada 6 horas



// 1. Detectar y crear ruta basandonos en la url del scrip
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_url = $_SERVER['SCRIPT_NAME'];
$url_limpia = $protocol . $host . $script_url;
//convertir a hash corto
$hash_mini_id = substr(md5($url_limpia), 0, 5); 



////Cookie Reforce
$cookiePath = "/"; // Simplificado para evitar errores de parseo
$cookieParams = "; SameSite=Lax"; // Lax es compatible con HTTPS y redirecciones
$cookieDomain = ""; // Dejar vacío para el host actual
$isSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
$isHttpOnly = true; // ACTIVADO: Protege contra robo por JavaScript
////



// SESSION PHP - PREPARACION TIEMPO EXTENDIDO
ob_start(); // 1. Siempre primero para evitar Error 500
// Configuración de duración: 1 meses en segundos
$duracion = 10 * 60; // 10 minutos
// $isSecure debe calcularse ANTES de este bloque (moverlo desde donde está más abajo)
$isSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

// 2. Forzamos al servidor a mantener la sesión por 6 meses
ini_set('session.gc_maxlifetime', $duracion);

// Cuánto tiempo dura la COOKIE en el navegador, + flags de seguridad
session_set_cookie_params([
    'lifetime' => $duracion,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isSecure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
// 3. Iniciamos la sesión
if (session_status() === PHP_SESSION_NONE) { session_start(); }




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

    'efile' => 'Archivo',
    'summary' => 'Resumen',


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
$options_lang = [
    'expires' => $expire_time,
    'path' => '/',
    'secure' => $isSecure,
    'httponly' => false, // False para que JS pueda leer el idioma si es necesario
    'samesite' => 'Lax'
];
//si no existe idioma graba en la cookie el español
setcookie('language', $lang, $options_lang);

}

///FUNCIONES //////

function obtener_version_remota(string $url, string $cacheFile, int $cacheSegundos): ?string {
    // 1. Si hay caché reciente, la usamos y no tocamos la red
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheSegundos) {
        $cached = trim(@file_get_contents($cacheFile));
        return $cached !== '' ? $cached : null;
    }

    // 2. Descarga con timeout corto para no colgar la página si GitHub está lento/caído
    $contexto = stream_context_create([
        'http'  => ['timeout' => 3],
        'https' => ['timeout' => 3],
    ]);
    $remoto = @file_get_contents($url, false, $contexto);

    // 3. Si falla la descarga, reusa la última versión conocida en caché (si existe) en vez de fallar en seco
    if ($remoto === false) {
        if (file_exists($cacheFile)) {
            $cached = trim(@file_get_contents($cacheFile));
            return $cached !== '' ? $cached : null;
        }
        return null;
    }

    // 4. Extrae la versión del código fuente remoto, igual formato que $fversion="X.X.X";
    if (!preg_match('/\$fversion\s*=\s*["\']([^"\']+)["\']/', $remoto, $m)) {
        return null;
    }

    $versionRemota = $m[1];
    @file_put_contents($cacheFile, $versionRemota, LOCK_EX);
    return $versionRemota;
}



function hay_conexion_internet(string $host = 'raw.githubusercontent.com', int $puerto = 443, float $timeoutSegundos = 1.0): bool {
    $conexion = @fsockopen($host, $puerto, $errno, $errstr, $timeoutSegundos);
    if ($conexion) {
        fclose($conexion);
        return true;
    }
    return false;
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

//funciones nuevas para mejorar la seguridad del config file
function cfg_load(string $file): array {
    if (!file_exists($file)) return [];
    $fm_cfg = [];
    include $file;
    return $fm_cfg;
}

function cfg_save(array $data, string $file): bool {
    $export = var_export($data, true);
    return file_put_contents($file, "<?php \$fm_cfg = $export; ?>", LOCK_EX) !== false;
}







// Obtener el idioma desde la URL (por defecto español)
if (isset($_GET['lang'])) {

$options_lang = [
    'expires' => $expire_time,
    'path' => '/',
    'secure' => $isSecure,
    'httponly' => false, 
    'samesite' => 'Lax'
];

$lang = $_GET['lang'];

//esto graba el idioma elegido del menu idioma
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







//////////////idioma-EOF////////////////////


















///DEFINIR COLOR POR DOMINIO
// Obtener el nombre del dominio actual
$host = $_SERVER['HTTP_HOST'];
// Crear un hash MD5 a partir del dominio
$hash = md5($host);
// Tomar los primeros 6 caracteres del hash como color hexadecimal
$colorHex = '#' . substr($hash, 0, 6);















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

$newstylealert = <<<EOD
<!-- new code-->
<style>
        body { background: #f0f0f0; font-family: Arial, sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0; }
        .auth-card { background: #fff; width: 380px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden; }
        .auth-header { background: linear-gradient(to bottom, #98a6b0, #c0cad1); padding: 15px 20px; font-size: 18px; font-weight: bold; color: #000; }
        .auth-body { padding: 30px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #0c2b3d; font-size: 14px; }
        .form-input { width: 100%; padding: 10px; border: 2px solid #dadce0; border-radius: 6px; font-size: 15px; box-sizing: border-box; }
        .form-input:focus { outline: none; border-color: #436074; }
        button, input[type="submit"] { width: 100%; background: #FFA500; border: none; color: #fff; padding: 10px; font-size: 16px; font-weight: bold; cursor: pointer; border-radius: 6px; }
        button:hover, input[type="submit"]:hover { opacity: 0.9; }
        .auth-footer { text-align: center; margin-top: 20px; font-size: 12px; color: #5f6368; }
        a { text-decoration: none; color: #436074; }
        a:hover { color: #FF0000; }
     
</style>
EOD;









 






//     ACCESO DE EMERGENCIA
// --- CONTROL DE ACCESO DE EMERGENCIA ANTI-DDOS  ---
if (isset($_POST['unlock'])) {

    $ahora = time();
    $registros = [];
    
/////// Cargamos configuración para ver la IP de confianza /////////////////
// 1. DECLARACIÓN FALTANTE: Obtener y hashear la IP actual del visitante
    $mi_ip_actual = $miip;
    $mi_ip_actual_hash = hash('sha256', $mi_ip_actual . $pepper); // Usamos el Pepper para coincidir con 'ihash'

    $configData = cfg_load($configFile);
    $ip_confianza = isset($configData['ihash']) ? $configData['ihash'] : '';

   // ¿Es el dueño en su IP de siempre?
   // $es_owner_reconocido = ($mi_ip_actual_hash === $ip_confianza);
      $es_owner_reconocido = hash_equals($ip_confianza, $mi_ip_actual_hash);


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
        // file4.php?unlockmode
        if ($_POST['unlock'] === $master_key) {


       // SOLUCIÓN CAMBIO GET A POST: Guardamos el bypass en la sesión para que dure en la siguiente recarga
            $_SESSION['bypass_active'] = true;
            $_SESSION['bypass_time'] = time();
            $acceso_emergencia = true; 
        }
        // Si no es correcto, $acceso_emergencia se queda en false (valor por defecto)
        
    } else {
        // Bloqueo total si excedió los 10 registros en el log
        echo "$seguridadcabeza <div class='mensajex' style='background:white;'>
            <h2>🚫 Límite de Emergencia Agotado</h2>
            <p>Se han detectado <b>$conteo_intentos intentos</b> de acceso en las últimas 24 horas.</p>
            <p>Por seguridad, esta función ha sido inhabilitada temporalmente.</p>
            </div> ";
      exit;
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







// 4. MOSTRAR FORMULARIO DE DESBLOQUEO UNLOCK o BYPASS
if (isset($_GET['bypass'])) {
   // echo "$newseguridadcabeza";
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
    exit;
}





//////// VERIFICAR SEGURIDAD (FLUJO UNIFICADO Y GLOBAL) /////////////////////////


if (file_exists($configFile)) {
    $configData = cfg_load($configFile);
    $seguridadcabeza = "$stylealert <header> <h1> 🌀 File Manager </h1></header> <br>";
    $newseguridadcabeza = "$newstylealert ";

    // --- VARIABLES MAESTRAS ---
    $master = $configData['fuser']; 
    $mastermail = $configData['fmail']; 
    $tokenhash_db = $configData['fpass'];
    $tokenhash_valid = hash('sha256', "$tokenplus$tokenhost$tokenhash_db");

    // 1. AUTO-LOGIN (Sincronizar Cookie con Sesión)
    if (!isset($_SESSION['user_auth']) || $_SESSION['user_auth'] !== true) {


//------Debug zone-----// 
// include "debug.php";




        if (isset($_COOKIE['Hash']) && hash_equals($tokenhash_valid, $_COOKIE['Hash']) && hash_equals($configData['fhash'], $haship)) {
            session_regenerate_id(true);
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



            echo " $newseguridadcabeza 

    <div class='auth-card'>
        <div class='auth-header'>🌀 File4 Manager</div>
        <div class='auth-body'>
        <label><b>⏳ Acceso Controlado</b></label>

                <p>Demasiados fallos detectados (Intento #$intentos).</p>
                <p>Por seguridad, espere: <b style='color:red; font-size:1.5em;'>" . gmdate("H:i:s", $restante) . "</b></p>
                <br> 
               
             
            <div class='auth-footer'>
                <small>Seguridad File4 - V$fversion</small>
            </div>
        </div>
    </div>

 
            ";
            //echo "intentadas fallidas area<br>";
            exit;
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

            cfg_save($configData, $configFile);
            
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

$loginzone = <<<EOD

    <div class="auth-card">
        <div class="auth-header">🌀 File4 Manager</div>
        <div class="auth-body">
            <form action="" method="post">
                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" name="fuser" class="form-input" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="fpass" class="form-input" required placeholder="Ingrese su contraseña" autocomplete="current-password">
                </div>
                <input type="submit" value="Acceso">
                <div style="display:none;"><input type="text" name="fhemail" value=""></div>
            </form>
            <div class="auth-footer">
                <small>Seguridad File4 - V4.4.7.4</small>
            </div>
        </div>
    </div>

EOD;
echo "$newseguridadcabeza";
echo "$loginzone";

        exit;
    }

}



//////// VERIFICAR SEGURIDAD FIN /////////////////////////

















if (isset($_GET['test'])) {
echo "prueba master es $master";
exit;
}




//Zona Download file/////////////////////////////////////// 
if (isset($_GET['dfile'])) {
    // 1. PROTECCIÓN DE SESIÓN: Si no está autenticado, el script muere aquí.
    if (!isset($is_authenticated) || $is_authenticated !== true) {
        header('HTTP/1.1 403 Forbidden');
        exit("Error: Acceso no autorizado.");
    }
$archivoSolicitado = $_GET['dfile'] ?? null;

if (!$archivoSolicitado) {
    die("❌ Error: No se especificó ningún archivo.");
}
// rutas de las carpetas donde estaran los archivos
$baseOrigen = __DIR__ . "/uploads";
//$rutaOrigen = $baseOrigen . basename($archivoSolicitado); // basename() limpia rutas como ../../
$rutaOrigen = $baseOrigen . $archivoSolicitado; 
// Definimos la carpeta temporal y la ruta completa del archivo
$carpetaTemporal = __DIR__ . "/temp/";
$rutaTemporal = $carpetaTemporal . basename($archivoSolicitado);

// 🔹 Lógica de creación automática de carpeta
if (!file_exists($carpetaTemporal)) {
    // Creamos la carpeta con permisos 0755 (lectura/escritura para el servidor)
    // El parámetro 'true' permite creación recursiva si fuera necesario
    mkdir($carpetaTemporal, 0755, true);
    
    // Opcional: Crear un archivo .htaccess para proteger la carpeta temp
    file_put_contents($carpetaTemporal . ".htaccess", "Deny from all");
}

// 3. Validación de Seguridad: ¿El archivo existe y está en la ruta permitida?
if (file_exists($rutaOrigen) && is_file($rutaOrigen)) {
    
    // 4. Copiar temporalmente al directorio actual
    if (copy($rutaOrigen, $rutaTemporal)) {
        
        // 5. Headers para forzar descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($rutaTemporal) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($rutaTemporal));
        
        // Limpiar buffer
        if (ob_get_level()) ob_end_clean();
        
        // 6. Enviar archivo y borrar
        readfile($rutaTemporal);
        
        // Eliminamos la copia temporal
        unlink($rutaTemporal);
        exit;
        
    } else {
        echo "❌ Error: Permisos insuficientes para copiar el archivo en el servidor.";
    }
} else {
    echo "❌ Error: El archivo no existe o el acceso está denegado.";
}
//echo "prueba descargador : probando $master : $rutaOrigen [temp]$rutaTemporal";
exit;
}
////////////////////////////////////////////////////////////




/////
//buscando la ruta real de cada carpeta
$ruta = $_GET['c'] ?? "";
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
     
    // PROTECCIÓN EXTRA: Si por alguna razón llegó aquí sin sesión, matamos el proceso.
    if (!$is_authenticated) { 
        header('HTTP/1.1 403 Forbidden');
        exit("Acceso denegado."); 
    }
  
  
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    #$texto = filter_var($_POST['texto'], FILTER_SANITIZE_STRING); // Sanitizar el texto
    $texto = $_POST['texto'];
    // Sanitizar el nombre del archivo (mantiene el texto limpio de etiquetas HTML/Scripts)
    $filename = htmlspecialchars($_POST['miArchivo'] ?? '', ENT_QUOTES, 'UTF-8');

    // Sanitizar la carpeta (mantiene el texto limpio de etiquetas HTML/Scripts)
    $carpeta = htmlspecialchars($_POST['miCarpeta'] ?? '', ENT_QUOTES, 'UTF-8');

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

$cokiruta=$_GET['c'] ?? '';
$cokifile=$_GET['editFile'] ?? '';

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


header("Location: $scriptfile.php?editFile=$cokifile&c=$cokiruta/");
exit;
}

///////EDITOR PLUS COOKIEr////////////////////////
if (isset($_GET['offeditor'])) {

   setcookie('editor', '', $options_editor);

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
                <a href='?c=$carpetazSafe/' class='verde'>CANCELAR</a>
            </form>
          </div>";
    exit;
}








/////// Guardar Configuración /////////////////////////////////
// Detectamos el parámetro GET que envía tu formulario
if (isset($_GET["fconfiguracion"])) {

if (!file_exists('.htaccess')) {
    $htaccess = <<<EOT
<Files "*.json">
    Require all denied
</Files>
<Files "*.lock">
    Require all denied
</Files>
<Files "*.log">
    Require all denied
</Files>
EOT;
    @file_put_contents('.htaccess', $htaccess);
}

//echo "VERIFICAR GUARDANDO DATOS";

    // 1. Verificamos que se haya enviado por POST para procesar datos
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Cargamos la configuración actual para no perder lo que no se cambie
     // $configActual = json_decode(file_get_contents($configFile), true);
        $configActual = cfg_load($configFile);

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
$themex = $_POST['fskin'] ?? '';

// Si no está vacío, procedemos a validar su formato
if (!empty($themex)) {
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $themex)) {
        die("Nombre de theme inválido");
    }
}

$theme_options = [
    'expires' => time() + (6 * 30 * 24 * 60 * 60), //6 meses
    'path' => '/',
    'secure' => $isSecure,  // ✅ Usar tu variable existente
    'httponly' => false,     // Puede ser false porque no es sensible
    'samesite' => 'Lax'
];

//echo "GUARDANDO THEME.... $hash_mini_id - $url_limpia";
//setcookie('fm_theme', $themex, $theme_options);
setcookie('fm_theme_'.$hash_mini_id.'', $themex, $theme_options);
 


        // 6. Guardado Atómico con Bloqueo
        if (cfg_save($config, $configFile)) {
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
    <title> File Manager V4 </title>
    <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAaCAYAAACgoey0AAAACXBIWXMAAC4jAAAuIwF4pT92AAAC7ElEQVRIS8VWO28TQRCe3b2H3yaRg8Vbsng0EJDgD+QnpEMUkIKKipKONiWioCYCodRI1GlwBQgKkEAoRQxVEAGDKPy4W77Zuz07xvbZsi1OGu+db2a+bx47e0T/6RJXb927BuyHkDMDHML4WcfrAdYHb7Y2H8+DqwMnlyGrkEyKwyreb4Bo/dvOs8+jdBuNxkS8JLQ8iJpAWO8K5ObK2g22m+liB5xKm840ZyUorEPW0hTT3nOq+RJpin16F3D/CFHvDbHTK/8Gwb6/Qp6gRDu2FBZ4QlyjxjbnIGenMPoF3T2QrTe2NtvWyRT2iSpHMWmWLM6hIMdGHE7leyx/7qEOeZ3AajHwUOZzBI2wNGXUb3/p4vX77fXaB2mBE3ALKEJBQmOGjEuoxKbIL5Oo1OjUyRPQxa4MuwCBnUaQvEaoBamDuyrobmitg/1u5dPQVIsONrbk7AB13EYTLim/SDJXJtfLQp1Z+sNyzi+gQFnT4k39/HCq3SypbImEmyPJjMN4i/O9IW6fEUmAyByPRH6JRAKa3qcdmDadY0+TiLVUJEtHyamep9NV7EbDfv5XO2qv3d7oc3zSmTIdXy6SlosBZcS2ySIdJMACaZNImVIKTTX/SK3HVkCNl3dWuxGwkILrpBC1EDPP/5Gsu1Hb1Fkhai7paO3mSaBDWyEixp8Mz2U2IyrO/KwFiOv7zgDDqQilQqYRraPoRzvKswU0JPDDEt2DZ/yckLMEY8N+gv33rcD4fm2ANXYsuRkKPZx4qHMA90mNh9ZaRzMlBrMkzTr430CmGLfsifcGOBRSSpXB1sUgkO5EJ7Phw4NpsJrpTfnzxe1L+2wmtXIc8gpc55FNMccXpr4GWPi5nCwcQVfjk2tBQ6OPeA9YyYzvOh7yjTQv/nqVRIzhvosTA18IdiAvFP2t9e5Ix9sO/nz/EoTtGursY4dzi5idgtT3vjS05mece7YeOtKJ+js5xmSx0hDZUjMGsPZ2/WiB/wJcVLk32a477wAAAABJRU5ErkJggg==">

<?php
// Definimos la ruta del archivo de estilo externo
//$themeActivo = $_COOKIE['fm_theme'] ?? ''; //
$themeActivo = $_COOKIE['fm_theme_'.$hash_mini_id.''] ?? ''; //mod para usar el mismo script en diferentes carpetas con theme propio cada uno
// Esto elimina puntos (.), barras (/) y caracteres especiales
$themeActivo = preg_replace('/[^a-zA-Z0-9_-]/', '', $themeActivo); //security
$externalStyle = 'fmstyle_'.$themeActivo.'.css';

if (file_exists($externalStyle)) {
    // 1. Si el archivo existe, cargamos el link externo (Ignora el estilo interno)
    echo '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($externalStyle, ENT_QUOTES, 'UTF-8') . '?v=' . filemtime($externalStyle) . '">';
} else {

    // 2. Si NO existe, cargamos tu style predeterminado (File4 Style)
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
} 
// Cierre del else style external
?>


</head>
<body>




<?php
// Verificar session de usuario
if (empty($master)) {
?>
<table style="width: 100%; background-color: red;">
    <tr>
        <td style="text-align: left; padding: 10px; color: white;">
            <b> ⚠️ MODO INSEGURO </b>: Por favor crea una Contraseña en: <b> ⚙️ <a href="?mod=config" class='snaranja' role='button'>Configurar</a></b>
        </td>
    </tr>
</table>

<?php
} 
//EOF - Verificar session de usuario

// ── AVISO DE NUEVA VERSIÓN DISPONIBLE ──
if ($is_authenticated && hay_conexion_internet()) {
    $version_remota = obtener_version_remota($furlVersionCheck, $version_cache_file, $version_cache_segundos);
    if ($version_remota !== null && version_compare($version_remota, $fversion, '>')) {
        echo "<table style='width:100%;background-color:#04ab8a;'>
                <tr><td style='text-align:left;padding:10px;color:white;'>
                    <b>🚀 Versión nueva $version_remota disponible</b> (tienes $fversion instalada) —
                    <a href='?mod=update' style='color:#fff;text-decoration:underline;'>Actualizar ahora</a>
                </td></tr>
              </table>";
    } 
   
}
?>




<?php
$uploadDir = 'uploads/';
$activeDir = 'uploads';


// para la lista de carpetas con links

$getruta = isset($_GET['c']) ? $_GET['c'] : '/';
$rutax = "/$getruta";
$partes = explode('/', trim($rutax, '/'));
$acumulado = "/";


if (isset($getruta)) {

    $carpetax = $getruta;
    $carpetap = $getruta;
    $carpetapSafe = htmlspecialchars($carpetap, ENT_QUOTES, 'UTF-8');
    $carpetaz = $getruta;
    $carpetaz = rtrim($carpetaz, '/');

$carpetax = htmlspecialchars($carpetax, ENT_QUOTES, 'UTF-8');
$carpetazSafe = htmlspecialchars($carpetaz, ENT_QUOTES, 'UTF-8');


 $dcarpetaz = $carpetaz;


// 1. Normalización de seguridad: Eliminamos posibles dobles barras
// Esto evita errores como "uploads//carpeta"
$dcarpetaz = str_replace('//', '/', $dcarpetaz);

// 2. Lógica de Directorio Raíz:
// Si después de limpiar, la ruta está vacía, aseguramos que sea "/"
if (trim($dcarpetaz) == "") {
    $dcarpetaz = "/";
}

// 3. Formato de Directorio:
// Nos aseguramos de que siempre termine en "/" para que los enlaces concatenen bien
if (substr($dcarpetaz, -1) !== '/') {
    $dcarpetaz .= '/';
}
 $dcarpetazSafe = htmlspecialchars($dcarpetaz, ENT_QUOTES, 'UTF-8');





    // Crear la ruta completa a la carpeta
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

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755);
}

/////////// Subir archivo basico ////////////////////////////////////////
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    $targetFile = $uploadDir . basename($_FILES['fileToUpload']['name']);

    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
        echo " $alertaini ⚠️ El archivo  <span style='color:yellow;'>". htmlspecialchars(basename($_FILES['fileToUpload']['name'])). " </span> ha sido subido exitosamente. $alertafin ";
    } else {
        echo " $alertaini⚠️Error al subir el archivo. $alertafin";
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
                    <a href='?c=$carpetazSafe/' class='naranja'>CANCELAR</a>
                </form>
              </div>";
        exit;
    }

    // 2. Validación de Identidad
    $peppered_confirm = hash_hmac("sha512", $_POST['confirm_update_pass'], $pepper);
    if (!password_verify($peppered_confirm, $configData['fpass'])) {
        die("$seguridadcabeza <div class='mensajex' style='background:red;'><h2>❌ Contraseña incorrecta. Operación cancelada por seguridad.</h2></div>");
    }

  //publicKeyBase64 ya esta definido al inicio del script


    // 3. Proceso de Descarga
    $furl = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/file4.php';
    $furlSig = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/file4.php.sig';

    // A. Verificar si la extensión necesaria está instalada
    if (!extension_loaded('sodium')) {
        die("$seguridadcabeza <div class='mensajex'>
            <h2>⚠️ Falta instalar la extensión 'sodium'</h2>
            <p>Para realizar actualizaciones seguras, este servidor requiere la extensión <b>PHP Sodium</b>.</p>
            <p>Por favor, actívala desde el panel de control de tu hosting (sección de Extensiones PHP) o contacta a tu soporte técnico.</p>
            <a href='?c=$carpetazSafe/' class='naranja'>VOLVER</a>
            </div>");
    }

    // A. Descargar contenido y firma
    $fcontenido = @file_get_contents($furl);
    $firmaBinaria = @file_get_contents($furlSig);




// B. Verificación Ed25519
// 1. Decodificar la clave Base64 a binario (esto la convierte a los 32 bytes requeridos)
$publicKeyBinaria = base64_decode($publicKeyBase64);

// 2. Decodificar la firma descargada de Base64 a binario
$firmaBinariaDecodificada = base64_decode($firmaBinaria);

// Validación de seguridad para la firma
if (strlen($firmaBinariaDecodificada) !== 64) {
     die(" $alertaini 🛑 Error: La firma (.sig) debe ser de 64 bytes. Longitud recibida: " . strlen($firmaBinariaDecodificada) . " $alertafin ");
}

// 3. Verificación
try {
    // IMPORTANTE: Se usa $publicKeyBinaria (binario), no $publicKeyBase64 (texto)
    $verificado = sodium_crypto_sign_verify_detached($firmaBinariaDecodificada, $fcontenido, $publicKeyBinaria);
} catch (Exception $e) {
    die(" $alertaini 🛑 Error en la verificación: " . $e->getMessage() . " $alertafin ");
}

if (!$verificado) {
    die(" $alertaini 🛑 ERROR: Firma inválida. El archivo fue modificado o la firma no corresponde. $alertafin ");
}





    $furlicon = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/favicon.ico';
    $furlidioma = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/en.json';
    $furlidioma2 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/de.json';

    $furlskin1 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_dark-zidrave.css';
    $furlskin2 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_original.css';
    $furlskin3 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_taringa.css';
    $furlskin4 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_dark-red.css';
    $furlskin5 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_dark-leonardo.css';
    $furlskin6 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_blue.css';
    $furlskin7 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_identi.css';
    $furlskin8 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_invision.css';
    $furlskin9 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_phpbb.css';
    $furlskin10 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_phpnuke.css';
    $furlskin11 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_steam.css';
    $furlskin12 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_vbulletin.css';
    $furlskin13 = 'https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/fmstyle_whatsapp.css';

    $rutaArchivoLocal = isset($_GET['updatefile']) ? $_GET['updatefile'] . ".php" : "$scriptfile.php";

    //$fcontenido = @file_get_contents($furl); //aun no escribire el script para otras verificaciones
    $fcontenidoicon = @file_get_contents($furlicon);
    $fcontenidolang = @file_get_contents($furlidioma);
    $fcontenidolang2 = @file_get_contents($furlidioma2);

    $fcontenidoskin1 = @file_get_contents($furlskin1);
    $fcontenidoskin2 = @file_get_contents($furlskin2);
    $fcontenidoskin3 = @file_get_contents($furlskin3);
    $fcontenidoskin4 = @file_get_contents($furlskin4);
    $fcontenidoskin5 = @file_get_contents($furlskin5);
    $fcontenidoskin6 = @file_get_contents($furlskin6);
    $fcontenidoskin7 = @file_get_contents($furlskin7);
    $fcontenidoskin8 = @file_get_contents($furlskin8);
    $fcontenidoskin9 = @file_get_contents($furlskin9);
    $fcontenidoskin10 = @file_get_contents($furlskin10);
    $fcontenidoskin11 = @file_get_contents($furlskin11);
    $fcontenidoskin12 = @file_get_contents($furlskin12);
    $fcontenidoskin13 = @file_get_contents($furlskin13);

    if ($fcontenido === FALSE) {
        die(" $alertaini ⚠️ No se pudo descargar el archivo desde GitHub. Revisa la conexión del servidor. $alertafin ");
    }

     // --- 4. PARCHEO DINÁMICO: conserva TUS valores actuales, no el placeholder del repo ---
    // Escapa un valor para insertarlo de forma segura como literal PHP,
    // y además escapa $ y \ para que preg_replace no los interprete como backreferences.
    function construir_reemplazo_php(string $nombreVar, string $valorActual): string {
        $valorEscapadoPhp = addcslashes($valorActual, "\\'");
        $lineaPhp = '$' . $nombreVar . " = '" . $valorEscapadoPhp . "';";
        return str_replace(['\\', '$'], ['\\\\', '\$'], $lineaPhp);
    }

    $patrones = [
        '/\$tokenplus\s*=\s*(["\']).*?\1;/'  => construir_reemplazo_php('tokenplus', $tokenplus),
        '/\$pepper\s*=\s*(["\']).*?\1;/'     => construir_reemplazo_php('pepper', $pepper),
        '/\$configFile\s*=\s*(["\']).*?\1;/' => construir_reemplazo_php('configFile', $configFile),
    ];

   $fcontenido = preg_replace(array_keys($patrones), array_values($patrones), $fcontenido);

    // Verificación: confirma que el parcheo realmente aplicó tus valores actuales
    if (strpos($fcontenido, addcslashes($tokenplus, "\\'")) === false
        || strpos($fcontenido, addcslashes($pepper, "\\'")) === false
        || strpos($fcontenido, addcslashes($configFile, "\\'")) === false) {
        die("$alertaini ⚠️ El parcheo de configuración local falló (posible cambio de formato en el script fuente). Actualización cancelada por seguridad — tus credenciales NO se sobrescribieron. $alertafin");
    }


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

    if ($fcontenidoskin6) file_put_contents("fmstyle_blue.css", $fcontenidoskin6);
    if ($fcontenidoskin7) file_put_contents("fmstyle_identi.css", $fcontenidoskin7);
    if ($fcontenidoskin8) file_put_contents("fmstyle_invision.css", $fcontenidoskin8);
    if ($fcontenidoskin9) file_put_contents("fmstyle_phpbb.css", $fcontenidoskin9);
    if ($fcontenidoskin10) file_put_contents("fmstyle_phpnuke.css", $fcontenidoskin10);
    if ($fcontenidoskin11) file_put_contents("fmstyle_steam.css", $fcontenidoskin11);
    if ($fcontenidoskin12) file_put_contents("fmstyle_vbulletin.css", $fcontenidoskin12);
    if ($fcontenidoskin13) file_put_contents("fmstyle_whatsapp.css", $fcontenidoskin13);
    

    echo " $alertaini ⚠️ " . $tl['okupdate'] . " $alertafin";
    echo "<a href='?c=$carpetazSafe/' class='naranja' role='button'> <b> " . $tl['reload'] . " </b></a>";
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
    $archivonameSafe = htmlspecialchars($archivoname, ENT_QUOTES, 'UTF-8');

    // --- PROTECCIÓN ---
    // 1. No se puede borrar el archivo definido en $configFile
    // 2. No se puede borrar el propio script ejecutable (file4.php)
    if ($archivoname === $configFile || $archivoname === "$scriptfile.php") {
        echo "$alertaini ❌ ERROR: El archivo <span style='color:yellow;'>$archivonameSafe</span> es un archivo de sistema y NO puede ser eliminado. $alertafin";
    } 
    else {
        // Proceder con el borrado si no es un archivo protegido
        if (file_exists($fileToDelete)) {
            if (unlink($fileToDelete)) {
                echo "$alertaini ⚠️ El archivo <span style='color:red;'>$archivonameSafe</span> ha sido eliminado... $alertafin";
            } else {
                echo "$alertaini ❌ Error al intentar eliminar el archivo. $alertafin";
            }
        } else {
            echo "$alertaini ⚠️ El archivo <span style='color:red;'>$archivonameSafe</span> no fue encontrado. $alertafin";
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
if (isset($_POST['deleteFolder']) || isset($_GET['deleteFolder'])) {



    $elfolder = $_POST['deleteFolder'] ?? $_GET['deleteFolder'];
    $folderToDelete = $uploadDir . $elfolder;
    
    if (is_dir($folderToDelete)) {
        rmdir($folderToDelete);
        echo "$alertaini ⚠️Carpeta eliminada solo si estaba vacia. $alertafin";
    } else {
        echo "$alertaini ⚠️ Carpeta no encontrada. $alertafin";
    }
}



// Editar o crear archivo
if (isset($_GET['editFile'])) {

    $fileToEdit = $_GET['editFile'] ?? '';
    $fileToEdit = "uploads$carpetaz/$fileToEdit";
    if (file_exists($fileToEdit)) {
        $fileContent = file_get_contents($fileToEdit);
    } else {

        // Si el archivo no existe, crearlo con contenido vacío
        file_put_contents($fileToEdit, '');
        $fileContent = '';
    }
}

// Guardar archivo editado
if (isset($_POST['saveFile'])) {

    $fileToSave = $_POST['fileName'];
    $fileToSave = "uploads$carpetaz/$fileToSave";
    $c = $_POST['c'];
    $cSafe = htmlspecialchars($c, ENT_QUOTES, 'UTF-8'); 
    $newContent = $_POST['fileContent'];
    file_put_contents($fileToSave, $newContent);
    echo "$alertaini  ⚠️ Archivo Guardado. $alertafin";

    $elarchivo = $_GET['editFile'] ?? '';
    $elarchivoSafe = htmlspecialchars($elarchivo, ENT_QUOTES, 'UTF-8');
    echo "<a href='?editFile=$elarchivoSafe&c=$cSafe/' class='naranja' role='button'> <b> RECARGAR </b></a>";
    exit;
}

// Renombrar archivo
if (isset($_POST['renameFile'])) {
    $oldName = $uploadDir . $_POST['oldName'];
    $newName = $uploadDir . $_POST['newName'];
    $oldNameBase = basename($_POST['oldName']);

    // --- PROTECCIÓN: no se puede renombrar el archivo de configuración ---
    if ($oldNameBase === $configFile) {
        echo "$alertaini ❌ No se puede tocar archivo de sistema. $alertafin";
    }
    elseif (file_exists($oldName)) {
        if (rename($oldName, $newName)) {
            echo "$alertaini ⚠️ Archivo renombrado. $alertafin ";
            echo "<a href='?c=$carpetapSafe' class='naranja' role='button'><b>RECARGAR </b></a>";
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
    echo "<a href='?c=$carpetapSafe' class='naranja' role='button'><b>RECARGAR </b></a>";
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
    $namefilec = $_POST['archivoacomprimir'] ?? '';
    $namefilecSafe = htmlspecialchars($namefilec, ENT_QUOTES, 'UTF-8'); 
    $namefilepass = $_POST['password'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

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
            echo "$alertaini ⚠️El archivo <b>$namefilecSafe.zip</b> se ha creado correctamente. $alertafin";
        } elseif (is_dir($ruta)) {
            // Añadir una carpeta completa
            comprimirCarpetaConContrasena($ruta, $nombreZip, [], $namefilepass);
            echo "$alertaini ⚠️ La carpeta <b>$namefilecSafe.zip</b> se ha creado correctamente. $alertafin";
        } else {
            $rutaSafe = htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8');
            echo " $alertaini ⚠️ La ruta especificada no es válida $rutaSafe . $alertafin ";
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

    echo "<a href='?c=$carpetapSafe' class='naranja' role='button'><b>RECARGAR </b></a>";
    exit;
}
////////////////// Comprimir archivo o carpeta 🚀 🚀🚀🚀🚀🚀🚀🚀














// Listar archivos y carpetas
$items = scandir($uploadDir);






?>





    <header>
        <h1> 🌀 File Manager   -  <?php echo "$scriptfm";?>   



 <a href='?'>🏠</a>   <a href='?c=<?php echo "$carpetazSafe";?>/../'>↩️</a>   <a href='?mod=creartexto&c=<?php echo "$carpetazSafe";?>/'>📝</a> <a href='?mod=crearcarpeta&c=<?php echo "$carpetazSafe";?>/'> 🗂️ </a>  <a href='?mod=eliminarcarpeta&c=<?php echo "$carpetazSafe";?>/'>❌</a> <a href='?mod=config&c=<?php echo "$carpetazSafe";?>/'>⚙️ </a> <a href='?mod=update&c=<?php echo "$carpetazSafe";?>/'> 🔄 </a></h1>
    </header>




<div style="width:100%; height:5px; background-color:<?php echo "$colorHex";?>;"></div>

<a href='<?php echo "$scriptfile";?>.php' class='enlacez' role='button'>
<?php echo $tl['home'];?>:  </a> /

<?php
foreach ($partes as $parte) {
    if ($parte !== "") {
        // Construir la ruta acumulativa
        $acumulado .= $parte . '/';
        //#$acumulado = rtrim($acumulado, '/');

        $parteSafe = htmlspecialchars($parte, ENT_QUOTES, 'UTF-8');
        $acumuladoSafe = htmlspecialchars($acumulado, ENT_QUOTES, 'UTF-8');
        
        // Generar el enlace
        echo " <a href='$scriptfile.php?c=" . $acumuladoSafe . "' class='enlacez' role='button'>" . $parteSafe . " </a> <b>/</b> ";

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
<center>   <a href="?c=<?php echo "$carpetazSafe";?>/" class="azulin2"> Cerrar </a>   </center>
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


        <input type="submit" value=" ⬆️ <?php echo $tl['uploadfile'];?>" name="submit" class="btn btn-primary">
      <a href="?c=<?php echo "$carpetazSafe/";?>&uploadmultiple=1" class="btn btn-warning"> <?php echo $tl['uploadmultiplefiles'];?> </a>
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
<b>  <a href="?offeditor=1&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetazSafe";?>" class="azulin2"> <?php echo $tl['desactivate'];?> Editor Plus </a> </b>


<?php

  } else {
?>

<b>  <a href="?oneditor=1&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetazSafe";?>" class="snaranja"> <?php echo $tl['activate'];?> Editor Plus </a> </b>

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
            <input id="miCarpeta"  type="hidden" name="miCarpeta" value='<?php echo "$carpetazSafe";?>' >
            <button onclick="guardarTexto()"> <?php echo $tl['savefile'];?></button> <a href="?mod=oneditor&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetazSafe";?>/" class="azulin2"> <?php echo $tl['discardchanges'];?> </a>  <a href="?c=<?php echo "$carpetazSafe";?>/" class="azulin2"> <?php echo $tl['close'];?> </a> <br>
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
            <input type="hidden" name="c" value='<?php echo "$carpetazSafe";?>' >
            <input type="submit" name="saveFile" value="<?php echo $tl['savefile'];?>"> <a href="?mod=oneditor&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetazSafe";?>/" class="azulin2"><?php echo $tl['discardchanges'];?> </a>  <a href="?c=<?php echo "$carpetazSafe";?>/" class="azulin2"> <?php echo $tl['close'];?> </a>
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
            <input type="hidden" name="c" value='<?php echo "$carpetazSafe";?>' >
            <input type="submit" name="saveFile" value="GUARDAR ARCHIVO"> <a href="?mod=oneditor&editFile=<?php echo htmlspecialchars($_GET['editFile']); ?>&c=<?php echo "$carpetazSafe";?>" class="azulin2">Descartar Cambios </a>  <a href="?c=<?php echo "$carpetazSafe";?>/" class="azulin2"> Cerrar </a>
        </form>
    <?php endif; ?>













































<?php

///////////////////////////////////////// CONFIGURAR SISTEMA /////////////////////////⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️⚙️
$mod = isset($_GET['mod']) ? $_GET['mod'] : '';
?>


<?php if ($mod == "update"): ?>

       <br>
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> 

   <h2> 🔄 <?php echo $tl['update'];?>: </h2>

 
<form action="?fupdate=ok&c=<?php echo "$carpetazSafe/";?>&updatefile=<?php echo "$scriptfile";?>" method="post">
        (Version Actual <b><?php echo $fversion;?>) </b><br>
        <?php echo $tl['msgupdate'];?>.  <br><br>
        <input type="submit" value=" <?php echo $tl['update'];?> "> 
        <a href='?c=<?php echo "$carpetazSafe";?>/' class='azulin'> <?php echo $tl['cancel'];?></a><br>


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
$themeActivo = $_COOKIE['fm_theme_'.$hash_mini_id.''] ?? '';
//$themeActivo = $_COOKIE['fm_theme'] ?? '';

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
        <a href='?fborrarconfiguracion=1&c=<?php echo "$carpetazSafe";?>/' class='azulin'> <?php echo $tl['deleteconfiguration'];?> </a><br>


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
$creartexto=$_GET['creartexto'] ?? '';
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
        <input type="hidden" name="c" value='<?php echo "$carpetazSafe";?>' >
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
        <input type="hidden" name="c" value="<?php echo "$carpetapSafe";?>" >
        <input type="submit" value="<?php echo $tl['deletefolder'];?>">
    </form>
     <br>
			</div>
		</div>
	</div> <br>


<?php endif; ?>










<?php
$comprimir=$_GET['comprimir'] ?? '';
$comprimirSafe = htmlspecialchars($comprimir, ENT_QUOTES, 'UTF-8');
?>

<?php /*if (isset($comprimir)): */?>
<?php if (!empty($comprimir)): ?>

<!--💦💦💦💦💦💦💦-->



       <br>
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> 
    <h2> 📚 <?php echo $tl['compress'];?> ZIP (<?php echo "$comprimirSafe";?>)</h2>
    <form action="" method="post">
       <?php echo $tl['msgcompress'];?>:<br>
        <input type="hidden" name="archivoacomprimir" value="<?php echo "$comprimirSafe";?>" required class="formtext" readonly>
        <?php echo $tl['password'];?>:
        <input type="text" name="password" value=""  class="formtext">
        <input type="hidden" name="c" value="<?php echo "$carpetapSafe";?>" >
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
        <input type="submit" value="<?php echo $tl['compress'];?>" name="compressFile">  <a href='?c=<?php echo "$carpetapSafe";?>' class='azulin2'> <?php echo $tl['close'];?> </a>
    </form><br>



			</div>
		</div>
	</div> <br>


<?php endif; ?>











<?php
//////////////////////////////////// cambiar nombre  ////////////
$archivoacambiarnombre=$_GET['archivoacambiarnombre'] ?? NULL;
$archivoacambiarnombreSafe = htmlspecialchars($archivoacambiarnombre ?? '', ENT_QUOTES, 'UTF-8'); 

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

<center> <h1> <?php echo " $icon $archivoacambiarnombreSafe";?> </h1> </center> 
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
$fileinfoSafe = htmlspecialchars($fileinfo, ENT_QUOTES, 'UTF-8'); 

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
        echo "<p><strong>▶️ ".$tl['fullpath'].":</strong> <br><input type='text' id='campo' name='campo' value='$fileinfoSafe' style='width: 460px;' class='formtext'><br>";
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
        
        <input type="hidden" name="oldName" value="<?php echo "$archivoacambiarnombreSafe";?>"  readonly required class="formtext">
         
        <input type="text" name="newName" value="<?php echo "$archivoacambiarnombreSafe";?>" required class="formtext" style='width: 250px;'>
        <input type="hidden" name="c" value="<?php echo "$carpetapSafe";?>" >
        <input type="submit" value="<?php echo $tl['renamefile'];?>" name="renameFile">
    </form>
<hr>
    <h3> 🖊️ <?php echo $tl['copyfile'];?>  </h3>
    <form action="" method="post">
        <input type="hidden" name="oldName" value="<?php echo "$archivoacambiarnombreSafe";?>"  readonly required class="formtext">
        
        <input type="text" name="newName" value="<?php echo "$archivoacambiarnombreSafe";?>" required class="formtext" style='width: 250px;'>
        <input type="hidden" name="c" value="<?php echo "$carpetapSafe";?>" >
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
 echo "<a href='$fileinfoSafe' target='_black69'><img src='$fileinfoSafe' height='250' ></a>";
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

 <a href="?editFile=<?php echo "$archivoacambiarnombreSafe";?>&c=<?php echo "$carpetapSafe";?>" class='naranja'>  <?php echo $tl['edit'];?> </a> 

<?php endif; ?>


<?php if (empty($comprimible) || $comprimible !== "ok"): ?>
             <a href="?comprimir=<?php echo "$archivoacambiarnombreSafe";?>&c=<?php echo "$carpetapSafe";?>" class='verde'>   <?php echo $tl['compress'];?> </a>     
<?php endif; ?>

  <a href="?c=<?php echo "$carpetapSafe";?>" class='azulin'>  <?php echo $tl['close'];?>  </a>       <a href='?deleteFile=uploads<?php echo "$carpetapSafe";?><?php echo "$archivoacambiarnombreSafe";?>&c=<?php echo "$carpetapSafe";?>' class='rojito' onclick="return confirm('¿Estás seguro de que deseas eliminar este archivo?');">  <?php echo $tl['delete'];?> </a> </center>

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
                $labelSafe = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');                
                echo "<a href='?c=" . htmlspecialchars($enlaceLimpio) . "' style='color:var(--navigation); font-weight:bold;'>📂$labelSafe</a> ";
                
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




// 1. Detectar el protocolo (http o https)
$wprotocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

// 2. Obtener el host (192.168.0.74)
$whost = $_SERVER['HTTP_HOST'];

// 3. Obtener el directorio del script actual (/subir/)
$wcurrentDir = dirname($_SERVER['SCRIPT_NAME']);

// 4. Limpiar y normalizar la ruta del directorio
// Nos aseguramos de que termine en / y no tenga barras duplicadas
$wcurrentDir = rtrim($wcurrentDir, '/\\') . '/';

// 5. Construir la URL completa
$wbaseurl = $wprotocol . $whost . $wcurrentDir;



?>

<style>
        /* --- Modal de Imagen --- */
        #image-modal {
            cursor:pointer;
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(6px);
            gap: 20px;
        }

        .copy-path-input {
            width: 90%;
            max-width: 700px;
            padding: 12px;
            border: 2px solid #444;
            background: #222;
            color: #fff;
            border-radius: 6px;
            text-align: center;
            font-family: monospace;
            cursor: pointer;
            font-size: 1rem;
        }

        #image-modal img {
            backdrop-filter: blur(6px);
            max-width: 95%;
            max-height: 80%;
            border-radius: 10px;
            box-shadow: 0 0 40px rgba(0,0,0,0.5);
            object-fit: contain;
        }
    </style> 


<?php

#///agregando iconos personalzados para el visualizador de imagenes ////
$imageExts = ["jpg", "jpeg", "png", "gif", "webp", "bmp", "svg", "jfif"];

// BUCLE DE LISTADO DE ARCHIVOS
        foreach ($items as $item) {

 $itemSafe = htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); 

            if ($item != '.' && $item != '..') {
            $uploadDir = empty($uploadDir) ? '/' : $uploadDir;  
            $filePath = $uploadDir . $item;
            $filePerms = substr(sprintf('%o', fileperms($filePath)), -4);
            $fileOwner = posix_getpwuid(fileowner($filePath))['name'];

          //$fileModTime = date("d-m-Y / H:i", filemtime($filePath)); //d-m-Y H:i:s
            $fileModTime = date("d-m-Y", filemtime($filePath)) . " / <span class='file-time'>" . date("H:i", filemtime($filePath)) . "</span>";





#///agregando iconos personalzados ////
        if (is_dir($uploadDir . $item)) {

            $fileType = 'folder';
            $icon = '📂';
$totalCarpetas++;


        } else {
$totalArchivos++;
$totalPesoCarpeta += filesize($uploadDir . '/' . $item); //solo mediremos el peso de archivos
            $fileType = 'file';
            $fileSize = filesize($filePath);
            
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




                if (is_dir($uploadDir . $item)) {

// estos son carpetas
echo " 
    <div class='fila'>
        <div class='celda'> ◽ $icon <a href='?c=$carpetazSafe/$itemSafe/'><b>$itemSafe </b>  </a> </div>
        <div class='celda'>   ".$tl['folder']."  </div>
        <div class='celda'>  $fileModTime </div>
        <div class='celda'> <div class='fileperms'> <b>$filePerms </b></div> </div> 
        <div class='celda'>  $fileOwner </div>
	<div class='celda'>  <a href='?archivoacambiarnombre=$itemSafe&c=$carpetazSafe/'>🖊️</a> <a href='?deleteFolder=$itemSafe&c=$carpetazSafe/'>❌</a> <a href='?comprimir=$itemSafe&c=$carpetazSafe/'>📚</a>
     </div>
    </div>
 ";

                } else {

$fileSize = filesize($uploadDir . $item);

//Estos son archivos
$itemr = $item;


if (strlen($itemr) > 30) {
    $itemr = substr($itemr, -30);
    $itemr = "➰".$itemr;


}
    $itemr = htmlspecialchars($itemr, ENT_QUOTES, 'UTF-8'); 

echo " 
    <div class='fila'> ";
    // ARCHIVOS aplican lógica especial para imagenes
    if (in_array($extension, $imageExts)) {
echo "  <div class='celda'> ◽ $icon <a href='#' class='file-link image-link' data-file='$uploadDir$itemSafe'> $itemr </a> ➡︎ <a href='$uploadDir$itemSafe' target='_black'>🔗</a> </div> ";
} else {
echo "  <div class='celda'> ◽ $icon <a href='$uploadDir$itemSafe' target='_black'>$itemr  </a> </div> ";
}

echo "  <div class='celda'> " . formatSize($fileSize) . " </div>
        <div class='celda'>  $fileModTime </div>
        <div class='celda'>  <div class='fileperms2'> $filePerms </div></div> 
        <div class='celda'>  $fileOwner </div>
	<div class='celda'>  <a href='?editFile=$itemSafe&c=$carpetazSafe/'>✏️</a> <a href='?archivoacambiarnombre=$itemSafe&c=$carpetazSafe/'>🖊️</a> <a href='#eliminar_$itemSafe'>❌</a> <a href='?comprimir=$itemSafe&c=$carpetazSafe/'>📚</a> <a href='?dfile=$dcarpetazSafe$itemSafe'>💾</a> </div>
    </div>
 ";

//zona de confirmaciones para eliminacion 

 echo "
        <div id='eliminar_$itemSafe' class='mensaje'>
            <center>
                <p><b>¿ ".$tl['qdelete']." ?</b></p><br>
                <p><h2>$itemSafe</h2></p><br>
                <a class='cerrar' href='?deleteFile=$uploadDir$itemSafe&c=$carpetazSafe/'>".$tl['deletenow']."</a> 
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
        

echo " 
    <div class='fila'>
        <div class='celda'> ◽  <a href='?c=$carpetazSafe/../'>📁 <b>.. </b></a> </div>
        <div class='celda'> ".$tl['folder']." </div>
        <div class='celda'>  Null </div>
        <div class='celda'>  <div class='fileperms2'> Null </div> </div>
        <div class='celda'>  ".$tl['system']." </div>
	<div class='celda'>   </div>
    </div>
   
 ";

?>
    <!-- fin del bucle -->
</div>  



<br>
	<div class="tabla">
		<div class="filasinfx">
			<div class="celda"> 
✅ <?php echo $tl['summary'];?>
     ◽ 📁 <?php echo formatSize($totalPesoCarpeta); ?> <?php echo $tl['size'];?>
     ◽ 📁 <?php echo $totalCarpetas; ?> <?php echo $tl['folder'];?>(s)
     ◽ 📄 <?php echo $totalArchivos; ?> <?php echo $tl['efile'];?>(s)


</div></div></div>
<hr>





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


// Obtener carga del procesador y calcular porcentaje estimado
 
function get_cpu_cores() {
    $cores = 1;
    if (stristr(PHP_OS, 'WIN')) {
        // Windows
        $process = popen('wmic cpu get NumberOfLogicalProcessors', 'rb');
        if (false !== $process) {
            fgets($process);
            $cores = intval(fgets($process));
            pclose($process);
        }
    } else {
        // Linux / BSD / MacOS
        if (is_readable('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $cores = count($matches[0]);
        } elseif (($process = popen('sysctl -n hw.ncpu', 'rb')) !== false) {
            // MacOS / BSD
            $cores = intval(fgets($process));
            pclose($process);
        } else {
            // Fallback para otros Linux
            $cores = intval(shell_exec('nproc')) ?: 1;
        }
    }
    return $cores > 0 ? $cores : 1;
}


// 1. Obtener carga y núcleos
$numCores = get_cpu_cores();
$loadAvg  = sys_getloadavg();
$cpuLoad  = $loadAvg[0]; // Carga del último minuto

// 2. Calcular porcentaje real basado en núcleos
// Un load de 1.0 en 4 núcleos es 25%. Un load de 4.0 en 4 núcleos es 100%.
$cpuUsagePercent = round(($cpuLoad / $numCores) * 100, 2);
$cpuUsage = $cpuUsagePercent . '%';



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
//echo " ✅ ".$tl['processorusage'].": <b> " . $cpuLoad . " (".$tl['averageload'].") - " . $cpuUsage . " </b><br>\n";
echo " ✅ " . ($tl['processorusage'] ?? 'Carga CPU') . ": <b> " . $cpuLoad . " (" . ($tl['averageload'] ?? 'Promedio') . ") - " . $cpuUsage . " (" . $numCores . " Cores)</b><br>\n";
echo " ✅ ".$tl['coretemperature'].": <b> " . $coreTemp . "  </b><br>\n";
//echo " ⏱️ ".$tl['uptime'].": <b>" . getUptime() . "</b><br>\n";
echo " ⏱️ Online: <b>" . getUptime() . "</b><br>\n";
echo " ✴️ ".$tl['operatingsystem'].": " . $os . "</li>\n";
echo " <hr>\n";

?>



			</div>
		</div>
	</div> <br>


<div class="upload-section"> 
 🗂️ FILE MANAGER | Full Version <b><?php echo "$fversion";?> </b> <?php echo $tl['createdby'];?> <a href='https://zidrave.net/' target='_black'>http://zidrave.net</a> - Email: <b>developer@zidrave.net</b><br>
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




    <div id="image-modal"></div>

<script>
    const modal = document.getElementById('image-modal');

    document.querySelectorAll('.image-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const fileUrl = link.dataset.file;
            
            modal.innerHTML = `
                <img src="${fileUrl}" alt="Vista previa">
                <input type="text" class="copy-path-input" value="<?php echo "$wbaseurl";?>${fileUrl}" readonly>
                <p style="color: #22c55e; font-weight: bold; margin:0; display:none;" id="copy-msg">¡Copiado al portapapeles!</p>

            `;
            
            modal.style.display = 'flex';

            const input = modal.querySelector('.copy-path-input');
            const msg = modal.querySelector('#copy-msg');

            input.addEventListener('click', (event) => {
                event.stopPropagation();
                
                // 1. Seleccionar el texto
                input.select();
                input.setSelectionRange(0, 99999); 

                // 2. Intentar copiar con API moderna
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(input.value).then(() => {
                        confirmarCopiado(input, msg);
                    });
                } else {
                    // 3. Fallback: Método antiguo para sitios sin HTTPS o locales
                    try {
                        document.execCommand('copy');
                        confirmarCopiado(input, msg);
                    } catch (err) {
                        alert("Error al copiar. Por favor, copia manualmente.");
                    }
                }
            });
        });
    });

    function confirmarCopiado(el, msg) {
        const originalColor = el.style.borderColor;
        el.style.borderColor = "var(--success)";
        msg.style.display = "block";
        
        setTimeout(() => {
            el.style.borderColor = originalColor;
            msg.style.display = "none";
        }, 2000);
    }

    modal.addEventListener('click', () => {
        modal.style.display = 'none';
        modal.innerHTML = ''; 
    });
</script>

</body>
</html>
