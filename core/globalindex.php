<?php
// ==============================
// CONFIGURACIÓN
// ==============================


// Session para rate limiting
// session_start();

// 1 = pedirá contraseña, 0 = acceso libre
$versinclave = 1;  
// 0 = clave simple, 1 = clave avanzada con hash (En esta opcion tienes q crear tu hash ejem: $2y$12$RcgZxApBg/cXAcpXcaZ0QuUf3hBjmcl4bZ....)
$passwordadvance = 1;  
// Modo básico (clave visible)
$password = "1111";
// Modo avanzado (clave hasheada con bcrypt)
$password_hashed = '$2y$12$YG7Be5tX8l2DPAN0TNR7fO9ZWBGWdhNR97LDsSKwOSSLlVv4L5Eoq'; 
// corresponde a la clave "*******" //ahora es secreta pero puedes crear la tuya con: 
// echo password_hash("tuclave_nueva", PASSWORD_DEFAULT); o usando la opcion /?passgen=on  de este script


$secretKeySession = 'CAMBIA_ESTO_POR_UNA_CADENA_LARGA_Y_UNICA_123452525420';



// Detectar dominio y subdominio para poner configuracion personalizada para cada subdominio o dominio
$host = $_SERVER['HTTP_HOST']; // Esto devuelve "subdominio.dominio.com" o "dominio.com"

// Verificar si $host es exactamente "files.zidrave.net"
//reglas especiales para un tipo de subdominio
if ($host === "files.zidrave.net") {
$versinclave = 0;  // 0 acceso libre sin clave o poner clave y clave personalizada para cada dominio o subdominio
$password = "1111";
$passwordadvance = 1;
$password_hashed = '$2y$12$RcgZxApBg/cXAcpXcaZ0QuUf3hBjmcl4bZbonIQvWLyK4.0E0hjrO'; //otro password para este subdominio o dominio

}


////buscar PLugin ( GI-SECURITY.PHP ) para personalizar configuracion en carpetas diferentes
//$gisFile = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $_SERVER['REQUEST_URI'] . 'gi-security.php';
//$gisFile = str_replace("%20"," ",$gisFile); //falta mejorar pero este detalle daba problemas con carpetas con espacios
$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // 🔥 Solo la ruta, sin ? ni parámetros
$gisFile = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $uriPath . '/gi-security.php';
$gisFile = str_replace("%20", " ", $gisFile); // Decodificar espacios

if (!file_exists($gisFile)) {
//echo "no existe aun -  $gisFile<br>";
} else {
//echo "archivo de seguridad encontrado! $gisFile<br>";
include("$gisFile");

}





// Configuración de seguridad
$max_attempts = 5;
$lockout_time = 900; // 15 minutos
// Cookie
$cookie_name = "file_manager_auth";
$cookie_duration = 604800; // 7 días en segundos

// ==============================

// FORZAR configuración de sesión ANTES de session_start()
ini_set('session.gc_maxlifetime', $cookie_duration);
ini_set('session.cookie_lifetime', $cookie_duration);

// Iniciar sesión con configuración extendida
session_start([
    'cookie_lifetime' => $cookie_duration,
    'gc_maxlifetime' => $cookie_duration,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);



// ==============================
// GESTIÓN DE TEMAS
// ==============================
//$available_themes = ['taringa', 'joomla'];
$available_themes = ['taringa', 'joomla', 'github', 'leonardo', 'zidrave-skin'];
$default_theme = 'zidrave-skin';

// Cambiar tema
if (isset($_GET['change_theme']) && in_array($_GET['change_theme'], $available_themes)) {
    setcookie('selected_theme', $_GET['change_theme'], time() + (86400 * 180), '/'); // para 6 meses
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Obtener tema actual
$current_theme = isset($_COOKIE['selected_theme']) ? $_COOKIE['selected_theme'] : $default_theme;
if (!in_array($current_theme, $available_themes)) $current_theme = $default_theme;








// ==============================
// FUNCIONES DE SEGURIDAD
// ==============================

function getRateLimitKey() {
    return 'login_attempts_' . $_SERVER['REMOTE_ADDR'];
}

function checkRateLimit() {
    global $max_attempts, $lockout_time;
    $key = getRateLimitKey();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['attempts' => 0, 'lockout_until' => 0];
    }
    
    $data = $_SESSION[$key];
    
    // Si está bloqueado
    if ($data['lockout_until'] > time()) {
        $remaining = ceil(($data['lockout_until'] - time()) / 60);
        return ['blocked' => true, 'minutes' => $remaining];
    }
    
    // Resetear si pasó el tiempo
    if ($data['lockout_until'] > 0 && $data['lockout_until'] <= time()) {
        $_SESSION[$key] = ['attempts' => 0, 'lockout_until' => 0];
    }
    
    return ['blocked' => false, 'attempts' => $data['attempts']];
}

function recordFailedAttempt() {
    global $max_attempts, $lockout_time;
    $key = getRateLimitKey();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['attempts' => 0, 'lockout_until' => 0];
    }
    
    $_SESSION[$key]['attempts']++;
    
    // Bloquear si excede intentos
    if ($_SESSION[$key]['attempts'] >= $max_attempts) {
        $_SESSION[$key]['lockout_until'] = time() + $lockout_time;
    }
}

function resetAttempts() {
    $key = getRateLimitKey();
    $_SESSION[$key] = ['attempts' => 0, 'lockout_until' => 0];
}

function generateSecureToken() {
    return bin2hex(random_bytes(32));
}

function getUserFingerprint() {
    $theip = $_SERVER['HTTP_CF_CONNECTING_IP'] 
          ?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
          ?? $_SERVER['REMOTE_ADDR'];
    $realIp = explode(',', $theip)[0]; // por si X-Forwarded-For trae varias IPs encadenadas
    
    return hash('sha256', 
        $_SERVER['HTTP_USER_AGENT'] . 
        trim($realIp) .
        'salt_secreto_unico'
    );
}

 



