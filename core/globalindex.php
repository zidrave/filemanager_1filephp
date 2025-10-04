<?php
// ==============================
// CONFIGURACIÓN
// ==============================

// 1 = pedirá contraseña, 0 = acceso libre
$versinclave = 1;  

// 0 = clave simple, 1 = clave avanzada con hash (En esta opcion tienes q crear tu hash ejem: $2y$12$RcgZxApBg/cXAcpXcaZ0QuUf3hBjmcl4bZ....)
$passwordadvance = 1;  

// Modo básico (clave visible)
$password = "1111";

// Modo avanzado (clave hasheada con bcrypt)
$password_hashed = '$2y$12$RcgZxApBg/cXAcpXcaZ0QuUf3hBjmcl4bZbonIQvWLyK4.0E0hjrO'; 
// corresponde a la clave "*******" //ahora es secreta pero puedes crear la tuya con: 
// echo password_hash("tuclave_nueva", PASSWORD_DEFAULT);



// Detectar dominio y subdominio para poner configuracion personalizada para cada subdominio o dominio
$host = $_SERVER['HTTP_HOST']; // Esto devuelve "subdominio.dominio.com" o "dominio.com"
// Verificar si $host es exactamente "files.zidrave.net"
if ($host === "files.zidrave.net") {
//reglas especiales para un tipo de subdominio
//$password_hashed = '$2y$12$RcgZxApBg/cXAcpXcaZ0QuUf3hBjmcl4bZbonIQvWLyK4.0E0hjrO'; //otro password para este subdominio o dominio
$versinclave = 0;  // 0 acceso libre sin clave o poner clave y clave personalizada para cada dominio o subdominio
}


////buscar PLugin ( GI-SECURITY.PHP ) para personalizar configuracion en carpetas diferentes
$gisFile = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $_SERVER['REQUEST_URI'] . 'gi-security.php';
$gisFile = str_replace("%20"," ",$gisFile); //falta mejorar pero este detalle daba problemas con carpetas con espacios
if (!file_exists($gisFile)) {
//echo "no existe aun -  $gisFile<br>";
} else {
//echo "archivo de seguridad encontrado! $gisFile<br>";
include("$gisFile");

}





// Cookie
$cookie_name = "file_manager_auth";
$cookie_duration = 3600 * 24 * 7; // 1 hora -cambiado a 1 semana

// ==============================





// ==============================
// GESTIÓN DE TEMAS
// ==============================
//$available_themes = ['taringa', 'joomla'];
$available_themes = ['taringa', 'joomla', 'github', 'leonardo', 'zidrave-skin'];
$default_theme = 'zidrave-skin';

