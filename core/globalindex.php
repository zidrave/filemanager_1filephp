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

// Cookie
$cookie_name = "file_manager_auth";
$cookie_duration = 3600; // 1 hora






// ==============================
// GESTIÓN DE TEMAS
// ==============================
//$available_themes = ['taringa', 'joomla'];
$available_themes = ['taringa', 'joomla', 'github', 'leonardo', 'filemanager'];
$default_theme = 'taringa';

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
// FUNCIONES DE TEMAS
// ==============================
function getThemeStyles($theme) {
    $styles = [
'filemanager' => "
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
        <a href="#" class="top-menu-item">🏠 Inicio</a>
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
        <strong>Ruta actual:</strong> <?php echo str_replace($baseDir,"",$targetDir) ?: "/"; ?>
    </div>
    <?php else: ?>
    <strong>Ruta actual:</strong> <?php echo str_replace($baseDir,"",$targetDir) ?: "/"; ?>
    <?php endif; ?>
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

<!-- Selector de Temas -->
<div class="theme-selector">
    <label style="font-size:12px; color:#666; display:block; margin-bottom:8px; font-weight:bold;">🎨 Tema:</label>
    <select onchange="window.location.href='?change_theme='+this.value">
        <option value="taringa" <?php echo $current_theme === 'taringa' ? 'selected' : ''; ?>>Taringa</option>
        <option value="joomla" <?php echo $current_theme === 'joomla' ? 'selected' : ''; ?>>Joomla</option>
        <option value="github" <?php echo $current_theme === 'github' ? 'selected' : ''; ?>>Github</option>
        <option value="filemanager" <?php echo $current_theme === 'filemanager' ? 'selected' : ''; ?>>File Manager</option>
        <option value="leonardo" <?php echo $current_theme === 'leonardo' ? 'selected' : ''; ?>>Leonardo</option>

    </select>
</div>

<footer>
    <p class="copyright">© <?php echo date("Y"); ?> zIDLAB Corporation - Todos los derechos reservados</p>
    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEicRrhs4L2BvhDfxiyrZGCWUYcCiDrKTOskZSwIsjvVZx7AQMNG6huy2DoX0An7ywtr8iOxm26Qo2r03DBLcHNCCMV67sC2e9Cvj5wqQHtibqCBZEC2X-0A9Rh3sb9TTlj8M_lpuZb_4hziIPBE-2Zh54Ie6O1cF5Is-hLHKVeSxSz_tJDc3J0jC_UDkg8/s320/logoskull2.png" alt="Logo" />
    <p style="font-size:12px; opacity:0.8;">Explorador de Carpetas</p>
</footer>

<?php
function formatBytes($bytes,$precision=2){$units=['B','KB','MB','GB','TB'];$bytes=max($bytes,0);$pow=floor(($bytes?log($bytes):0)/log(1024));$pow=min($pow,count($units)-1);$bytes/= (1<< (10*$pow));return round($bytes,$precision).' '.$units[$pow];}
?>
</body>