// ==============================
// LOGOUT MEJORADO
// ==============================
if (isset($_GET['logout'])) {
    // Eliminar cookie
    setcookie($cookie_name, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => false,  // Ajusta según tu servidor
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    // Destruir sesión completamente
    unset($_SESSION['auth_token']);
    unset($_SESSION['auth_fingerprint']);
    unset($_SESSION['auth_time']);
    unset($_SESSION['csrf_token']);
    
    // Opcional: destruir toda la sesión
    // session_destroy();
    
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host   = $_SERVER['HTTP_HOST'];
    $uri    = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: $scheme://$host$uri");
    exit;
}








// ==============================
// LOGIN MEJORADO
// ==============================

// PASO 1: Siempre verificar si hay sesión activa válida (independiente de $versinclave)
$is_authenticated = false;

if (isset($_COOKIE[$cookie_name])) {
    $parts = explode('|', $_COOKIE[$cookie_name]);

    if (count($parts) === 3) {
        [$token, $authTime, $signature] = $parts;
        $current_fingerprint = getUserFingerprint();
        $expected_signature = hash_hmac('sha256', $token . '|' . $authTime . '|' . $current_fingerprint, $secretKeySession);

        if (hash_equals($expected_signature, $signature) && (time() - (int)$authTime) < $cookie_duration) {
            $is_authenticated = true;
            // Rehidratamos la sesión para que el resto del script siga funcionando igual
            $_SESSION['auth_token'] = $token;
            $_SESSION['auth_fingerprint'] = $current_fingerprint;
            $_SESSION['auth_time'] = (int)$authTime;
        } else {
            setcookie($cookie_name, '', time() - 3600, '/');
        }
    } else {
        setcookie($cookie_name, '', time() - 3600, '/');
    }
}

// PASO 2: Solo exigir login si $versinclave = 1 Y no está autenticado
if ($versinclave == 1 && !$is_authenticated) {
    
    // Verificar rate limit
    $rateCheck = checkRateLimit();
    
    if ($rateCheck['blocked']) {
        $login_error = "Demasiados intentos fallidos. Espera {$rateCheck['minutes']} minutos.";
        goto show_login_form;
    }
    
    // Procesar login
    if (isset($_POST['password']) && isset($_POST['csrf_token'])) {
        
        // Verificar token CSRF
        if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $login_error = "Token de seguridad inválido";
            recordFailedAttempt();
            goto show_login_form;
        }
        
        $input_pass = $_POST['password'];
        $auth_ok = false;
        
        if ($passwordadvance == 0) {
            if ($input_pass === $password) $auth_ok = true;
        } else {
            if (password_verify($input_pass, $password_hashed)) $auth_ok = true;
        }
        
if ($auth_ok) {
    session_regenerate_id(true);
    $session_token = generateSecureToken();
    $fingerprint = getUserFingerprint();
    $authTime = time();

    $_SESSION['auth_token'] = $session_token;
    $_SESSION['auth_fingerprint'] = $fingerprint;
    $_SESSION['auth_time'] = $authTime;

    // Cookie autosuficiente: firma HMAC que no depende de que el archivo de sesión sobreviva
    $payload = $session_token . '|' . $authTime;
    $signature = hash_hmac('sha256', $payload . '|' . $fingerprint, $secretKeySession);
    $cookie_val = $payload . '|' . $signature;

    setcookie($cookie_name, $cookie_val, [
        'expires' => time() + $cookie_duration,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    resetAttempts();
    $is_authenticated = true;
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
} else {
            $login_error = "Contraseña incorrecta";
            recordFailedAttempt();
            error_log("Login fallido desde: " . $_SERVER['REMOTE_ADDR']);
        }
    }
}

show_login_form:

// PASO 3: Mostrar formulario solo si $versinclave = 1 Y no está autenticado
if ($versinclave == 1 && !$is_authenticated):
    $_SESSION['csrf_token'] = generateSecureToken();
    // ... tu formulario de login ...
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso - Gestor</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
  height:100vh; display:flex; justify-content:center; align-items:center;
  font-family:Arial, sans-serif;
  background:linear-gradient(180deg,#0b1220,#001a33);
  padding:15px;
}
.login{
  width:100%; max-width:360px; padding:30px; text-align:center;
  background:linear-gradient(180deg,#fff,#f2f2f2);
  border-radius:14px;
  box-shadow:0 8px 20px rgba(0,0,0,0.6),0 0 30px rgba(0,85,204,0.1);
  position:relative;
}
.login::before{
  content:""; position:absolute; inset:-6px; border-radius:18px;
  background:linear-gradient(45deg,rgba(0,85,204,0.15),rgba(0,200,255,0.08));
  filter:blur(18px); z-index:-1;
}
input,button{
  width:100%; padding:12px; margin:10px 0;
  border-radius:8px; font-size:15px;
}
input{
  border:1px solid #ccc;
}
button{
  border:1px solid #003366;
  background:#003366; color:#fff; font-weight:bold; cursor:pointer;
}
button:hover{background:#0055cc}
.error{color:#c62828; margin-top:10px; font-size:14px}

/* En pantallas pequeñas el cuadro ocupa casi todo */
@media (max-width:600px){
  .login{
    max-width:100%;
    border-radius:0;
    height:auto;
    padding:20px;
  }
}
</style>
</head>
<body>
<div class="login">
  <h2>🔒 Login</h2>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="password" name="password" placeholder="Contraseña" required autofocus>
    <button type="submit">Entrar</button>
  </form>
  <?php if (isset($login_error)) echo "<div class='error'>$login_error</div>"; ?>
  <?php 
  $rateCheck = checkRateLimit();
  if ($rateCheck['attempts'] > 0 && !$rateCheck['blocked']) {
      echo "<div style='color:#666; font-size:12px; margin-top:10px;'>Intentos: {$rateCheck['attempts']}/{$max_attempts}</div>";
  }
  ?>
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
// FUNCIONES DE TEMAS
// ==============================
function getThemeStyles($theme) {
    $styles = [
'zidrave-skin' => "
* {margin:0;padding:0;box-sizing:border-box;}
body {font-family: Arial, sans-serif; background:#080c11; color:#e0e1dd; min-height:100vh;}
header {background:linear-gradient(180deg,#263c5e 0%, #071f31 100%); color:#fff; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #415a77;}
.title {font-size:18px; font-weight:bold;}
.logout-btn {font-size: 14px;font-family: inherit; background:#3e6fa8; color:#fff; border:none; padding:8px 15px; cursor:pointer; border-radius:5px; text-decoration:none;}
.logout-btn:hover {background:#778da9;}

.breadcrumb {background:#045476; padding:10px 20px; font-size:13px; color:#ffffff;}
.breadcrumb a {color:#edf6f9; text-decoration:none;}
.breadcrumb a:hover {text-decoration:underline;}
.main-content {padding:20px; max-width:1200px; margin:auto;}
.content-box {background:#01050e; border:1px solid #1e3a5f; border-radius:8px; margin-bottom:20px; overflow:hidden;}
.box-header {background:#1e3a5f; padding:13px 18px; font-weight:bold; color:#edf6f9; text-transform:uppercase;}
table {width:100%; border-collapse:collapse;}
th, td {padding:12px 15px; border-bottom:1px solid #1e3a5f; font-size:15px;}
th {color:#edf6f9; text-align:left; background:#010813;}
tbody tr {background:#0f1e2e;}
tbody tr:nth-child(even) {background:#16283c;}
tbody tr:hover {background:#020d17;}
.logout-btnred {font-size: 14px;font-family: inherit; background:#9e1010; color:#fff; border:none; padding:8px 15px; cursor:pointer; border-radius:5px; text-decoration:none;}
.logout-btnred:hover {background:#d10000;}
.eform {
                  font-family: monospace;
                  font-size:16px;
                  background: linear-gradient(to bottom, #2a3f50, #243442);
                  color:#b9cacb;
                  border:1px solid #415571;
  }
.link-link {color:#00fbff; text-decoration:none; gap:6px;}
.file-link {color:#e0e1dd; text-decoration:none; display:flex; align-items:center; gap:6px;}
.file-link:hover {color:#00fbff;}
.file-icon {font-size:14px;}
.info-box {line-height: 1.8; font-size:20px; background:#031a30; border:1px solid #415a77; padding:20px; margin-bottom:25px; border-left:4px solid #778da9; border-radius:5px;}
.info-box strong {color:#edf6f9;}
footer {background:#061c32; color:#aaa; text-align:center; padding:20px; margin-top:30px; border-top:2px solid #1e3a5f;}
footer img { width:100px; opacity:0.7; }
.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:15px;}
.stat-item {font-size:18px;background:#192733; padding:12px; border-radius:8px; color:#37bfe1;}
.stat-label {font-size:15px; color:#37bfe1; solid #1e3a5f; }
.stat-value {font-size:18px; color:#fff; font-weight:bold;}
.theme-selector {position:fixed; bottom:20px; right:20px; background:#111827; padding:12px; border-radius:6px; border:1px solid #415a77; box-shadow:0 2px 8px rgba(0,0,0,0.4);}
.theme-selector select {padding:6px 10px; background:#0d1b2a; border:1px solid #415a77; color:#edf6f9; border-radius:4px;}

/* Responsive: ocultar columna Modificado */
@media (max-width: 768px) {
  th:nth-child(4), td:nth-child(4) { display:none; }
}
",



'leonardo' => "
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: Arial, sans-serif;
    background: #0f0f1a;  /* Fondo oscuro estilo Leonardo */
    color: #e4e6eb;
    min-height: 100vh;
    font-size: 14px;
}
header {
    background: linear-gradient(90deg, #1a1a2e, #16213e); /* degrade futurista */
    color: #ffffff;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #2a2a40;
}
.title { font-size: 18px; font-weight: bold; color: #f5f5f5; }
.logout-btn {
    background: linear-gradient(45deg, #e94560, #b13552);
    color: #ffffff;
    border: none;
    padding: 6px 15px;
    cursor: pointer;
    border-radius: 4px;
    text-transform: uppercase;
    font-size: 11px;
    font-weight: bold;
    box-shadow: 0 0 8px rgba(233,69,96,0.5);
    text-decoration:none; 
}
.logout-btn:hover {
    background: linear-gradient(45deg, #ff6f91, #e94560);
}
.breadcrumb {
    background: #1a1a2e;
    padding: 10px 15px;
    color: #aaa;
    font-size: 11px;
}
.breadcrumb a {
    color: #00d9ff;
    text-decoration: none;
}
.breadcrumb a:hover {
    text-decoration: underline;
}
.main-content {
    padding: 20px;
    max-width: 1200px;
    margin: auto;
}
.content-box {
    background: #161b2e;
    border: 1px solid #2a2a40;
    border-radius: 8px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 0 12px rgba(0,0,0,0.5);
}
.box-header {
    background: #0f0f1a;
    padding: 12px 15px;
    font-weight: bold;
    color: #00d9ff;
    text-transform: uppercase;
    font-size: 11px;
    border-bottom: 1px solid #2a2a40;
    letter-spacing: 1px;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #2a2a40;
    font-size: 13px;
}
th {
    text-align: left;
    text-transform: uppercase;
    background: linear-gradient(to bottom, #ff6f91, #e94560); /* degradado rosado vertical */
    color: #fff;
    letter-spacing: 0.8px;
}
tbody tr {
    background: #1a1a2e;
    transition: background 0.2s;
}
tbody tr:nth-child(even) {
    background: #111122;
}
tbody tr:hover {
    background: #232344;
}
td { color: #e4e6eb; }
.logout-btnred {font-weight: bold;text-transform: uppercase;font-size: 11px;font-family: inherit; background:#9e1010; color:#fff; border:none; padding: 6px 15px; cursor:pointer; border-radius:4px; text-decoration:none; box-shadow: 0 0 8px rgba(233,69,96,0.5);}
.logout-btnred:hover {background:#d10000;}
.eform {
                  font-family: monospace;
                  font-size:16px;
                  background: linear-gradient(to bottom, #190111, #161221);
                  color:#b9cacb;
                  border:1px solid #1f426f;
  }
.link-link {color:#33e0ff; text-decoration:none; gap:6px;}
.file-link {
    color: #00d9ff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}
.file-link:hover {
    color: #33e0ff;
}
.file-icon { font-size: 14px; }
.info-box {
    line-height: 1.5;
    font-size:16px;
    background: #0f0f1a;
    border: 1px solid #2a2a40;
    padding: 15px;
    margin-bottom: 20px;
    border-left: 4px solid #00d9ff;
    border-radius: 4px;
}
.info-box strong {
    color: #00d9ff;
}
footer {
    background: #161b2e;
    color: #aaa;
    text-align: center;
    padding: 20px;
    margin-top: 30px;
    border-top: 1px solid #2a2a40;
}
footer img { width: 100px; opacity: 0.7; }
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
}
.stat-item {
    background: #161b2e;
    padding: 12px;
    border-radius: 4px;
    color: #e4e6eb;
    border-left: 3px solid #00d9ff;
    box-shadow: 0 0 6px rgba(0,217,255,0.3);
}
.stat-label {
    font-size: 11px;
    color: #aaa;
    text-transform: uppercase;
}
.stat-value {
    font-size: 15px;
    color: #fff;
    font-weight: bold;
}
.theme-selector {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #161b2e;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #2a2a40;
    box-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.theme-selector select {
    padding: 6px 10px;
    background: #0f0f1a;
    border: 1px solid #2a2a40;
    color: #e4e6eb;
    border-radius: 4px;
}

/* Responsive: ocultar columna Modificado */
@media (max-width: 768px) {
    th:nth-child(4), td:nth-child(4) {
        display: none;
    }
}
",





'github' => "
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: Arial, sans-serif;
    background: #0d1117;     /* fondo oscuro tipo GitHub Dark */
    color: #c9d1d9;           /* texto claro */
    min-height: 100vh;
    font-size: 14px;
}
header {
    background: #161b22;      /* un tono más claro de fondo */
    color: #c9d1d9;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #30363d;
}
.title { font-size: 18px; font-weight: bold; }
.logout-btn {
    background: #6a6d71;      /* rojo de acento tipo GitHub */
    text-decoration: none;
    color: #ffffff;
    border: none;
    padding: 6px 15px;
    cursor: pointer;
    border-radius: 4px;
    text-transform: uppercase;
    font-size: 11px;
    font-weight: bold;
}
.logout-btn:hover {
    background: #444950;
}
.breadcrumb {
    background: #161b22;
    padding: 10px 15px;
    color: #8b949e;
    font-size: 11px;
}
.breadcrumb a {
    color: #58a6ff;
    text-decoration: none;
}
.breadcrumb a:hover {
    text-decoration: underline;
}
.main-content {
    padding: 20px;
    max-width: 1200px;
    margin: auto;
}
.content-box {
    background: #161b22;
    border: 1px solid #30363d;
    border-radius: 6px;
    margin-bottom: 20px;
    overflow: hidden;
}
.box-header {
    background: #0d1117;
    padding: 12px 15px;
    font-weight: bold;
    color: #58a6ff;
    text-transform: uppercase;
    font-size: 11px;
    border-bottom: 1px solid #30363d;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #30363d;
    font-size: 13px;
}
th {
    color: #8b949e;
    text-align: left;
    text-transform: uppercase;
    background: #0d1117;
}
tbody tr {
    background: #161b22;
    transition: background 0.2s;
}
tbody tr:nth-child(even) {
    background: #0d1117;
}
tbody tr:hover {
    background: #21262d;
}
td { color: #c9d1d9; }

.logout-btnred {font-weight: bold;text-transform: uppercase;font-size: 11px;font-family: inherit; background:#9e1010; color:#fff; border:none; padding: 6px 15px; cursor:pointer; border-radius:4px; text-decoration:none; box-shadow: 0 0 8px rgba(233,69,96,0.5);}
.logout-btnred:hover {background:#d10000;}
.eform {
                  font-family: monospace;
                  font-size:16px;
                  background: linear-gradient(to bottom, #111622, #0e121b);
                  color:#b9cacb;
                  border:1px solid #6a6d71;
  }
.eform:hover {
  border: 1px solid #678c9e; /* Cambia el color del borde */
  box-shadow: 0 0 6px #00b4ff; /* Opcional: efecto de brillo */
}

.link-link {color:#79c0ff; text-decoration:none; gap:6px;}
.file-link {
    color: #58a6ff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}
.file-link:hover {
    color: #ffffff;
}
.file-icon { font-size: 14px; }
.info-box {
    line-height: 1.5;
    font-size:16px;
    background: #0d1117;
    border: 1px solid #30363d;
    padding: 15px;
    margin-bottom: 20px;
    border-left: 4px solid #58a6ff;
    border-radius: 4px;
}
.info-box strong {
    color: #58a6ff;
}
footer {
    background: #161b22;
    color: #8b949e;
    text-align: center;
    padding: 20px;
    margin-top: 30px;
    border-top: 1px solid #30363d;
}
footer img { width: 100px; opacity: 0.7; }
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
}
.stat-item {
    background: #161b22;
    padding: 12px;
    border-radius: 4px;
    color: #c9d1d9;
    border-left: 3px solid #58a6ff;
}
.stat-label {
    font-size: 11px;
    color: #8b949e;
    text-transform: uppercase;
}
.stat-value {
    font-size: 15px;
    color: #c9d1d9;
    font-weight: bold;
}
.theme-selector {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #161b22;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #30363d;
    box-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.theme-selector select {
    padding: 6px 10px;
    background: #0d1117;
    border: 1px solid #30363d;
    color: #c9d1d9;
    border-radius: 4px;
}

/* Responsive: ocultar columna Modificado */
@media (max-width: 768px) {
    th:nth-child(4), td:nth-child(4) {
        display: none;
    }
}
",



        'taringa' => "
* {margin:0;padding:0;box-sizing:border-box;}
body {font-family:Arial, Helvetica, sans-serif; background:#e8eef7; color:#333; min-height:100vh;}
header {background:linear-gradient(180deg, #3d6fa8 0%, #2d5a8f 100%); padding:15px 30px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 5px rgba(0,0,0,0.2);}
.title {display:flex; align-items:center; gap:12px; font-size:22px; font-weight:bold; color:#fff; text-shadow:1px 1px 2px rgba(0,0,0,0.3);}
.folder-icon {font-size:28px;}
.logout-btn {background:linear-gradient(180deg, #ff6b35 0%, #e55a2b 100%); color:#fff; border:1px solid #d54a1f; padding:10px 20px; font-family:Arial,sans-serif; font-weight:bold; font-size:13px; cursor:pointer; transition: all 0.2s; text-transform:uppercase; border-radius:3px; box-shadow:0 2px 4px rgba(0,0,0,0.2); text-decoration:none;}
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

.logout-btnred {background:linear-gradient(180deg, #b33000 0%, #f13f04 100%); color:#fff; border:1px solid #d54a1f; padding:10px 20px; font-family:Arial,sans-serif; font-weight:bold; font-size:13px; cursor:pointer; transition: all 0.2s; text-transform:uppercase; border-radius:3px; box-shadow:0 2px 4px rgba(0,0,0,0.2); text-decoration:none;}
.logout-btnred:hover {background:linear-gradient(180deg, #000000 0%, #992600 100%); box-shadow:0 3px 6px rgba(0,0,0,0.3); transform:translateY(-1px);}

.eform {
                  font-family: monospace;
                  font-size:13px;
                  background: linear-gradient(to bottom, #f2f2f2, #f2f4f7);
                  color:#345893;
                  border:1px solid #a6aab0;
  }

.link-link {color:#2d5a8f; text-decoration:none; gap:6px;}
.file-link {color:#3d6fa8; text-decoration:none; display:flex; align-items:center; gap:8px; font-weight:500;}
.file-link:hover {color:#2d5a8f; text-decoration:underline;}
.file-icon {font-size:18px;}
   
.info-box {line-height: 1.5; font-size:18px; background:#fffbea; border:1px solid #f5e6a8; border-left:4px solid #ff6b35; padding:15px 20px; margin-bottom:20px; border-radius:3px;}
.info-box p {line-height: 1.5; font-size:16px; margin:8px 0; color:#666; }
.info-box strong {color:#2d5a8f; font-weight:bold;}
footer {background:#2d5a8f; color:#fff; text-align:center; padding:30px; margin-top:40px; border-top:3px solid #3d6fa8;}
footer img { width:120px; margin-top:10px; opacity:0.7; }
.logo {width:100px; height:100px; margin:15px auto; opacity:0.9;}
.copyright {font-size:13px; margin-bottom:15px; opacity:0.9;}
.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px; margin:15px 0;}
.stat-item {background:#f5f7fa; padding:12px; border-radius:3px; border-left:3px solid #3d6fa8;}
.stat-label {font-size:12px; color:#666; text-transform:uppercase; margin-bottom:5px;}
.stat-value {font-size:16px; color:#2d5a8f; font-weight:bold;}
.theme-selector {position:fixed; bottom:20px; right:20px; background:#fff; padding:15px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:1000;}
.theme-selector select {padding:8px 12px; border:1px solid #d5dde5; border-radius:4px; font-size:13px; cursor:pointer;}
@media (max-width:768px){th:nth-child(4),td:nth-child(4){display:none;}}
",
        'joomla' => "
* {margin:0;padding:0;box-sizing:border-box;}
body {font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background:#f5f7fa; color:#333;}
.top-nav {background:linear-gradient(180deg, #1e3a5f 0%, #152d4a 100%); box-shadow:0 2px 8px rgba(0,0,0,0.15);}
.top-menu {display:flex; max-width:1400px; margin:0 auto;}
.top-menu-item {color:#fff; text-decoration:none; padding:15px 25px; font-size:14px; font-weight:500; border-left:1px solid rgba(255,255,255,0.1); transition:all 0.2s; display:flex; align-items:center; gap:5px;}
.top-menu-item:first-child {border-left:none;}
.top-menu-item:hover {background:rgba(255,255,255,0.1);}
header {background:linear-gradient(135deg, #1e5a8e 0%, #2970b3 100%); padding:40px 30px; color:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.header-content {max-width:1400px; margin:0 auto; display:flex; justify-content:space-between; align-items:center;}
.title {font-size:36px; font-weight:300; letter-spacing:0.5px; color:#fff;}
.logout-btn {background:#ff9800; color:#fff; padding:12px 30px; border:none; font-size:15px; font-weight:600; cursor:pointer; transition:all 0.2s; border-radius:3px; text-decoration:none; display:inline-block;}
.logout-btn:hover {background:#fb8c00; box-shadow:0 4px 12px rgba(255, 152, 0, 0.4); transform:translateY(-1px);}
.breadcrumb {background:#2970b3; padding:14px 30px; box-shadow:0 2px 5px rgba(0,0,0,0.1);}
.breadcrumb-content {max-width:1400px; margin:0 auto; color:rgba(255,255,255,0.9); font-size:14px;}
.breadcrumb a {color:rgba(255,255,255,0.9); text-decoration:none; transition:color 0.2s;}
.breadcrumb a:hover {color:#fff; text-decoration:underline;}
.main-content {max-width:1400px; margin:30px auto; padding:0 30px;}
.info-box {background:#fff; padding:25px 30px; border-radius:4px; box-shadow:0 2px 8px rgba(0,0,0,0.08); margin-bottom:30px; border-left:4px solid #2970b3;}
.info-box p {line-height: 1.5; font-size:16px; margin:8px 0; color:#666; }
.info-box strong {color:#1e3a5f;}
.content-box {width:100%; background:#fff; border-radius:4px; box-shadow:0 2px 8px rgba(0,0,0,0.08); overflow:hidden; margin-bottom:30px;}
.box-header {background:linear-gradient(180deg, #f5f7fa 0%, #e8eef7 100%); padding:12px 20px; border-bottom:1px solid #d5dde5; font-weight:bold; color:#2d5a8f; border-radius:0;}
table {width:100%; border-collapse:collapse;}
thead {background:linear-gradient(180deg, #2970b3 0%, #1e5a8e 100%);}
th {padding:16px 30px; text-align:left; font-weight:600; color:#fff; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;}
tbody tr {border-bottom:1px solid #e8eef5; transition:all 0.2s;}
tbody tr:hover {background:#f8fafc;}
tbody tr:last-child {border-bottom:none;}
td {padding:16px 30px; color:#333; font-size:14px;}

 
.logout-btnred {background:#a80000; color:#fff; padding:12px 30px; border:none; font-size:15px; font-weight:600; cursor:pointer; transition:all 0.2s; border-radius:3px; text-decoration:none; display:inline-block;}
.logout-btnred:hover {background:#d10000; box-shadow:0 4px 12px rgba(255, 152, 0, 0.4); transform:translateY(-1px);}
.eform {
                  font-family: monospace;
                  font-size:13px;
                  background: linear-gradient(to bottom, #f2f2f2, #e6ebf4);
                  color:#263e54;
                  border:1px solid #a6aab0;
  }


.link-link {color:#2d5a8f; text-decoration:none; gap:6px;}
.file-link {color:#2970b3; text-decoration:none; display:flex; align-items:center; gap:10px; font-weight:500;}
.file-link:hover {color:#30465a; text-decoration:none;}
.file-icon {font-size:20px;}
.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:30px;}
.stat-item {background:#fff; padding:20px 25px; border-radius:4px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-left:4px solid #2970b3;}
.stat-label {color:#666; font-size:12px;  letter-spacing:0.5px; margin-bottom:8px;}
.stat-value {color:#1e3a5f; font-size:20px; font-weight:600;}
footer {background:linear-gradient(180deg, #0d1f33 0%, #152d4a 100%); color:rgba(255,255,255,0.8); padding:30px; margin-top:60px; text-align:center;}
footer img {width:120px; margin-top:10px; opacity:0.7;}
.copyright {font-size:13px; color:rgba(255,255,255,0.6);}
.theme-selector {position:fixed; bottom:20px; right:20px; background:#fff; padding:15px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); z-index:1000;}
.theme-selector select {padding:8px 12px; border:1px solid #d5dde5; border-radius:4px; font-size:13px; cursor:pointer;}
@media (max-width:768px){th:nth-child(4),td:nth-child(4){display:none;}}
"
    ];
    return $styles[$theme] ?? $styles['taringa'];
}


///MENSAJE DE NO PERMITIDO ///////////////
//$alertasegura = "<center><h1>☠️ Acceso Prohibido ☠️</center></h1>";
$alertasegura = <<<HTML
<style>
body {
    background-color: #0e0e0e;
    color: #f5f5f5;
    font-family: 'Segoe UI', sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
h1 {
    color: #ff3b3b;
    text-shadow: 0 0 10px #ff3b3b;
    font-size: 2.5em;
    margin-bottom: 10px;
}
p {
    color: #ccc;
    font-size: 1.1em;
}
.alert-box {
    background: #1b1b1b;
    border: 2px solid #ff3b3b;
    border-radius: 12px;
    padding: 30px 40px;
    box-shadow: 0 0 25px rgba(255, 0, 0, 0.3);
    text-align: center;
}
</style>

<div class="alert-box">
    <h1>☠️ Acceso Prohibido ☠️</h1>
    <p>Tu intento ha sido registrado. Solo el propietario puede acceder a esta función.</p>
</div>
HTML;








/////OBTENER PACH RECURSIVO /////////////
// Obtener solo la parte del request sin los parámetros
$pathWithoutQuery = strtok($_SERVER['REQUEST_URI'], '?');
// Decodificar también el path
$pathWithoutQuery = urldecode($pathWithoutQuery);
// Quitar posible barra final (si la hay)
$pathWithoutQuery = rtrim($pathWithoutQuery, '/');
// Unir con el root del servidor
$baseDir2 = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $pathWithoutQuery;







/////// Guardar archivo editado//////////
if (isset($_POST['saveFile'])) {

// Si hay sesión activa, permitir edición global aunque la carpeta no requiera clave
    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
         // die($alertasegura); // usaremos exit; para agregar un registro de logs a futuro
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }


    $fileToSave = $_POST['fileName'];
// falta agregar seguridad
    // SANITIZAR Y VALIDAR
    $fileToSave = basename($fileToSave); // Solo nombre, sin path



    // Validar extensión permitida (opcional pero recomendado) 
    $allowedExts = ['txt', 'md', 'php', 'html', 'css', 'js', 'json', 'xml', 'htm', 'htaccess', 'htpasswd', 'dat', 'bashrc', 'info', 'ini'];
    $ext = strtolower(pathinfo($fileToSave, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        die("🚫 Extensión no permitida");
    }



    $fullPath = $baseDir2 . '/' . $fileToSave;
    $fileToSave = $baseDir2 . '/' . $fileToSave;


    // Validar tamaño
    $newContent = $_POST['fileContent'];
    $maxSizex = 50 * 1024 * 1024; // 10MB
    if (strlen($newContent) > $maxSizex) {
        die("🚫 Archivo demasiado grande (máximo 50MB)");
    }

    file_put_contents($fileToSave, $newContent);
    echo "<script>alert('Archivo Guardado.');</script>";

  // $elarchivo = $_GET['editFile'];
  //  echo "<a href='?editFile=$elarchivo&c=$c/' class='naranja' role='button'> <b> RECARGAR </b></a>";
  //  exit;
}

///////// Editar o crear archivo//////////////////
if (isset($_GET["edit"])) {

    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
         // die($alertasegura); // usaremos exit; para agregar un registro de logs a futuro
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }


    $fileToEdit = $_GET["edit"];
//obteniendo la url interna
$fileToEdit = urldecode($_GET['edit']); // decodifica %20 → espacio

// Construir la ruta final del archivo
$editarFile = $baseDir2 . '/' . $fileToEdit;
//echo "Ruta completa 2: $editarFile<br>";


    if (file_exists($editarFile)) {
        $fileContent = file_get_contents($editarFile);
    } else {
        // Si el archivo no existe, crearlo con contenido vacío
        file_put_contents($editarFile, '');
        $fileContent = '';
    }
}


/////////// SUBIR MULTI FILES //////////////////
if($_GET["new"]=="uploads"){
    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
         // die($alertasegura); // usaremos exit; para agregar un registro de logs a futuro
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }
} // fin de subir multi files










// ==============================
// LÓGICA DE DESCARGA DE ARCHIVOS
// ==============================
if (isset($_GET['download'])) {
    $fileToDownload = basename($_GET['download']);
    $filePath = $targetDir . '/' . $fileToDownload;
    
    if (file_exists($filePath) && is_file($filePath)) {
        // Headers para forzar descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileToDownload . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Limpiar buffer de salida
        ob_clean();
        flush();
        
        // Leer y enviar archivo
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        die("Archivo no encontrado");
    }
exit;
}



//////////////// Eliminar carpeta /////////////////////
if (isset($_GET['deleteFolder'])) {
    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
         // die($alertasegura); // usaremos exit; para agregar un registro de logs a futuro
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }
    $elfolder=$_GET['deleteFolder'];
    $folderToDelete = rtrim($baseDir2, '/') . '/' . ltrim($elfolder, '/');

    if (is_dir($folderToDelete)) {
        rmdir($folderToDelete);
        // echo " ⚠️ Carpeta eliminada $folderToDelete solo si estaba vacia.  ";
    } else {
//        echo "  ⚠️ Carpeta $folderToDelete no encontrada.  ";
          echo "<script>alert('🚨 FOLDER $elfolder no encontrado');</script>";
    }
echo "<script>window.location.href = './'; </script>";
exit;
}





/////////// ELIMINAR FILES //////////////////
if($_GET["delete"]){
    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
         // die($alertasegura); // usaremos exit; para agregar un registro de logs a futuro
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }


$cadena = $_GET['delete'];
$archivoname = basename($cadena);
//    $fileToDelete = $baseDir2 . $archivoname;
      $fileToDelete = rtrim($baseDir2, '/') . '/' . ltrim($archivoname, '/');

    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
       // echo " ⚠️El archivo $fileToDelete  a sido eliminado...  ";
    } else {
       // echo " ⚠️El archivo $fileToDelete  no fue encontrado.  ";
echo "<script>alert('🚨 Archivo $archivoname no encontrado');</script>";
    }

echo "<script>window.location.href = './'; </script>";
exit;


} // fin de ELIMINAR FILES


/////////// CREAR FILE //////////////////
if($_GET["new"]=="file"){
    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
         // die($alertasegura); // usaremos exit; para agregar un registro de logs a futuro
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }
} // fin de new file


if (isset($_POST['createFile'])){
    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }
$fileToEdit = $_POST["FileNew"];
    // 🧹 Limpiar el nombre para evitar inyecciones o rutas
    $namefile = trim($fileToEdit);
    $namefile = basename($namefile); // elimina rutas como ../../etc
    $namefile = preg_replace('/[^a-zA-Z0-9_\-. ñÑáéíóúÁÉÍÓÚ]/u', '', $namefile);
$fileToEdit = $namefile;

$fileToEdit = urldecode($fileToEdit); // decodifica %20 → espacio
// Construir la ruta final del archivo
$editarFile = $baseDir2 . '/' . $fileToEdit;
    if (file_exists($editarFile)) {
echo "<script>alert(' 🚨 El archivo $fileToEdit ya existe!');</script>";
    } else {
        //echo "creando el archivo  $editarFile ";
        file_put_contents($editarFile,'');
echo "<script>window.location.href = './?edit=$fileToEdit'; </script>";
exit;
    }
 } //filename




/////////// CREAR CARPETA /////////////////////
if($_GET["new"]=="folder"){
    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
         // die($alertasegura); // usaremos exit; para agregar un registro de logs a futuro
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }
}

////
if (isset($_POST['createFolder'])) {
    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
         // die($alertasegura); // usaremos exit; para agregar un registro de logs a futuro
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }

 


    // 🚧 2. Validar que el campo no esté vacío
    if (empty($_POST['FolderNew'])) {
        echo "<script>alert('❌ No se indicó el nombre de la carpeta'); window.history.back();</script>";
        exit;
    }

    // 🧹 3. Limpiar el nombre de la carpeta para evitar inyecciones o rutas
    $namecarpeta = trim($_POST['FolderNew']);
    $namecarpeta = basename($namecarpeta); // elimina rutas como ../../etc
//  $namecarpeta = preg_replace('/[^a-zA-Z0-9_\-. ]/', '', $namecarpeta);
    $namecarpeta = preg_replace('/[^a-zA-Z0-9_\-. ñÑáéíóúÁÉÍÓÚ]/u', '', $namecarpeta);

 $newFolder = rtrim($baseDir2, '/') . '/' . ltrim($namecarpeta, '/');

    if (!is_dir($newFolder)) {
        mkdir($newFolder, 0755);
//        echo "  ⚠️ Carpeta creada en $newFolder (casi).  ";
    echo "<script>alert(' ✅ La carpeta $namecarpeta se creo, correctamente'); window.location.href = './'; </script>";
exit;
    } else {
//        echo "  ⚠️ La carpeta no se creo, por que ya existe.  ";
    echo "<script>alert('   ⚠️ La carpeta $namecarpeta no se creo, por que ya existe o alguna otra razon'); window.location.href = './';</script>";
exit;
    }

}





///////////////////////////////////////
///  SUBIR VARIOS X AJAX  V.GIS  //////
///////////////////////////////////////
if (isset($_GET["varios"])) {
    // Requiere autenticación SIEMPRE (sin importar $versinclave)
    if (!$is_authenticated) {
         // die($alertasegura); // usaremos exit; para agregar un registro de logs a futuro
          echo "$alertasegura";
          //codigo para registrar actividad - falta
          exit;
    }

//echo "subiendo varios test en ...";
echo " <script>alert('subiendo varios test  ');</script> "; // esto no sale cuando ajax lo ejecuta

if (!empty($_FILES['files']['name'][0])) {
    
    foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
        $fileName = basename($_FILES['files']['name'][$key]);
        $targetFile2 = rtrim($baseDir2, '/') . '/' . ltrim($fileName, '/');

        if (move_uploaded_file($tmpName, $targetFile2)) {
            echo "Archivo subido: $fileName\n";
        } else {
            echo "Error al subir el archivo: $fileName\n";
        }
    }
} else {
    //echo "No se han recibido archivos.";
    echo " <script>alert('No se han recibido archivos.  ');</script> ";
}
exit;
}
///////////////////////////////////////
///  FIN SUBIR VARIOS X AJAX     //////
///////////////////////////////////////

////////////////// Comprimir archivo o carpeta 🚀
if (isset($_POST['compressFile'])) {

    // ✅ Validar autenticación UNA SOLA VEZ
    if (!$is_authenticated) {
        echo $alertasegura;
        exit;
    }

    // ✅ SANITIZAR inputs
    $namefilec = isset($_POST['archivoacomprimir']) ? trim($_POST['archivoacomprimir']) : '';
    $namefilepass = $_POST['password'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    // ✅ Validar que no esté vacío
    if (empty($namefilec)) {
        echo "<script>alert('❌ Debes especificar un archivo o carpeta'); window.history.back();</script>";
        exit;
    }

    // ✅ SEGURIDAD: Eliminar path traversal
    $namefilec = str_replace(['../', '..\\', '~'], '', $namefilec);
    $namefilec = ltrim($namefilec, '/\\');

    // ✅ Construir rutas de forma segura
    $ruta = rtrim($baseDir2, '/') . '/' . $namefilec;
    $nombreZip = rtrim($baseDir2, '/') . '/' . $namefilec . '.zip';

    // ✅ Validar que el archivo/carpeta exista
    if (!file_exists($ruta)) {
        echo "<script>alert('❌ El archivo o carpeta no existe'); window.history.back();</script>";
        exit;
    }

    // ✅ Validar que esté dentro del directorio permitido
    $rutaReal = realpath($ruta);
    $baseReal = realpath($baseDir2);
    if ($rutaReal === false || strpos($rutaReal, $baseReal) !== 0) {
        echo "<script>alert('🚫 Acceso denegado'); window.history.back();</script>";
        exit;
    }

    // ✅ Función para comprimir carpetas
    function comprimirCarpetaConContrasena($origen, $destino, $excluir = [], $contrasena) {
        $zip = new ZipArchive();

        if ($zip->open($destino, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
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
                    $zip->setCompressionName($rutaRelativa, ZipArchive::CM_DEFLATE, 9);
                    
                    if (!empty($contrasena)) {
                        $zip->setEncryptionName($rutaRelativa, ZipArchive::EM_AES_256, $contrasena);
                    }
                }
            }
        }

        $zip->close();
        return file_exists($destino);
    }

    // ✅ Procesar compresión
    $exito = false;
    $mensaje = '';

    if (is_file($ruta)) {
        // Comprimir un único archivo
        $zip = new ZipArchive();
        
        if ($zip->open($nombreZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($ruta, basename($ruta));
            $zip->setCompressionName(basename($ruta), ZipArchive::CM_DEFLATE, 9);
            
            if (!empty($namefilepass)) {
                $zip->setEncryptionName(basename($ruta), ZipArchive::EM_AES_256, $namefilepass);
            }

            $zip->close();
            $exito = true;
            $mensaje = "Archivo comprimido correctamente";
        } else {
            $mensaje = "Error al crear el archivo ZIP";
        }
        
    } elseif (is_dir($ruta)) {
        // Comprimir carpeta completa
        if (comprimirCarpetaConContrasena($ruta, $nombreZip, [], $namefilepass)) {
            $exito = true;
            $mensaje = "Carpeta comprimida correctamente";
        } else {
            $mensaje = "Error al comprimir la carpeta";
        }
    } else {
        $mensaje = "La ruta especificada no es válida";
    }

    // ✅ Agregar comentario (DESPUÉS de cerrar el ZIP)
    if ($exito && !empty($descripcion) && file_exists($nombreZip)) {
        $zip = new ZipArchive();
        if ($zip->open($nombreZip) === TRUE) {
            $zip->setArchiveComment($descripcion);
            $zip->close();
        }
    }

    // ✅ Mostrar resultado
    if ($exito) {
        $nombreArchivo = basename($nombreZip);
        echo "<script>alert('✅ $mensaje\\n\\nArchivo: $nombreArchivo'); window.location.href = './';</script>";
    } else {
        echo "<script>alert('❌ $mensaje'); window.history.back();</script>";
    }
    exit;
}
////////////////// Comprimir archivo o carpeta 🚀


// ==============================
 // SEO Y DATOS SEMÁNTICOS DE LA PÁGINA
 // ==============================
$seoPath = str_replace($baseDir, "", $targetDir) ?: "/";
$seoPath = '/' . trim($seoPath, '/') . (trim($seoPath, '/') !== '' ? '/' : '');
$seoPathLabel = $seoPath === '/' ? 'directorio raíz' : trim($seoPath, '/');
$pageTitle = 'Explorador de Archivos' . ($seoPath === '/' ? '' : ' - ' . $seoPathLabel);
$pageDescription = 'Explorador de archivos y directorios de Zidrave. Consulta, organiza y gestiona archivos disponibles en ' . ($seoPath === '/' ? 'el directorio raíz' : $seoPathLabel) . '.';
$canonicalUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . strtok($_SERVER['REQUEST_URI'], '?');
$canonicalUrl = rtrim($canonicalUrl, '/') . ($seoPath === '/' ? '/' : '/');
$seoPathEsc = htmlspecialchars($seoPath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$pageTitleEsc = htmlspecialchars($pageTitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$pageDescriptionEsc = htmlspecialchars($pageDescription, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$canonicalUrlEsc = htmlspecialchars($canonicalUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo $pageTitleEsc; ?></title>
<meta name="description" content="<?php echo $pageDescriptionEsc; ?>">
<meta name="robots" content="noindex, follow">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="theme-color" content="#1e3a5f">

<link rel="canonical" href="<?php echo $canonicalUrlEsc; ?>">

<meta property="og:title" content="<?php echo $pageTitleEsc; ?>">
<meta property="og:description" content="<?php echo $pageDescriptionEsc; ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo $canonicalUrlEsc; ?>">
<meta property="og:image" content="https://files.zidrave.net/logzidxx2.png">
<meta property="og:site_name" content="Zidrave File Manager">

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?php echo $pageTitleEsc; ?>">
<meta name="twitter:description" content="<?php echo $pageDescriptionEsc; ?>">
<meta name="twitter:image" content="https://files.zidrave.net/logzidxx2.png">

<script type="application/ld+json">
<?php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $pageTitle,
    'description' => $pageDescription,
    'url' => $canonicalUrl,
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Zidrave File Manager',
        'url' => 'https://files.zidrave.net/'
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
</script>

<style>
<?php echo getThemeStyles($current_theme); ?>

/* Mejoras semánticas y de accesibilidad que no alteran las funciones existentes */
.sr-only {
    position:absolute !important;
    width:1px !important;
    height:1px !important;
    padding:0 !important;
    margin:-1px !important;
    overflow:hidden !important;
    clip:rect(0,0,0,0) !important;
    white-space:nowrap !important;
    border:0 !important;
}
.table-wrapper {
    width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}
.file-link {
    min-width:0;
}
.file-link b {
    overflow-wrap:anywhere;
}
@media (max-width:768px) {
    .table-wrapper table {
        min-width:620px;
    }
}
</style>
</head>
<body>

<?php if ($current_theme === 'joomla'): ?>
<nav class="top-nav" aria-label="Navegación principal">
    <div class="top-menu">
        <a href="/" class="top-menu-item">🏠 Inicio</a>
        <a href="#" class="top-menu-item">📁 Explorador</a>
        <a href="#" class="top-menu-item">⚙️ Configuración</a>
        <a href="#" class="top-menu-item">👤 Usuario</a>
    </div>
</nav>
<?php endif; ?>

<header role="banner">
    <?php if ($current_theme === 'joomla'): ?>
    <div class="header-content">
        <div class="title">Explorador de Archivos</div>
   <?php if ($versinclave == 1): ?>
        <a href="?logout" class="logout-btn">Cerrar Sesión x</a>
    <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="title" aria-label="Zidrave File Manager"><span class="folder-icon" aria-hidden="true">📁</span> Explorador de Archivos</div>
       <?php if ($versinclave == 1): ?>
    <a href="?logout" class="logout-btn">Cerrar Sesión</a>
       <?php endif; ?>
    <?php endif; ?>
</header>

<?php
//$mdFile = str_replace("%20"," ",$mdFile); 
$urlactual = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$urlactual = str_replace("%20"," ",$urlactual); 
$urlsindom = $_SERVER['REQUEST_URI'];
?>


<nav class="breadcrumb" aria-label="Ruta de navegación">
    <?php if ($current_theme === 'joomla'): ?>
    <div class="breadcrumb-content">
        <h1>🌎 <?php echo htmlspecialchars($urlactual, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h1>
    </div>
    <?php else: ?>
    <h1>🌎 <?php echo htmlspecialchars($urlactual, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h1>
    <?php endif; ?>
</nav>
<?php
$path = str_replace($baseDir, "", $targetDir) ?: "/";
$path2 = $path; 
if ($path !== "/") {
    $path = rtrim($path, "/") . "/";
}

$segments = explode("/", trim($path, "/"));
$breadcrumb = "";
$currentPath = "";

foreach ($segments as $index => $segment) {
    if ($segment === "") continue; // evitar vacío al inicio

    $currentPath .= "/" . $segment;

    // Si es el último segmento → poner en negrita sin link
    if ($index === array_key_last($segments)) {
        $breadcrumb .= "<b> <a href='$currentPath/' class='link-link' > $segment </a> </b> / ";
//        $ultimodir = $segment;
        $ultimodir = "<a href='$currentPath/' class='link-link' >$segment </a>";
    } else {
        $safeSegment = htmlspecialchars($segment, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeCurrentPath = htmlspecialchars($currentPath . '/', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $breadcrumb .= "<a href='$safeCurrentPath' class='link-link'>$safeSegment</a><b>/</b>";
        
    }
}
?>

<main id="main-content" class="main-content">
<section class="info-box" aria-labelledby="directory-info-title">
    <h2 id="directory-info-title" class="sr-only">Información del directorio</h2>
    <p><strong>📂 Directorio :</strong>  <a href='/'>🏠 </a> <b>/</b> <?php echo "$breadcrumb"; ?> </p> 
    <p><strong>📊 Elementos:</strong> <span style="background-color: #b85900; color: #ffffff;"> <b> <?php echo $file_count; ?> </b> </span>  archivos en este directorio</p>

<?php
  if ($is_authenticated) {
?>
<a href="?new=folder" class="logout-btn" aria-label="Crear una nueva carpeta">📂 Crear Carpeta</a>  <a href="?new=file" class="logout-btn" aria-label="Crear un nuevo archivo">📝 Crear Archivo</a>   <a href="?new=uploads" class="logout-btn" aria-label="Subir archivos">🔄 Subir Archivos</a>

<?php 
}
?>
</section>










<?php
if($_GET["passgen"]){
?>

<div class="info-box">
<?php
$passx = isset($_POST["passx"]) ? $_POST["passx"] : "1111";
$clavemagica = password_hash("$passx", PASSWORD_DEFAULT);
?>
<b class="link-link"> <?php echo "$clavemagica  </b> <br> <b class='stat-label'> CLAVE ORIGINAL: $passx ";?></b>

<form action="./?passgen=on" method="POST">
🔐
          <input  class="eform" type="text" name="passx"
            placeholder="Contraseña a encriptar..."
            value=""
            style="
              padding:8px;
              border-radius:5px;

              min-width:250px;
              box-sizing:border-box;
            ">
<button type="submit" name="passgen"  class="logout-btn">Generar Password</button>
<a href="<?php echo "$path2"; ?>" class="logout-btnred">Cerrar</a>
 </form>
</div> 

<?php
}
?>






<?php
if($_GET["new"]=="uploads"){
?>
<div class="info-box">




    <style>
        #drop-area {
            width:95%;

            padding: 60px;
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
        <button id="fileSelect" class="logout-btn">Seleccionar archivos</button>
        <div id="file-list"></div>
        <div id="progress-bar">
            <div id="progress-bar-fill">0%</div>


        </div>
<center>   <a href="./" class="logout-btnred"> Cerrar </a>   </center>
    </div>

<?php
        $itargetFile = rtrim($baseDir2, '/') . '/' . ltrim($fileName, '/');
 // echo "$baseDir2 , $itargetFile <br>"; //descubrir las rutas

?>


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
            xhr.open('POST', './?varios=1', true);

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


 
</div> 
<?php
}
?>



<?php
if($_GET["new"]=="folder"){
?>
<div class="info-box">
<form action="./" method="POST">
📂 
          <input  class="eform" type="text" name="FolderNew"
            placeholder="Nombre de la carpeta.."
            value=""
            style="
              padding:8px;
              border-radius:5px;

              min-width:250px;
              box-sizing:border-box;
            ">

<button type="submit" name="createFolder"  class="logout-btn">Crear Carpeta</button>
 </form>
</div> 
<?php
}
?>




<?php
if($_GET["new"]=="file"){
?>
<div class="info-box">
<form action="./" method="POST">
📄 
          <input  class="eform" type="text" name="FileNew"
            placeholder="Nombre del archivo.."
            value=""
            style="
              padding:8px;
              border-radius:5px;

              min-width:250px;
              box-sizing:border-box;
            ">

<button type="submit" name="createFile"  class="logout-btn">Crear Archivo</button>
 </form>
</div> 
<?php
}
?>




<?php
if($_GET["new"]=="compress"){
$file_f = $_GET["f"];
$file_f = basename($file_f);
?>
<div class="info-box">
<form action="./" method="POST">
📦 
          <input  class="eform" type="text" name="archivoacomprimir"
            placeholder="Nombre del archivo.."
            value="<?php echo $file_f;?>"
            style="
              padding:8px;
              border-radius:5px;

              min-width:250px;
              box-sizing:border-box;
            ">

          <input  class="eform" type="text" name="password"
            placeholder="Password (Opcional)"
            value=""
            style="
              padding:8px;
              border-radius:5px;

              min-width:250px;
              box-sizing:border-box;
            ">
        <input type="hidden" name="descripcion" value="
           ,______________________________________       
          |_________________,----------._ [____]  ''-,__  __....-----====
                        (_(||||||||||||)___________/   ''                |
                           `----------' zIDRAvE[ ))'-,                   |
                     FILE MANAGER (Index of)    ''    `,  _,--....___    |
                     https://zidrave.net/?p=4641        `/           ''''
...................................................................................
2025
" >
<button type="submit" name="compressFile"  class="logout-btn">Comprimir Archivo</button>
<a href="<?php echo "$path2"; ?>" class="logout-btnred">Cerrar</a>
 </form>
</div> 
<?php
}
?>



<?php
/////////////////////////////////////EDIT/////////////////////////////////

if (isset($_GET['edit']) ) {
$efile=$_GET["edit"];
//htmlspecialchars
$fileContent = htmlspecialchars($fileContent);

?>
<div  class="content-box" >

  <div class="box-header" >  
    <h3 >📝 Editando <?php echo "$efile";?> </h3>
      </div>

<div style="padding:20px;">
  <div class="stat-value">

     <form action="" method="post" style="width:100%;">
        <textarea name="fileContent" class="eform"
          style="
            width:100%;
            height:400px;
            border-radius:6px;
            resize:vertical;
            padding:10px;
            box-sizing:border-box;
          "
          placeholder="Escribe o edita tu código aquí..."><?php echo "$fileContent";?></textarea>

        <!-- Contenedor flexible -->
        <div style="
          display:flex;
          justify-content:space-between;
          align-items:center;
          margin-top:10px;
          flex-wrap:wrap;
        ">
          <!-- IZQUIERDA -->
          <input  class="eform" type="text" name="fileName"
            placeholder="Nombre del archivo..."
            value="<?php echo "$efile";?>"
            style="
              padding:8px;
              border-radius:5px;

              min-width:200px;
              box-sizing:border-box;
            ">

          <!-- DERECHA -->
          <div style="display:flex; gap:8px;">
            <button type="submit" name="saveFile"  class="logout-btn">Guardar</button>
            <a href="<?php echo "$path2"; ?>" class="logout-btnred">Cerrar</a>
          </div>
        </div>
      </form>

  </div>
</div>

  </div>
<?php
} ////////////////////fin EDIT //////////////////////////
?>










<div id="txt-viewer"  style="display:none;">
 
 
<div style="padding:10px;">
    <div style="text-align:right; margin-top:-20px;">
<?php
  if ($is_authenticated) {
?>
        <a href="?edit=" class="logout-btn">Editar</a>
<?php
}
?>
        <button id="close-txt2" class="logout-btnred">Cerrar</button>
    </div>
</div>
 

<div  class="content-box" >

  <div class="box-header">  
    <h3 id="txt-title"></h3>
      </div>

<div style="padding:20px; display:none;" id="txt-path-container">
  <div class="stat-value">
      <pre id="txt-path"></pre>
  </div>
</div>

    <div style="padding:20px;">
    <div class="stat-item" >
        <pre id="txt-content" style="white-space:pre-wrap; font-family:monospace; margin:0;"></pre>
    </div>
    </div>

</div>



<div style="padding:10px;">
    <div style="text-align:right; margin-top:-20px;">


<?php
  if ($is_authenticated) {
?>
        <a href="?edit=" class="logout-btn">Editar</a>
<?php
}
?>


        <button id="close-txt" class="logout-btnred">Cerrar</button>
    </div>
</div>


 
<br>
</div>



<?php
///////////////////////// README.MD ///////////////////////////////
$mdFile = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $_SERVER['REQUEST_URI'] . 'readme.md';
$mdFile = str_replace("%20"," ",$mdFile); 

//$mdFile = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $_SERVER['REQUEST_URI'] . '/readme.md';
//$mdFile = __DIR__ . $_SERVER['REQUEST_URI'].'/readme.md';
//$mdFile = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/readme.md';


if (!file_exists($mdFile)) {
//    echo "no encontro $mdFile";
//    http_response_code(404);
//    echo "<h1>readme.md no encontrado</h1> <p>  $mdFile </p>";
//    exit;
} else {
//echo "Buscando: $mdFile";
$md = file_get_contents($mdFile);

// Escapamos primero para evitar inyección; aplicaremos transformaciones sobre texto seguro.
// $md = htmlspecialchars($md, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

// ---- Parser Markdown ligero (cubrir lo más usado) ----



 

// ---- Parser Markdown con protección de bloques ----

// 1) PROTEGER bloques de código con triple backticks
$codeBlocks = [];
$codeCounter = 0;
$md = preg_replace_callback(
    '/```([a-zA-Z0-9_+-]*)\n(.*?)\n```/s',
    function($m) use (&$codeBlocks, &$codeCounter){
        $lang = $m[1] ? ' class="lang-'.htmlspecialchars($m[1], ENT_QUOTES).'"' : '';
        $code = htmlspecialchars($m[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $placeholder = "___CODEBLOCK{$codeCounter}___";
        $codeBlocks[$placeholder] = "<pre><code{$lang}>{$code}</code></pre>";
        $codeCounter++;
        return $placeholder;
    },
    $md
);

// 2) PROTEGER código inline con backticks simples
$inlineCode = [];
$inlineCounter = 0;
$md = preg_replace_callback(
    '/`([^`\n]+)`/',
    function($m) use (&$inlineCode, &$inlineCounter){
        $code = htmlspecialchars($m[1], ENT_QUOTES);
        $placeholder = "___INLINECODE{$inlineCounter}___";
        $inlineCode[$placeholder] = "<code>{$code}</code>";
        $inlineCounter++;
        return $placeholder;
    },
    $md
);



// 2) Headers
$md = preg_replace('/^######\s*(.+)$/m', '<h6>$1</h6>', $md);
$md = preg_replace('/^#####\s*(.+)$/m', '<h5>$1</h5>', $md);
$md = preg_replace('/^####\s*(.+)$/m', '<h4>$1</h4>', $md);
$md = preg_replace('/^###\s*(.+)$/m', '<h3>$1</h3>', $md);
$md = preg_replace('/^##\s*(.+)$/m', '<h2>$1</h2>', $md);
$md = preg_replace('/^#\s*(.+)$/m', '<h1>$1</h1>', $md);


// 5) Imágenes (ANTES de enlaces y ANTES de escapar)
$md = preg_replace_callback(
    '/!\[([^\]]*)\]\(([^)]+)\)/',
    function($m){
        $alt = htmlspecialchars($m[1], ENT_QUOTES);
        $url = htmlspecialchars($m[2], ENT_QUOTES);
        return '<center><img src="'.$url.'" alt="'.$alt.'" style="max-width:100%; height:auto; border-radius:4px;" /></center>';
    },
    $md
);

// 5) Enlaces
$md = preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function($m){
    $text = htmlspecialchars($m[1], ENT_QUOTES);
    $url  = htmlspecialchars($m[2], ENT_QUOTES);
    return '<a href="'. $url .'" target="_blank" rel="noopener noreferrer">'. $text .'</a>';
}, $md);


//lineas
$md = preg_replace(
    '/(?:\r?\n|\A)[ \t]*(?:-{3,}|\*{3,}|_{3,})[ \t]*(?:\r?\n|\z)/',
    "<hr style=\"border:none; height:1px; background:#415a77; margin:20px 0;\">",
    $md
);

// 5) Bold **text** or __text__
$md = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $md);
$md = preg_replace('/\_\_(.+?)\_\_/s', '<strong>$1</strong>', $md);

// 6) Italic *text* or _text_
$md = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $md);
$md = preg_replace('/\_(.+?)\_/s', '<em>$1</em>', $md);

// 7) Inline code  
$md = preg_replace('/`([^`]+)`/','<code>$1</code>', $md);

// 8) Listas no ordenadas (líneas que empiezan con - o *)
// Convertimos grupos de líneas en <ul><li>...</li></ul>
$md = preg_replace_callback('/(^((?:[ \t]*[-\*]\s+.+\r?\n)+))/m', function($m){
    $block = trim($m[1]);
    $items = preg_split('/\r?\n/', $block);
    $html = "<ul>";
    foreach($items as $it){
        $it = preg_replace('/^[\-\*]\s+/', '', $it);
        $html .= '<li>'.$it.'</li>';
    }
    $html .= "</ul>";
    return $html;
}, $md);

// 9) Listas ordenadas 1. 2. 3.
$md = preg_replace_callback('/(^((?:[ \t]*\d+\.\s+.+\r?\n)+))/m', function($m){
    $block = trim($m[1]);
    $items = preg_split('/\r?\n/', $block);
    $html = "<ol>";
    foreach($items as $it){
        $it = preg_replace('/^\d+\.\s+/', '', $it);
        $html .= '<li>'.$it.'</li>';
    }
    $html .= "</ol>";
    return $html;
}, $md);

// 10) Líneas vacías => saltos de párrafo
// Primero normalizamos saltos de línea
$md = str_replace(["\r\n","\r"], "\n", $md);
// Reemplazamos doble newline por </p><p>, pero respetando que ya haya headers, pre, ul, ol, etc.
$parts = preg_split("/\n{2,}/", $md);
foreach ($parts as &$p) {
    // Si ya empieza con una etiqueta bloque, no envolver
    if (preg_match('/^\s*<(h[1-6]|ul|ol|pre|blockquote|img|p)/i', trim($p))) {
        $p = $p;
    } else {
        // Reemplazco saltos simples por <br> para conservar líneas
        $p = '<p>' . nl2br(trim($p)) . '</p>';
    }
}


$bodyHtml = implode("\n", $parts);
?>
 

<div  class="content-box" id="readme-container">
  <div class="box-header">  
    <h4>🎖️readme.md</h4>
      </div>
 <div style="padding:20px;">
  <div class="stat-value">
     <pre> <?php echo "$bodyHtml"; ?>  </pre>    
  </div>
 </div>
</div>

<?php
/////////////////////////README.MD ///////////////////////////////
} //cerrando
?>

 



<div id="image-modal" style="display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    justify-content:center;
    align-items:center;
    cursor:pointer;
    backdrop-filter: blur(6px);
"></div>










<section class="content-box" aria-labelledby="file-list-title">
    <div class="box-header"><h2 id="file-list-title">📄 Listado de Archivos</h2></div>
    <div class="table-wrapper">
    <table>
        <caption class="sr-only">Archivos y carpetas disponibles en <?php echo $seoPathEsc; ?></caption>
        <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Tipo</th>
                <th scope="col">Tamaño</th>
                <th scope="col">Modificado</th>
            </tr>
        </thead>
        <tbody>
<?php
// Subir al padre
if ($requestedPath !== "." && $requestedPath !== "") {
    $parent = dirname($requestedPath);
    $parent = $parent === "." ? "/" : "/" . $parent . "/";
    $safeParent = htmlspecialchars($parent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo "<tr><td colspan='4'><a href='$safeParent' class='file-link' aria-label='Subir al directorio anterior'><span class='file-icon' aria-hidden='true'>🔺</span><b> Subir al directorio anterior </b></a></td></tr>";
}



// LISTAR CARPETAS Y ARCHIVOS

//esta funcion es para calcular el tamaño de una carpeta pero es muy pesada solo usarlo bajo demanda
function folderSize($dir) {
    $size = 0;
    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        $size += is_dir($path) ? folderSize($path) : filesize($path);
    }
    return $size;
}


// Obtener lista de archivos y carpetas
$files = scandir($targetDir);

// Filtrar "." y ".." y ocultar globalindex.php de la vista
$files = array_diff($files, ['.', '..', 'globalindex.php']);


// Separar carpetas y archivos
$folders = [];
$regularFiles = [];

foreach ($files as $file) {
    if (is_dir($targetDir . '/' . $file)) {
        $folders[] = $file;
    } else {
        $regularFiles[] = $file;
    }
}

// Ordenar alfabéticamente (insensible a mayúsculas/minúsculas)
natcasesort($folders);
natcasesort($regularFiles);

// Combinar: primero carpetas, luego archivos
$sortedFiles = array_merge($folders, $regularFiles);
$carpetaNum = 1;
$archivoNum = 1;

// Ahora usa $sortedFiles en lugar de $files
foreach ($sortedFiles as $file) {
    $fullPath = $targetDir . "/" . $file;
    $isDir = is_dir($fullPath);
  if ($is_authenticated) {


    //$type = $isDir ? "🗂️ | ⚙️ 📚 ❌" : "📄 | 📝 ⚙️ 📚 ❌";
if($isDir){



$type = "🗂️ | 
⚙️ 
<a href=\"?new=compress&f=$file\" class='link-link'>📚  </a>
<a href=\"?deleteFolder=$file\" class='link-link'  onclick=\"return confirm('🗑 ¿Seguro que deseas eliminar \\n la Carpeta $file ❓ \\n \\n  Solo Se eliminara si la Carpeta esta vacia');\">❌ </a>";
} else {
$type = "📄 | 
<a href=\"?edit=$file\" class='link-link'>📝 </a>
<a href=\"?fconfig=$file\" class='link-link'>⚙️ </a>
<a href=\"?download=$file\" class='link-link' target='_blank' rel='noopener noreferrer'> ⬇️  </a>
<a href=\"?new=compress&f=$file\" class='link-link'>📚 </a>
<a href=\"?delete=$file\" class='link-link'  onclick=\"return confirm('🗑 ¿Seguro que deseas eliminar \\n el archivo $file ❓');\">❌ </a>";
}


  } else {
    $type = $isDir ? "🗂️ DIR " : "📄 Archivo";
  }

// ver tamaño de una carṕeta solo bajo demanda.
//$size = $isDir ? formatBytes(folderSize($fullPath)) : formatBytes(filesize($fullPath));

    $size = $isDir ? "-" : formatBytes(filesize($fullPath)); //en caso de ser carpeta mostrara - en ves de su tamaño, es mas lite asi.
    $size = preg_replace('/([\d\.]+)/', '<strong class="link-link">$1</strong>', $size);
    $fileMtime = filemtime($fullPath);
    $modTime = date("d/m/Y H:i:s", $fileMtime);
    $modTime = preg_replace('/^(\d{2}\/\d{2}\/\d{4})/', '<strong class="link-link">$1</strong>', $modTime);
    $modTimeHtml = '<time datetime="' . date('c', $fileMtime) . '">' . $modTime . '</time>';

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    // Asignar iconos
    $icon = "📄"; 
    if ($isDir) {
        $icon = "📁";
    } elseif (in_array($ext, ["jpg","jpeg","png","gif","webp","bmp"])) {
        $icon = "🖼️";
    } elseif (in_array($ext, ["mp3","wav","ogg"])) {
        $icon = "🎵";
    } elseif (in_array($ext, ["mp4","mkv","avi"])) {
        $icon = "🎬";
    } elseif (in_array($ext, ["zip","rar","7z","tar","gz"])) {
        $icon = "📚";
    } elseif (in_array($ext, ["php","html","css","js","py","sh"])) {
        $icon = "💻";
    } elseif (in_array($ext, ["pdf"])) {
        $icon = "📕";
    }  elseif (in_array($ext, ["docx","rtf"])) {
        $icon = "📘";
    }


    $link = "/" . ltrim(($requestedPath === "." ? "" : $requestedPath . "/") . $file, "./");
    if ($isDir) $link .= "/";

    echo "<tr>";
    $textExts = ["txt", "log", "md", "ini", "cfg", "json", "xml", "csv", "dat", "inf"];
    $imageExts = ["jpg", "jpeg", "png", "gif", "webp", "bmp", "svg"];






// DESPUÉS (CORREGIDO):

if ($isDir) {
  $numerocarpeta = $carpetaNum++;
    // CARPETAS siempre van a enlace normal
    $safeLink = htmlspecialchars($link, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeFile = htmlspecialchars($file, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo "<td><a href='$safeLink' class='file-link' title='Carpeta $safeFile' aria-label='Abrir carpeta $safeFile'><span class='file-icon' aria-hidden='true'>$icon</span> <b>$safeFile</b></a></td>";
} else {
$numeroarchivo = $archivoNum++;
//recortar el nombre de los archivos que sean muy largos
$itemr = $file;
if (strlen($itemr) > 33) {
    $itemr = substr($itemr, -33);
    $file = "➰".$itemr;
}

    // ARCHIVOS aplican lógica especial
    if (in_array($ext, $textExts)) {
        $safeLink = htmlspecialchars($link, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeFile = htmlspecialchars($file, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo "<td><a href='$safeLink' title='Archivo $safeFile' class='file-link txt-link' data-file='$safeLink' aria-label='Abrir archivo $safeFile'><span class='file-icon' aria-hidden='true'>$icon</span> <b>$safeFile</b> 🔹</a></td>";
    } elseif (in_array($ext, $imageExts)) {
        $safeLink = htmlspecialchars($link, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeFile = htmlspecialchars($file, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo "<td><a href='$safeLink' title='Vista previa de $safeFile' class='file-link image-link' data-file='$safeLink' aria-label='Ver imagen $safeFile'><span class='file-icon' aria-hidden='true'> $icon</span> <b>$safeFile</b> 🔹</a></td>";
    } else {
        $safeLink = htmlspecialchars($link, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeFile = htmlspecialchars($file, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo "<td><a href='$safeLink' title='Abrir $safeFile' class='file-link' aria-label='Abrir $safeFile'><span class='file-icon' aria-hidden='true'> $icon</span> <b>$safeFile</b></a></td>";
    }
}



    echo "<td>$type</td>";
    echo "<td>$size</td>";
    echo "<td>$modTimeHtml</td>";
    echo "</tr>";
}







// Subir al padre
if ($requestedPath !== "." && $requestedPath !== "") {
    $parent = dirname($requestedPath);
    $parent = $parent === "." ? "/" : "/" . $parent . "/";
    $safeParent = htmlspecialchars($parent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
echo "<tr><td colspan='4'><a href='$safeParent' class='file-link' aria-label='Subir al directorio anterior'><span class='file-icon' aria-hidden='true'>🔺</span> <b> Subir al directorio anterior </b></a></td></tr>";
}

?>
        </tbody>
    </table>
    </div>
</section>

<section class="content-box" aria-labelledby="system-info-title">
    <div class="box-header"><h2 id="system-info-title">📊 Información del Sistema</h2></div>
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
</section>
</main>

<!-- Selector de Temas -->
<aside class="theme-selector" aria-label="Selector de temas">
    <label for="theme-select" style="font-size:12px; color:#666; display:block; margin-bottom:8px; font-weight:bold;">🎨 Tema:</label>
    <select id="theme-select" aria-label="Seleccionar tema visual" onchange="window.location.href='?change_theme='+this.value">
        <option value="zidrave-skin" <?php echo $current_theme === 'zidrave-skin' ? 'selected' : ''; ?>>Zidrave Skin</option>
        <option value="taringa" <?php echo $current_theme === 'taringa' ? 'selected' : ''; ?>>Taringa</option>
        <option value="joomla" <?php echo $current_theme === 'joomla' ? 'selected' : ''; ?>>Joomla</option>
        <option value="github" <?php echo $current_theme === 'github' ? 'selected' : ''; ?>>Github</option>
        <option value="leonardo" <?php echo $current_theme === 'leonardo' ? 'selected' : ''; ?>>Leonardo</option>

    </select>
</aside>



<script>


(function() {
    const modal = document.getElementById('image-modal');
    const imageLinks = Array.from(document.querySelectorAll('.image-link'));
    const imagenes = imageLinks.map(link => link.dataset.file);
    let indiceActual = -1;
    let zoomActual = 1;
    let posY = 0;

    function aplicarTransformacion() {
        const img = modal.querySelector('img');
        if (img) img.style.transform = `translateY(${posY}px) scale(${zoomActual})`;
    }

    function mostrarImagen(indice) {
        if (indice < 0 || indice >= imagenes.length) return;

        indiceActual = indice;
        zoomActual = 1;
        posY = 0;
        const fileUrl = imagenes[indiceActual];

        modal.innerHTML = `
            <img src="${fileUrl}" alt="Vista previa de imagen ${indiceActual + 1}" style="max-width:90%; max-height:90%; transition: transform 0.15s ease;">
            <p style="position:absolute; bottom:20px; color:#fff; font-size:13px; background:rgba(0,0,0,0.5); padding:6px 14px; border-radius:20px;">
                ${indiceActual + 1} / ${imagenes.length} — ← → cambiar · + − zoom · ↑ ↓ mover · Esc cerrar
            </p>
        `;
        modal.style.display = 'flex';
        aplicarTransformacion();
    }

    function cerrarModal() {
        modal.style.display = 'none';
        modal.innerHTML = '';
        indiceActual = -1;
        zoomActual = 1;
        posY = 0;
    }

    imageLinks.forEach((link, indice) => {
        link.addEventListener('click', e => {
            e.preventDefault();
            mostrarImagen(indice);
        });
    });

    modal.addEventListener('click', (e) => {
        if (e.target.tagName === 'IMG' && zoomActual > 1) return;
        cerrarModal();
    });

    document.addEventListener('keydown', (e) => {
        if (indiceActual === -1) return;

        if (e.key === 'ArrowRight' || e.key === ' ') {
            e.preventDefault();
            mostrarImagen(indiceActual + 1);
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            mostrarImagen(indiceActual - 1);
        } else if (e.key === 'ArrowDown') {
            if (zoomActual <= 1) return; // sin zoom no hay nada que mover
            e.preventDefault();
            posY -= 40;
            aplicarTransformacion();
        } else if (e.key === 'ArrowUp') {
            if (zoomActual <= 1) return;
            e.preventDefault();
            posY += 40;
            aplicarTransformacion();
        } else if (e.key === 'Escape') {
            cerrarModal();
        } else if (e.key === '+' || e.key === '=') {
            e.preventDefault();
            zoomActual = Math.min(zoomActual + 0.25, 5);
            if (zoomActual === 1) posY = 0; // recentra si vuelve a 100%
            aplicarTransformacion();
        } else if (e.key === '-') {
            e.preventDefault();
            zoomActual = Math.max(zoomActual - 0.25, 0.25);
            if (zoomActual <= 1) posY = 0;
            aplicarTransformacion();
        }
    });
})();



function mostrarArchivo(nombre, contenido, ruta) {
    document.getElementById("txt-title").textContent = nombre;
    document.getElementById("txt-content").textContent = contenido;

    if (ruta) {
        // Arma la URL completa con dominio
        const fullUrl = window.location.origin + ruta;
        document.getElementById("txt-path").textContent = "📎 " + fullUrl;
        document.getElementById("txt-path-container").style.display = "block";
    } else {
        document.getElementById("txt-path-container").style.display = "none";
    }

    document.getElementById("txt-viewer").style.display = "block";



    // OCULTAR el contenedor del README cuando se abre un archivo de texto
    const readmeContainer = document.getElementById("readme-container");
    if (readmeContainer) {
        readmeContainer.style.display = "none";
    }

    // 🔹 Actualizar el enlace del botón Editar
    const editLinks = document.querySelectorAll(".logout-btn[href*='?edit=']");
    editLinks.forEach(link => {
        const filename = ruta ? ruta.split("/").pop() : nombre.replace("📄 ", "");
        link.href = `?edit=${filename}`;
    });

}

document.addEventListener("DOMContentLoaded", () => {
    const links = document.querySelectorAll(".txt-link");
    const viewer = document.getElementById("txt-viewer");
    const title = document.getElementById("txt-title");
    const content = document.getElementById("txt-content");

    const closeBtn = document.getElementById("close-txt");
    const closeBtn2 = document.getElementById("close-txt2");


    links.forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            const file = link.dataset.file;

//fetch(file)
fetch(file + "?_=" + Date.now()) // ← fuerza a no usar caché
    .then(res => res.text())
    .then(text => {
        mostrarArchivo("📄 " + file.split("/").pop(), text, file);
        viewer.style.display = "block";

        // 👇 Aquí haces que la página suba al inicio
        window.scrollTo({ top: 0, behavior: 'smooth' });
    })
    .catch(err => {
        mostrarArchivo("Error", "No se pudo cargar el archivo.", null);
        viewer.style.display = "block";
        window.scrollTo({ top: 0, behavior: 'smooth' }); // también en errores
    });
        });
    });

    closeBtn.addEventListener("click", () => {
        viewer.style.display = "none";
        title.textContent = "";
        content.textContent = "";
        // MOSTRAR nuevamente el contenedor del README
        const readmeContainer = document.getElementById("readme-container");
        if (readmeContainer) {
            readmeContainer.style.display = "block";
        }
    });


    closeBtn2.addEventListener("click", () => {
        viewer.style.display = "none";
        title.textContent = "";
        content.textContent = "";
        // MOSTRAR nuevamente el contenedor del README
        const readmeContainer = document.getElementById("readme-container");
        if (readmeContainer) {
            readmeContainer.style.display = "block";
        }
    });



});


</script>

 



<footer>





    <p class="copyright">© <?php echo date("Y"); ?> zIDLAB Corporation - Todos los derechos reservados - <a href="?passgen=on" class="link-link">Generar Password</a></p>
    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEicRrhs4L2BvhDfxiyrZGCWUYcCiDrKTOskZSwIsjvVZx7AQMNG6huy2DoX0An7ywtr8iOxm26Qo2r03DBLcHNCCMV67sC2e9Cvj5wqQHtibqCBZEC2X-0A9Rh3sb9TTlj8M_lpuZb_4hziIPBE-2Zh54Ie6O1cF5Is-hLHKVeSxSz_tJDc3J0jC_UDkg8/s320/logoskull2.png" alt="Logo" />
    <p style="font-size:12px; opacity:0.8;">Explorador de Carpetas de Zidrave - <a href='https://zidrave.net/?p=4641'  class='link-link' target='_blank'><b>Ver Proyecto</b></a> Otros Proyectos:  <a href='https://file4-manager.pages.dev/'  class='link-link' target='_blank'><b>File4 Manager</b></a> - <a href='https://wiki.zidrave.net/'  class='link-link' target='_blank'><b>Wiki Zidrave</b></a> </p>
</footer>

<?php
function formatBytes($bytes,$precision=2){$units=['B','KB','MB','GB','TB'];$bytes=max($bytes,0);$pow=floor(($bytes?log($bytes):0)/log(1024));$pow=min($pow,count($units)-1);$bytes/= (1<< (10*$pow));return round($bytes,$precision).' '.$units[$pow];}
?>




</body>