// Cambiar tema
if (isset($_GET['change_theme']) && in_array($_GET['change_theme'], $available_themes)) {
//  setcookie('selected_theme', $_GET['change_theme'], time() + (86400 * 30), '/'); // era pa un mes
    setcookie('selected_theme', $_GET['change_theme'], time() + (86400 * 180), '/'); // para 6 meses
    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Obtener tema actual
$current_theme = isset($_COOKIE['selected_theme']) ? $_COOKIE['selected_theme'] : $default_theme;
if (!in_array($current_theme, $available_themes)) $current_theme = $default_theme;




// ==============================
// LOGIN CON COOKIE (SOLO SI $versinclave = 1)
// ==============================
$is_authenticated = true; // por defecto libre

if ($versinclave == 1) {
    $is_authenticated = false;

    if (isset($_POST['password'])) {
        $input_pass = $_POST['password'];
        $auth_ok = false;

        if ($passwordadvance == 0) {
            if ($input_pass === $password) $auth_ok = true;
        } else {
            if (password_verify($input_pass, $password_hashed)) $auth_ok = true;
        }

        if ($auth_ok) {
            $cookie_val = ($passwordadvance == 0) ? hash('sha256', $password) : hash('sha256', $password_hashed);
            setcookie($cookie_name, $cookie_val, time() + $cookie_duration, '/');
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            $login_error = "Contraseña incorrecta";
        }
    }

    // Verificación de cookie
    $expected_cookie = ($passwordadvance == 0) ? hash('sha256', $password) : hash('sha256', $password_hashed);

    $is_authenticated = isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] === $expected_cookie;
}





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
if ($versinclave == 1 && !$is_authenticated):
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
  <h2>🔒 Login </h2>
  <form method="post">
    <input type="password" name="password" placeholder="Contraseña" required autofocus>
    <button type="submit">Entrar</button>
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
// FUNCIONES DE TEMAS
// ==============================
function getThemeStyles($theme) {
    $styles = [
'zidrave-skin' => "
* {margin:0;padding:0;box-sizing:border-box;}
body {font-family: Arial, sans-serif; background:#080c11; color:#e0e1dd; min-height:100vh;}
header {background:linear-gradient(180deg,#263c5e 0%, #071f31 100%); color:#fff; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #415a77;}
.title {font-size:18px; font-weight:bold;}
.logout-btn {background:#415a77; color:#fff; border:none; padding:8px 15px; cursor:pointer; border-radius:5px; text-decoration:none;}
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
.file-link {color:#e0e1dd; text-decoration:none; display:flex; align-items:center; gap:6px;}
.file-link:hover {color:#00fbff;}
.file-icon {font-size:14px;}
.info-box {background:#031a30; border:1px solid #415a77; padding:15px; margin-bottom:20px; border-left:4px solid #778da9; border-radius:5px;}
.info-box strong {color:#edf6f9;}
footer {background:#061c32; color:#aaa; text-align:center; padding:20px; margin-top:30px; border-top:2px solid #1e3a5f;}
footer img { width:100px; opacity:0.7; }
.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:15px;}
.stat-item {background:#192733; padding:12px; border-radius:8px; color:#37bfe1;}
.stat-label {font-size:13px; color:#37bfe1; solid #1e3a5f; text-transform:uppercase;}
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
    font-size: 12px;
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
    background: linear-gradient(45deg, #e94560, #ff6f91);
    color: #ffffff;
    border: none;
    padding: 6px 15px;
    cursor: pointer;
    border-radius: 4px;
    text-transform: uppercase;
    font-size: 11px;
    font-weight: bold;
    box-shadow: 0 0 8px rgba(233,69,96,0.5);
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
    font-size: 12px;
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
    background: #f85149;      /* rojo de acento tipo GitHub */
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
    background: #ea3636;
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
.file-link {
    color: #58a6ff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}
.file-link:hover {
    color: #79c0ff;
}
.file-icon { font-size: 14px; }
.info-box {
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
.info-box p {margin:8px 0; color:#666; font-size:14px;}
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
.file-link {color:#2970b3; text-decoration:none; display:flex; align-items:center; gap:10px; font-weight:500;}
.file-link:hover {text-decoration:underline;}
.file-icon {font-size:20px;}
.stats-grid {display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:30px;}
.stat-item {background:#fff; padding:20px 25px; border-radius:4px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border-left:4px solid #2970b3;}
.stat-label {color:#666; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;}
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
<?php echo getThemeStyles($current_theme); ?>
</style>
</head>
<body>

<?php if ($current_theme === 'joomla'): ?>
<nav class="top-nav">
    <div class="top-menu">
        <a href="/" class="top-menu-item">🏠 Inicio</a>
        <a href="#" class="top-menu-item">📁 Explorador</a>
        <a href="#" class="top-menu-item">⚙️ Configuración</a>
        <a href="#" class="top-menu-item">👤 Usuario</a>
    </div>
</nav>
<?php endif; ?>

<header>
    <?php if ($current_theme === 'joomla'): ?>
    <div class="header-content">
        <h1 class="title">Explorador de Archivos</h1>
   <?php if ($versinclave == 1): ?>
        <a href="?logout" class="logout-btn">Cerrar Sesión x</a>
    <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="title"><span class="folder-icon">📁</span> Explorador de Archivos</div>
       <?php if ($versinclave == 1): ?>
    <a href="?logout" class="logout-btn">Cerrar Sesión</a>
       <?php endif; ?>
    <?php endif; ?>
</header>

<div class="breadcrumb <?php echo $current_theme === 'joomla' ? '' : ''; ?>">
    <?php if ($current_theme === 'joomla'): ?>
    <div class="breadcrumb-content">
   <h2> 🌎 <?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>  </h2> 
    </div>
    <?php else: ?>
   <h2> 🌎 <?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>  </h2> 
    <?php endif; ?>
</div>

<div class="main-content">
<div class="info-box">
    <p><strong>📂 Directorio actual:</strong> <?php echo str_replace($baseDir,"",$targetDir) ?: "/"; ?></p>
    <p><strong>📊 Elementos encontrados:</strong> <?php echo $file_count; ?> archivos en esta carpeta</p>
</div>











<div id="txt-viewer"  style="display:none;">
 
<div style="padding:10px;">
    <div style="text-align:right; margin-top:0px;">
        <button id="close-txt2" class="logout-btn">Cerrar</button>
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
        <button id="close-txt" class="logout-btn">Cerrar</button>
    </div>
</div>
<br>
</div>



<?php
///////////////////////// README.MD ///////////////////////////////
$mdFile = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $_SERVER['REQUEST_URI'] . '/readme.md';
//$mdFile = __DIR__ . $_SERVER['REQUEST_URI'].'/readme.md';
//$mdFile = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/readme.md';
//echo "Buscando: $mdFile";
if (!file_exists($mdFile)) {
//    http_response_code(404);
//    echo "<h1>readme.md no encontrado</h1> <p>Coloca un archivo  readme.md en esta carpeta.</p>";
//    exit;
} else {

$md = file_get_contents($mdFile);

// Escapamos primero para evitar inyección; aplicaremos transformaciones sobre texto seguro.
$md = htmlspecialchars($md, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

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
$md = preg_replace('/^[ \t]*(-{3,}|\*{3,}|_{3,})[ \t]*$/m', '<hr style="border:none; height:1px; background:#415a77; margin:20px 0;">', $md);

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
    echo "<tr><td colspan='4'><a href='$parent' class='file-link'><span class='file-icon'>🔺</span> Subir al directorio anterior</a></td></tr>";
}

// Archivos
foreach ($files as $file) {
    if ($file === "." || $file === "..") continue;
    $fullPath = $targetDir."/".$file;
    $isDir = is_dir($fullPath);
    $type = $isDir ? "Directorio" : "Archivo";
    $size = $isDir ? "-" : formatBytes(filesize($fullPath));
    $modTime = date("d/m/Y H:i:s", filemtime($fullPath));
//  $icon = $isDir ? "📁" : "📄";

    // Extensión en minúsculas
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

$icon = "📄"; 
if ($isDir) {
    $icon = "📁";
} elseif (in_array($ext, ["jpg","jpeg","png","gif","webp"])) {
    $icon = "🖼️";
} elseif (in_array($ext, ["mp3","wav","ogg"])) {
    $icon = "🎵";
} elseif (in_array($ext, ["mp4","mkv","avi"])) {
    $icon = "🎬";
} elseif (in_array($ext, ["zip","rar","7z","tar","gz"])) {
    $icon = "📦";
} elseif (in_array($ext, ["php","html","css","js","py","sh"])) {
    $icon = "💻";
}
    $link = "/".ltrim(($requestedPath==="."?"":$requestedPath."/").$file,"./");
    if($isDir) $link.="/";
    echo "<tr>";
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); //ahora trabaja con extensiones en mayuscula

// Definimos extensiones que se mostrarán como "texto visualizable"
$textExts = ["txt", "log", "md", "ini", "cfg", "json", "xml", "csv"];
// Definimos extensiones que se mostrarán como imágenes
$imageExts = ["jpg", "jpeg", "png", "gif", "webp", "bmp", "svg"];

if (in_array($ext, $textExts)) {
    echo "<td><a href='' class='file-link txt-link' data-file='".htmlspecialchars($link)."'><span class='file-icon'>$icon</span> $file 🔹​​ </a></td>";
} elseif (in_array($ext, $imageExts)) {
    // Para imágenes agregamos la clase image-link y el contenedor lo manejará con JS
    echo "<td><a href='' class='file-link image-link' data-file='".htmlspecialchars($link)."'><span class='file-icon'>$icon</span> $file 🔹</a></td>";
} else {
    echo "<td><a href='$link' class='file-link'><span class='file-icon'>$icon</span> $file</a></td>";
}

    echo "<td>$type</td>";
    echo "<td>$size</td>";
    echo "<td>$modTime</td>";
    echo "</tr>";
}



// Subir al padre
if ($requestedPath !== "." && $requestedPath !== "") {
    $parent = dirname($requestedPath);
    $parent = $parent === "." ? "/" : "/" . $parent . "/";
    echo "<tr><td colspan='4'><a href='$parent' class='file-link'><span class='file-icon'>🔺</span> Subir al directorio anterior</a></td></tr>";
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

<!-- Selector de Temas -->
<div class="theme-selector">
    <label style="font-size:12px; color:#666; display:block; margin-bottom:8px; font-weight:bold;">🎨 Tema:</label>
    <select onchange="window.location.href='?change_theme='+this.value">
        <option value="zidrave-skin" <?php echo $current_theme === 'zidrave-skin' ? 'selected' : ''; ?>>Zidrave Skin</option>
        <option value="taringa" <?php echo $current_theme === 'taringa' ? 'selected' : ''; ?>>Taringa</option>
        <option value="joomla" <?php echo $current_theme === 'joomla' ? 'selected' : ''; ?>>Joomla</option>
        <option value="github" <?php echo $current_theme === 'github' ? 'selected' : ''; ?>>Github</option>
        <option value="leonardo" <?php echo $current_theme === 'leonardo' ? 'selected' : ''; ?>>Leonardo</option>

    </select>
</div>



<script>


document.querySelectorAll('.image-link').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        const file = link.dataset.file;
        const modal = document.getElementById('image-modal');
        modal.innerHTML = `<img src="${file}" style="max-width:90%; max-height:90%;">`;
        modal.style.display = 'flex';
    });
});

// Para cerrar el modal
document.getElementById('image-modal').addEventListener('click', () => {
    document.getElementById('image-modal').style.display = 'none';
});




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

fetch(file)
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
    <p class="copyright">© <?php echo date("Y"); ?> zIDLAB Corporation - Todos los derechos reservados</p>
    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEicRrhs4L2BvhDfxiyrZGCWUYcCiDrKTOskZSwIsjvVZx7AQMNG6huy2DoX0An7ywtr8iOxm26Qo2r03DBLcHNCCMV67sC2e9Cvj5wqQHtibqCBZEC2X-0A9Rh3sb9TTlj8M_lpuZb_4hziIPBE-2Zh54Ie6O1cF5Is-hLHKVeSxSz_tJDc3J0jC_UDkg8/s320/logoskull2.png" alt="Logo" />
    <p style="font-size:12px; opacity:0.8;">Explorador de Carpetas de Zidrave - <a href='https://zidrave.net/?p=4641'  class='stat-label' target='_black'><b>Ver Proyecto</b></a></p>
</footer>

<?php
function formatBytes($bytes,$precision=2){$units=['B','KB','MB','GB','TB'];$bytes=max($bytes,0);$pow=floor(($bytes?log($bytes):0)/log(1024));$pow=min($pow,count($units)-1);$bytes/= (1<< (10*$pow));return round($bytes,$precision).' '.$units[$pow];}
?>




</body>


