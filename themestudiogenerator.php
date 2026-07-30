<?php
/**
 * Theme Studio Generator for File4 Manager
 * Compatible con file4.php v4.4.7.x
 * Coloca este archivo en la MISMA carpeta donde está file4.php
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['css'], $_POST['name'])) {
    header('Content-Type: application/json');
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '', trim($_POST['name']));
    if (empty($name) || strlen($name) > 40) {
        echo json_encode(['ok' => false, 'error' => 'Nombre inválido. Usa solo letras, números, guiones y guiones bajos (máx 40).']);
        exit;
    }
    $filename = 'fmstyle_' . $name . '.css';
    $filepath = __DIR__ . '/' . $filename;
    if (file_put_contents($filepath, $_POST['css'], LOCK_EX) !== false) {
        echo json_encode(['ok' => true, 'file' => $filename]);
    } else {
        echo json_encode(['ok' => false, 'error' => 'No se pudo escribir el archivo. Verifica permisos de escritura.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Theme Studio Generator for File4</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Fira+Code:wght@400&display=swap" rel="stylesheet">
<style>
:root {
  --s-bg:#0f1115; --s-panel:#181b21; --s-border:#2a2e36;
  --s-text:#e2e5e9; --s-muted:#8b929d; --s-accent:#3b82f6;
  --s-ok:#22c55e; --s-bad:#ef4444; --s-warn:#f59e0b;
}
* { box-sizing:border-box; }
body { margin:0; padding:0; font-family:'Inter',sans-serif; background:var(--s-bg); color:var(--s-text); height:100vh; overflow:hidden; display:flex; flex-direction:column; }

/* ── HEADER ── */
.studio-header { background:var(--s-panel); border-bottom:1px solid var(--s-border); padding:10px 20px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-shrink:0; }
.studio-header h1 { margin:0; font-size:16px; display:flex; align-items:center; gap:8px; }
.badge { background:var(--s-accent); color:#fff; font-size:10px; padding:2px 7px; border-radius:10px; text-transform:uppercase; letter-spacing:.5px; }
.kbd { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); border-radius:4px; padding:2px 6px; font-size:10px; font-family:'Fira Code',monospace; color:var(--s-muted); }

/* ── LAYOUT ── */
.studio-main { display:flex; flex:1; overflow:hidden; }
.sidebar { width:320px; background:var(--s-panel); border-right:1px solid var(--s-border); display:flex; flex-direction:column; overflow:hidden; }
.sidebar-scroll { flex:1; overflow-y:auto; padding:16px; }
.sidebar-footer { padding:12px 16px; border-top:1px solid var(--s-border); background:rgba(0,0,0,.15); }
.preview-area { flex:1; overflow:auto; padding:20px; background:#0a0c10; }

/* ── SECTION TITLES ── */
.section-title { font-size:10px; text-transform:uppercase; letter-spacing:1px; color:var(--s-muted); margin-bottom:8px; margin-top:2px; display:flex; align-items:center; gap:6px; }
.section-title::after { content:''; flex:1; height:1px; background:var(--s-border); }

/* ── BUTTONS ── */
.btn { border:none; padding:8px 12px; border-radius:7px; cursor:pointer; font-weight:600; font-size:12px; transition:all .15s; display:inline-flex; align-items:center; justify-content:center; gap:6px; width:100%; margin-bottom:8px; font-family:'Inter',sans-serif; }
.btn:disabled { opacity:.4; cursor:not-allowed; }
.btn-dark    { background:linear-gradient(135deg,#6366f1,#a855f7); color:#fff; box-shadow:0 3px 10px rgba(168,85,247,.25); }
.btn-dark:hover:not(:disabled)    { transform:translateY(-1px); box-shadow:0 5px 14px rgba(168,85,247,.35); }
.btn-light   { background:linear-gradient(135deg,#f59e0b,#f97316); color:#fff; box-shadow:0 3px 10px rgba(249,115,22,.25); }
.btn-light:hover:not(:disabled)   { transform:translateY(-1px); box-shadow:0 5px 14px rgba(249,115,22,.35); }
.btn-save    { background:var(--s-ok); color:#fff; }
.btn-save:hover:not(:disabled)    { filter:brightness(1.1); }
.btn-copy    { background:var(--s-border); color:var(--s-text); }
.btn-copy:hover:not(:disabled)    { background:#3a3f4a; }
.btn-undo    { background:transparent; border:1px solid var(--s-border); color:var(--s-muted); width:auto; padding:6px 10px; margin:0; }
.btn-undo:hover:not(:disabled)    { border-color:var(--s-accent); color:var(--s-accent); }
.btn-import  { background:transparent; border:1px solid var(--s-border); color:var(--s-muted); }
.btn-import:hover { border-color:var(--s-warn); color:var(--s-warn); }

/* ── PRESET CHIPS ── */
.preset-chips { display:flex; flex-wrap:wrap; gap:5px; margin-bottom:12px; }
.chip { border:none; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600; cursor:pointer; transition:all .15s; font-family:'Inter',sans-serif; }
.chip:hover { transform:translateY(-1px); opacity:.9; }

/* ── COLOR GRID ── */
.color-grid { display:grid; grid-template-columns:1fr 1fr; gap:7px; margin-bottom:14px; }
.color-item { background:rgba(0,0,0,.25); border:1px solid var(--s-border); border-radius:7px; padding:6px 7px; display:flex; align-items:center; gap:6px; position:relative; transition:border-color .2s; }
.color-item.locked { border-color:var(--s-warn); }
.color-item label { font-size:10px; color:var(--s-muted); flex:1; cursor:pointer; user-select:none; line-height:1.2; }
.color-item input[type="color"] { width:26px; height:26px; border:none; border-radius:5px; cursor:pointer; background:none; padding:0; flex-shrink:0; }
.color-item input[type="text"]  { width:62px; background:transparent; border:1px solid var(--s-border); color:var(--s-text); border-radius:4px; padding:2px 5px; font-size:10px; font-family:'Fira Code',monospace; }
.lock-btn { position:absolute; top:4px; right:4px; background:none; border:none; cursor:pointer; font-size:10px; opacity:.35; transition:opacity .15s; padding:0; line-height:1; }
.lock-btn:hover, .color-item.locked .lock-btn { opacity:1; }
.contrast-badge { position:absolute; bottom:3px; right:5px; font-size:8px; font-weight:700; border-radius:3px; padding:1px 4px; }
.contrast-badge.ok  { background:rgba(34,197,94,.2);  color:#22c55e; }
.contrast-badge.warn { background:rgba(245,158,11,.2); color:#f59e0b; }
.contrast-badge.bad  { background:rgba(239,68,68,.2);  color:#ef4444; }

/* ── INPUT ── */
.input-group { margin-bottom:10px; }
.input-group label { display:block; font-size:11px; color:var(--s-muted); margin-bottom:5px; }
.input-group input[type="text"] { width:100%; background:rgba(0,0,0,.2); border:1px solid var(--s-border); color:var(--s-text); border-radius:6px; padding:8px 10px; font-size:12px; font-family:'Inter',sans-serif; }
.input-group input[type="text"]:focus { outline:none; border-color:var(--s-accent); }

/* ── SAVED THEMES ── */
.saved-list { display:flex; flex-wrap:wrap; gap:5px; margin-bottom:10px; }
.saved-slot { position:relative; width:28px; height:28px; border-radius:6px; border:2px solid transparent; cursor:pointer; transition:border-color .15s; overflow:hidden; }
.saved-slot:hover { border-color:var(--s-text); }
.saved-slot .slot-preview { width:100%; height:100%; display:flex; flex-direction:column; }
.saved-slot .slot-half { flex:1; }
.saved-slot .slot-del { display:none; position:absolute; inset:0; background:rgba(239,68,68,.85); font-size:10px; align-items:center; justify-content:center; color:#fff; font-weight:700; }
.saved-slot:hover .slot-del { display:flex; }
.saved-slot.empty { border:2px dashed var(--s-border); background:rgba(255,255,255,.03); }
.saved-slot.empty::after { content:'+'; position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:var(--s-muted); font-size:14px; }
.saved-slot.empty:hover .slot-del { display:none; }

/* ── CSS OUTPUT ── */
.css-output { display:none; margin-top:10px; background:rgba(0,0,0,.3); border:1px solid var(--s-border); border-radius:7px; padding:10px; font-family:'Fira Code',monospace; font-size:10px; color:var(--s-muted); max-height:180px; overflow:auto; white-space:pre; }
.css-output.on { display:block; }
.toggle-row { display:flex; gap:6px; margin-bottom:8px; align-items:center; }
.toggle-css { background:none; border:1px solid var(--s-border); color:var(--s-muted); padding:5px 10px; border-radius:5px; cursor:pointer; font-size:11px; width:auto; transition:all .15s; font-family:'Inter',sans-serif; }
.toggle-css:hover { color:var(--s-text); border-color:var(--s-text); }

/* ── TOAST ── */
.toast { position:fixed; bottom:20px; right:20px; background:var(--s-panel); color:#fff; padding:10px 16px; border-radius:8px; border:1px solid var(--s-border); box-shadow:0 10px 30px rgba(0,0,0,.4); z-index:9999; transform:translateY(100px); opacity:0; transition:all .3s ease; display:flex; align-items:center; gap:8px; font-size:13px; max-width:320px; }
.toast.show { transform:translateY(0); opacity:1; }
.toast.ok  { border-left:4px solid var(--s-ok); }
.toast.bad { border-left:4px solid var(--s-bad); }
.toast.info { border-left:4px solid var(--s-accent); }

/* ── PREVIEW ── */
.preview-label { text-align:center; color:var(--s-muted); font-size:11px; margin-bottom:10px; letter-spacing:1px; text-transform:uppercase; }
.preview-frame { max-width:980px; margin:0 auto; border-radius:10px; overflow:hidden; border:1px solid var(--s-border); box-shadow:0 20px 60px rgba(0,0,0,.5); }

/* ── IMPORT MODAL ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.72); z-index:1000; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal { background:var(--s-panel); border:1px solid var(--s-border); border-radius:12px; padding:22px; width:560px; max-width:94vw; }
.modal h3 { margin:0 0 14px; font-size:15px; display:flex; align-items:center; gap:8px; }
.modal-tabs { display:flex; gap:0; margin-bottom:14px; border:1px solid var(--s-border); border-radius:8px; overflow:hidden; }
.modal-tab { flex:1; background:transparent; border:none; padding:8px; font-size:12px; font-weight:600; color:var(--s-muted); cursor:pointer; font-family:'Inter',sans-serif; transition:all .15s; }
.modal-tab.active { background:var(--s-accent); color:#fff; }
.modal-tab:not(.active):hover { background:rgba(255,255,255,.05); color:var(--s-text); }
.tab-panel { display:none; }
.tab-panel.active { display:block; }
.modal textarea { width:100%; height:160px; background:rgba(0,0,0,.3); border:1px solid var(--s-border); color:var(--s-text); border-radius:7px; padding:10px; font-family:'Fira Code',monospace; font-size:11px; resize:vertical; }
.modal textarea:focus { outline:none; border-color:var(--s-accent); }
/* Drag & Drop zone */
.drop-zone { border:2px dashed var(--s-border); border-radius:10px; padding:28px 20px; text-align:center; cursor:pointer; transition:all .2s; background:rgba(0,0,0,.2); }
.drop-zone:hover, .drop-zone.drag-over { border-color:var(--s-accent); background:rgba(59,130,246,.07); }
.drop-zone .dz-icon { font-size:32px; margin-bottom:8px; }
.drop-zone .dz-text { font-size:13px; color:var(--s-muted); }
.drop-zone .dz-text strong { color:var(--s-text); }
.drop-zone input[type="file"] { display:none; }
.file-loaded { margin-top:10px; background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.3); border-radius:7px; padding:8px 12px; font-size:12px; color:var(--s-ok); display:none; align-items:center; gap:8px; }
.file-loaded.show { display:flex; }
.modal-btns { display:flex; gap:8px; margin-top:14px; flex-wrap:wrap; }
.modal-btns button { flex:1; min-width:120px; }

/* ── CSS EDITOR PANEL (full-screen overlay) ── */
.css-editor-overlay { display:none; position:fixed; inset:0; background:#0a0c10; z-index:2000; flex-direction:column; }
.css-editor-overlay.open { display:flex; }
.css-editor-header { background:var(--s-panel); border-bottom:1px solid var(--s-border); padding:10px 18px; display:flex; align-items:center; gap:10px; flex-shrink:0; flex-wrap:wrap; }
.css-editor-header h2 { margin:0; font-size:14px; flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.ced-btn { border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; font-family:'Inter',sans-serif; transition:all .15s; display:inline-flex; align-items:center; gap:5px; white-space:nowrap; }
.ced-btn-primary  { background:var(--s-accent); color:#fff; }
.ced-btn-primary:hover  { filter:brightness(1.15); }
.ced-btn-ok       { background:var(--s-ok);    color:#fff; }
.ced-btn-ok:hover       { filter:brightness(1.1); }
.ced-btn-warn     { background:var(--s-warn);  color:#000; }
.ced-btn-warn:hover     { filter:brightness(1.1); }
.ced-btn-ghost    { background:var(--s-border); color:var(--s-text); }
.ced-btn-ghost:hover    { background:#3a3f4a; }
.ced-btn-close    { background:rgba(239,68,68,.15); color:var(--s-bad); border:1px solid rgba(239,68,68,.3); }
.ced-btn-close:hover    { background:var(--s-bad); color:#fff; }
.css-editor-body { flex:1; overflow:hidden; display:flex; }
.css-editor-nums { background:#0d1017; color:#4b5563; font-family:'Fira Code',monospace; font-size:13px; line-height:1.6; padding:10px 10px 10px 14px; text-align:right; user-select:none; overflow:hidden; border-right:1px solid var(--s-border); min-width:52px; white-space:pre; }
.css-editor-ta { flex:1; background:#0d1017; color:#e2e5e9; font-family:'Fira Code',monospace; font-size:13px; line-height:1.6; border:none; outline:none; padding:10px 14px; resize:none; overflow-y:scroll; white-space:pre; overflow-x:auto; tab-size:2; }
.css-editor-statusbar { background:var(--s-panel); border-top:1px solid var(--s-border); padding:5px 18px; display:flex; align-items:center; gap:16px; font-size:11px; color:var(--s-muted); flex-shrink:0; }
.css-editor-statusbar span { display:flex; align-items:center; gap:4px; }
</style>
</head>
<body>

<div class="studio-header">
  <h1>
    🎨 Theme Studio <span style="color:var(--s-muted);font-weight:400;">Generator</span>
    <span class="badge">File4 Compatible</span>
  </h1>
  <div style="display:flex;align-items:center;gap:12px;">
    <span style="color:var(--s-muted);font-size:11px;">
      <span class="kbd">Space</span> regenerar &nbsp;
      <span class="kbd">Ctrl+S</span> guardar &nbsp;
      <span class="kbd">Ctrl+Z</span> deshacer
    </span>
    <button class="btn btn-undo" id="undoBtn" onclick="undo()" disabled title="Deshacer última generación">↩ Undo</button>
  </div>
</div>

<div class="studio-main">
  <aside class="sidebar">
    <div class="sidebar-scroll">

      <!-- PRESETS DARK -->
      <div class="section-title">Presets Dark</div>
      <div class="preset-chips" id="presetsD"></div>

      <!-- PRESETS LIGHT -->
      <div class="section-title">Presets Light</div>
      <div class="preset-chips" id="presetsL"></div>

      <!-- GENERADORES ALEATORIOS -->
      <div class="section-title">Generadores Aleatorios</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
        <button class="btn btn-dark" onclick="generateTheme('dark')">🌑 Dark</button>
        <button class="btn btn-light" onclick="generateTheme('light')">☀️ Light</button>
      </div>

      <!-- PALETA -->
      <div class="section-title">Paleta (20 variables) <span style="font-size:9px;color:var(--s-muted);">🔒 = protegida</span></div>
      <div class="color-grid" id="colorGrid"></div>

      <!-- CSS -->
      <div class="toggle-row">
        <button class="toggle-css" onclick="document.getElementById('cssOutput').classList.toggle('on')">{ } Ver CSS</button>
        <button class="btn-import btn" style="width:auto;margin:0;padding:5px 10px;font-size:11px;" onclick="openImport()">⬆ Importar CSS</button>
      </div>
      <pre class="css-output" id="cssOutput"></pre>

      <!-- SAVED THEMES -->
      <div class="section-title">Guardados (localStorage)</div>
      <div class="saved-list" id="savedList"></div>
      <div class="section-title">Borrar con Click Derecho</div>

    </div>

    <div class="sidebar-footer">
      <div class="input-group">
        <label>Nombre del theme (para guardar como archivo)</label>
        <input type="text" id="themeName" placeholder="ej: neon-cyber" maxlength="40">
      </div>
      <button class="btn btn-save" onclick="saveTheme()">💾 Guardar en servidor</button>
      <button class="btn btn-copy" onclick="copyCSS()">📋 Copiar CSS</button>
    </div>
  </aside>

  <main class="preview-area">
    <div class="preview-label">Vista previa en tiempo real — <span id="previewModeBadge">dark</span></div>
    <div class="preview-frame" id="previewFrame"></div>
  </main>
</div>

<div class="toast" id="toast"></div>

<!-- ═══════════════ IMPORT MODAL ═══════════════ -->
<div class="modal-overlay" id="importModal">
  <div class="modal">
    <h3>⬆ Importar CSS</h3>

    <!-- Tabs -->
    <div class="modal-tabs">
      <button class="modal-tab active" id="tabPasteBtn" onclick="switchImportTab('paste')">📋 Pegar CSS</button>
      <button class="modal-tab"        id="tabUploadBtn" onclick="switchImportTab('upload')">📁 Subir archivo .css</button>
    </div>

    <!-- Tab: Pegar -->
    <div class="tab-panel active" id="tabPaste">
      <p style="color:var(--s-muted);font-size:11px;margin-bottom:8px;">Pega el contenido de un <code>fmstyle_*.css</code> para extraer la paleta y editarlo.</p>
      <textarea id="importCss" placeholder=":root {&#10;  --primary: #6366f1;&#10;  --bg-main: #0f1115;&#10;  ...&#10;}&#10;body { ... }"></textarea>
    </div>

    <!-- Tab: Subir archivo -->
    <div class="tab-panel" id="tabUpload">
      <div class="drop-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
        <div class="dz-icon">📂</div>
        <div class="dz-text"><strong>Arrastra un .css aquí</strong><br>o haz clic para seleccionar</div>
        <input type="file" id="fileInput" accept=".css,text/css">
      </div>
      <div class="file-loaded" id="fileLoaded">
        <span>✅</span>
        <span id="fileLoadedName">archivo.css</span>
        <span style="margin-left:auto;opacity:.6;" id="fileLoadedSize"></span>
      </div>
    </div>

    <!-- Acciones -->
    <div class="modal-btns">
      <button class="btn btn-save"   onclick="doImportPalette()">🎨 Extraer paleta</button>
      <button class="btn btn-dark"   onclick="doImportEditor()">✏️ Abrir editor CSS</button>
      <button class="btn btn-copy"   onclick="closeImport()">✕ Cancelar</button>
    </div>
  </div>
</div>

<!-- ═══════════════ CSS EDITOR PANEL ═══════════════ -->
<div class="css-editor-overlay" id="cssEditorOverlay">
  <div class="css-editor-header">
    <h2>✏️ Editor CSS Completo — <span id="cedFileName">sin nombre</span></h2>
    <button class="ced-btn ced-btn-primary" onclick="cedReExtract()">🎨 Re-extraer paleta</button>
    <button class="ced-btn ced-btn-warn"    onclick="cedGenerateVariant()">🎲 Generar variante</button>
    <button class="ced-btn ced-btn-ok"      onclick="cedApplyPreview()">▶ Aplicar preview</button>
    <button class="ced-btn ced-btn-ghost"   onclick="cedCopy()">📋 Copiar</button>
    <button class="ced-btn ced-btn-ok"      onclick="cedSave()">💾 Guardar</button>
    <button class="ced-btn ced-btn-close"   onclick="closeCssEditor()">✕ Cerrar editor</button>
  </div>
  <div class="css-editor-body">
    <div class="css-editor-nums" id="cedLineNums">1</div>
    <textarea class="css-editor-ta" id="cedTextarea"
              spellcheck="false"
              placeholder="/* Aquí aparecerá el CSS importado o generado */"
              oninput="cedOnInput()"
              onscroll="cedSyncScroll()"></textarea>
  </div>
  <div class="css-editor-statusbar">
    <span>📄 <span id="cedLineCount">0</span> líneas</span>
    <span>🔤 <span id="cedCharCount">0</span> caracteres</span>
    <span id="cedStatus" style="margin-left:auto;"></span>
    <span style="opacity:.5;">Tab = 2 espacios &nbsp;|&nbsp; Ctrl+S = guardar &nbsp;|&nbsp; Esc = cerrar</span>
  </div>
</div>

<script>
// ═══════════════════════════════════════════════════════
//  20 VARIABLES DEL THEME
// ═══════════════════════════════════════════════════════
const themeVars = [
  {key:'--primary',            label:'Primary',         group:'brand'},
  {key:'--primary-dark',       label:'Primary Dark',    group:'brand'},
  {key:'--secondary',          label:'Secondary',       group:'brand'},
  {key:'--success',            label:'Success',         group:'status'},
  {key:'--danger',             label:'Danger',          group:'status'},
  {key:'--warning',            label:'Warning',         group:'status'},
  {key:'--bg-main',            label:'Bg Main',         group:'bg'},
  {key:'--bg-secondary',       label:'Bg Sec hover',          group:'bg'},
  {key:'--bg-secondary2',      label:'Bg Sec 2',        group:'bg'},
  {key:'--bg-card',            label:'Bg Card',         group:'bg'},
  {key:'--text-primary',       label:'Text',            group:'text'},
  {key:'--text-secondary',     label:'Text 2nd',        group:'text'},
  {key:'--border',             label:'Border',          group:'ui'},
  {key:'--hover',              label:'Hover',           group:'ui'},
  {key:'--navigation',         label:'Navigation',      group:'text'},
  {key:'--header-text',        label:'Header Text',     group:'text'},
  {key:'--table-header-text',  label:'Table Head Text', group:'text'},
  {key:'--editor-bg',          label:'Editor Bg',       group:'editor'},
  {key:'--editor-text',        label:'Editor Text',     group:'editor'},
  {key:'--link-hover',         label:'Link Hover',      group:'ui'},
  {key:'--link',             label:'Link',            group:'text'},
];

let currentColors = {};
let lockedKeys    = new Set();
let undoStack     = [];   // últimas 5 generaciones
const MAX_UNDO    = 5;
const MAX_SAVED   = 32;
const LS_KEY      = 'file4_saved_themes';

// ═══════════════════════════════════════════════════════
//  UTILS
// ═══════════════════════════════════════════════════════
function rand(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; }

// ✅ FIX BUG: el rojo en HSL está en 350–360 ∪ 0–15
//    rand(350, 10) estaba roto porque min > max → resultado negativo
function randRedHue() { return Math.random() < 0.5 ? rand(350, 360) : rand(0, 15); }

function hslToHex(h, s, l) {
  l /= 100; s /= 100;
  const a = s * Math.min(l, 1 - l);
  const f = n => {
    const k   = (n + h / 30) % 12;
    const col = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
    return Math.round(255 * col).toString(16).padStart(2, '0');
  };
  return '#' + f(0) + f(8) + f(4);
}

function hexToRgb(hex) {
  const r = parseInt(hex.slice(1,3),16);
  const g = parseInt(hex.slice(3,5),16);
  const b = parseInt(hex.slice(5,7),16);
  return {r,g,b};
}

// WCAG relative luminance
function luminance(hex) {
  const {r,g,b} = hexToRgb(hex);
  const [R,G,B] = [r,g,b].map(c => {
    c /= 255;
    return c <= 0.03928 ? c/12.92 : Math.pow((c+0.055)/1.055, 2.4);
  });
  return 0.2126*R + 0.7152*G + 0.0722*B;
}

function contrastRatio(hex1, hex2) {
  const l1 = luminance(hex1), l2 = luminance(hex2);
  const [light, dark] = l1 > l2 ? [l1,l2] : [l2,l1];
  return (light + 0.05) / (dark + 0.05);
}

function contrastLabel(ratio) {
  if (ratio >= 4.5) return {cls:'ok',  text:'AA'};
  if (ratio >= 3.0) return {cls:'warn', text:'AA–'};
  return                    {cls:'bad', text:'✗'};
}

// ═══════════════════════════════════════════════════════
//  PRESETS
// ═══════════════════════════════════════════════════════
const PRESETS = {
  // ── DARK ──
  'Midnight': {
    '--primary':'#6366f1','--primary-dark':'#312e81','--secondary':'#1e1b4b',
    '--success':'#22c55e','--danger':'#ef4444','--warning':'#f59e0b',
    '--bg-main':'#0f0e17','--bg-secondary':'#161521','--bg-secondary2':'#1a1928',
    '--bg-card':'#13121f','--text-primary':'#f0f0f5','--text-secondary':'#9b99b8',
    '--border':'#2e2b4a','--hover':'#818cf8','--navigation':'#ffffff',
    '--header-text':'#ffffff','--table-header-text':'#ffffff',
    '--editor-bg':'#111020','--editor-text':'#e0dff8','--link-hover':'#ef4444',
  },
  'Steam': {
    '--primary':'#1a9fff','--primary-dark':'#006cbf','--secondary':'#1b2838',
    '--success':'#4caf50','--danger':'#f44336','--warning':'#ff9800',
    '--bg-main':'#1b2838','--bg-secondary':'#2a475e','--bg-secondary2':'#16202d',
    '--bg-card':'#1e2d3d','--text-primary':'#c7d5e0','--text-secondary':'#8fa9bd',
    '--border':'#2a475e','--hover':'#66c0f4','--navigation':'#c7d5e0',
    '--header-text':'#c7d5e0','--table-header-text':'#ffffff',
    '--editor-bg':'#1b2838','--editor-text':'#c7d5e0','--link-hover':'#f44336',
  },
  'Neon': {
    '--primary':'#00ff88','--primary-dark':'#00a854','--secondary':'#0d1117',
    '--success':'#00ff88','--danger':'#ff003c','--warning':'#ffcc00',
    '--bg-main':'#070b0f','--bg-secondary':'#0d1117','--bg-secondary2':'#111820',
    '--bg-card':'#0a0f14','--text-primary':'#e0ffe0','--text-secondary':'#00cc66',
    '--border':'#00ff8844','--hover':'#00ffaa','--navigation':'#ffffff',
    '--header-text':'#000000','--table-header-text':'#000000',
    '--editor-bg':'#060a0e','--editor-text':'#00ff88','--link-hover':'#ff003c',
  },
  'Cyberpunk': {
    '--primary':'#f72585','--primary-dark':'#9e0059','--secondary':'#240046',
    '--success':'#06d6a0','--danger':'#ef233c','--warning':'#ffd166',
    '--bg-main':'#10002b','--bg-secondary':'#1a0040','--bg-secondary2':'#200050',
    '--bg-card':'#14003a','--text-primary':'#e0aaff','--text-secondary':'#c77dff',
    '--border':'#7b2fff44','--hover':'#b5179e','--navigation':'#ffffff',
    '--header-text':'#ffffff','--table-header-text':'#ffffff',
    '--editor-bg':'#0d0020','--editor-text':'#e0aaff','--link-hover':'#ef233c',
  },
  'Ocean': {
    '--primary':'#00b4d8','--primary-dark':'#0077b6','--secondary':'#023e58',
    '--success':'#26c485','--danger':'#e63946','--warning':'#f4a261',
    '--bg-main':'#03071e','--bg-secondary':'#051d30','--bg-secondary2':'#07253e',
    '--bg-card':'#04162a','--text-primary':'#caf0f8','--text-secondary':'#90e0ef',
    '--border':'#0096c740','--hover':'#48cae4','--navigation':'#ffffff',
    '--header-text':'#ffffff','--table-header-text':'#ffffff',
    '--editor-bg':'#021728','--editor-text':'#caf0f8','--link-hover':'#e63946',
  },
  'AMOLED': {
    '--primary':'#bb86fc','--primary-dark':'#6200ee','--secondary':'#1f1f1f',
    '--success':'#03dac6','--danger':'#cf6679','--warning':'#ffde03',
    '--bg-main':'#000000','--bg-secondary':'#121212','--bg-secondary2':'#1e1e1e',
    '--bg-card':'#0a0a0a','--text-primary':'#e1e1e1','--text-secondary':'#a0a0a0',
    '--border':'#2c2c2c','--hover':'#9965f4','--navigation':'#ffffff',
    '--header-text':'#ffffff','--table-header-text':'#ffffff',
    '--editor-bg':'#000000','--editor-text':'#e1e1e1','--link-hover':'#cf6679',
  },

'Zidcora': {
  '--primary':'#071117',
  '--primary-dark':'#06131e',
  '--secondary':'#16202c',
  '--success':'#4caf50',
  '--danger':'#f44336',
  '--warning':'#ff9800',
  '--bg-main':'#1b2838',
  '--bg-secondary':'#2a475e',
  '--bg-secondary2':'#16202d',
  '--bg-card':'#1e2d3d',
  '--text-primary':'#c7d5e0',
  '--text-secondary':'#8fa9bd',
  '--border':'#2a475e',
  '--hover':'#66c0f4',
  '--navigation':'#c7d5e0',
  '--header-text':'#c7d5e0',
  '--table-header-text':'#3fcac8',
  '--editor-bg':'#07111d',
  '--editor-text':'#c7d5e0',
  '--link-hover':'#00eeff',
  '--link':'#81b4bb'
},
  // ── LIGHT ──
  'GitHub': {
    '--primary':'#0969da','--primary-dark':'#0550ae','--secondary':'#e6edf3',
    '--success':'#1a7f37','--danger':'#cf222e','--warning':'#9a6700',
    '--bg-main':'#ffffff','--bg-secondary':'#f6f8fa','--bg-secondary2':'#eaeef2',
    '--bg-card':'#ffffff','--text-primary':'#1f2328','--text-secondary':'#656d76',
    '--border':'#d0d7de','--hover':'#0860ca','--navigation':'#ffffff',
    '--header-text':'#ffffff','--table-header-text':'#ffffff',
    '--editor-bg':'#f6f8fa','--editor-text':'#1f2328','--link-hover':'#cf222e',
  },
  'Solarized': {
    '--primary':'#268bd2','--primary-dark':'#1a5e8a','--secondary':'#eee8d5',
    '--success':'#859900','--danger':'#dc322f','--warning':'#b58900',
    '--bg-main':'#fdf6e3','--bg-secondary':'#eee8d5','--bg-secondary2':'#e8e2d0',
    '--bg-card':'#ffffff','--text-primary':'#073642','--text-secondary':'#657b83',
    '--border':'#cec8b5','--hover':'#2aa198','--navigation':'#ffffff',
    '--header-text':'#ffffff','--table-header-text':'#ffffff',
    '--editor-bg':'#eee8d5','--editor-text':'#073642','--link-hover':'#dc322f',
  },
  'Sunset': {
    '--primary':'#e05a2b','--primary-dark':'#a33d1b','--secondary':'#ffe8d6',
    '--success':'#2d6a4f','--danger':'#c1121f','--warning':'#f4a261',
    '--bg-main':'#fff8f0','--bg-secondary':'#ffe8d6','--bg-secondary2':'#ffd9bc',
    '--bg-card':'#ffffff','--text-primary':'#3d1f0d','--text-secondary':'#8b4513',
    '--border':'#f4c8a8','--hover':'#c1440e','--navigation':'#ffffff',
    '--header-text':'#ffffff','--table-header-text':'#ffffff',
    '--editor-bg':'#fff4e8','--editor-text':'#3d1f0d','--link-hover':'#c1121f',
  },
  'Forest': {
    '--primary':'#2d6a4f','--primary-dark':'#1b4332','--secondary':'#d8f3dc',
    '--success':'#1b4332','--danger':'#c1121f','--warning':'#dda15e',
    '--bg-main':'#f8fffe','--bg-secondary':'#d8f3dc','--bg-secondary2':'#c8ecd0',
    '--bg-card':'#ffffff','--text-primary':'#1b2e22','--text-secondary':'#40916c',
    '--border':'#b7e4c7','--hover':'#52b788','--navigation':'#ffffff',
    '--header-text':'#ffffff','--table-header-text':'#ffffff',
    '--editor-bg':'#ecf8ef','--editor-text':'#1b2e22','--link-hover':'#c1121f',
  },
};

const DARK_PRESETS  = ['Midnight','Steam','Neon','Cyberpunk','Ocean','AMOLED','Zidcora'];
const LIGHT_PRESETS = ['GitHub','Solarized','Sunset','Forest'];

const PRESET_CHIP_COLORS = {
  'Midnight':'#6366f1','Steam':'#1a9fff','Neon':'#00ff88','Cyberpunk':'#f72585',
  'Ocean':'#00b4d8','AMOLED':'#bb86fc','Zidcora':'#1e647b',
  'GitHub':'#0969da','Solarized':'#268bd2','Sunset':'#e05a2b','Forest':'#2d6a4f',
};

// ═══════════════════════════════════════════════════════
//  GENERADORES ALEATORIOS
// ═══════════════════════════════════════════════════════
function generateTheme(mode) {
  pushUndo();
  const baseHue = rand(0, 360);
  const ana     = (baseHue + 30) % 360;
  let c = Object.assign({}, currentColors); // mantiene colores bloqueados

  if (mode === 'dark') {
    if (!lockedKeys.has('--primary'))           c['--primary']           = hslToHex(baseHue, rand(65,85),  rand(45,58));
    if (!lockedKeys.has('--primary-dark'))       c['--primary-dark']       = hslToHex(baseHue, rand(70,90),  rand(18,28));
    if (!lockedKeys.has('--secondary'))          c['--secondary']          = hslToHex(baseHue, rand(8,18),   rand(18,28));
    if (!lockedKeys.has('--success'))            c['--success']            = hslToHex(rand(140,160), rand(60,80), rand(38,50));
    if (!lockedKeys.has('--danger'))             c['--danger']             = hslToHex(randRedHue(), rand(75,95), rand(48,58)); // ✅ BUG FIJO
    if (!lockedKeys.has('--warning'))            c['--warning']            = hslToHex(rand(38,52),  rand(90,100), rand(52,62));
    if (!lockedKeys.has('--bg-main'))            c['--bg-main']            = hslToHex(baseHue, rand(10,22), rand(6,11));
    if (!lockedKeys.has('--bg-secondary'))       c['--bg-secondary']       = hslToHex(baseHue, rand(10,22), rand(10,14));
    if (!lockedKeys.has('--bg-secondary2'))      c['--bg-secondary2']      = hslToHex(baseHue, rand(10,22), rand(12,16));
    if (!lockedKeys.has('--bg-card'))            c['--bg-card']            = hslToHex(baseHue, rand(10,22), rand(8,13));
    if (!lockedKeys.has('--text-primary'))       c['--text-primary']       = hslToHex(0, 0, rand(88,96));
    if (!lockedKeys.has('--text-secondary'))     c['--text-secondary']     = hslToHex(ana, rand(35,55), rand(55,70));
    if (!lockedKeys.has('--border'))             c['--border']             = hslToHex(baseHue, rand(10,20), rand(24,34));
    if (!lockedKeys.has('--hover'))              c['--hover']              = hslToHex(baseHue, rand(75,95), rand(55,68));
    if (!lockedKeys.has('--navigation'))         c['--navigation']         = '#ffffff';
    if (!lockedKeys.has('--header-text'))        c['--header-text']        = '#ffffff';
    if (!lockedKeys.has('--table-header-text'))  c['--table-header-text']  = '#ffffff';
    if (!lockedKeys.has('--editor-bg'))          c['--editor-bg']          = hslToHex(baseHue, 40, 12);
    if (!lockedKeys.has('--editor-text'))        c['--editor-text']        = '#e8ecf1';
    if (!lockedKeys.has('--link-hover'))         c['--link-hover']         = c['--danger'];
    if (!lockedKeys.has('--link'))               c['--link']               = c['--primary'];
  } else {
    if (!lockedKeys.has('--primary'))           c['--primary']            = hslToHex(baseHue, rand(55,75), rand(32,44));
    if (!lockedKeys.has('--primary-dark'))       c['--primary-dark']       = hslToHex(baseHue, rand(65,85), rand(18,28));
    if (!lockedKeys.has('--secondary'))          c['--secondary']          = hslToHex(baseHue, rand(10,25), rand(82,90));
    if (!lockedKeys.has('--success'))            c['--success']            = hslToHex(rand(140,160), rand(50,70), rand(32,42));
    if (!lockedKeys.has('--danger'))             c['--danger']             = hslToHex(randRedHue(), rand(70,90), rand(42,52)); // ✅ BUG FIJO
    if (!lockedKeys.has('--warning'))            c['--warning']            = hslToHex(rand(40,55),  rand(90,100), rand(50,60));
    if (!lockedKeys.has('--bg-main'))            c['--bg-main']            = hslToHex(baseHue, rand(10,30), rand(94,98));
    if (!lockedKeys.has('--bg-secondary'))       c['--bg-secondary']       = hslToHex(baseHue, rand(10,25), rand(90,94));
    if (!lockedKeys.has('--bg-secondary2'))      c['--bg-secondary2']      = hslToHex(baseHue, rand(10,25), rand(88,92));
    if (!lockedKeys.has('--bg-card'))            c['--bg-card']            = '#ffffff';
    if (!lockedKeys.has('--text-primary'))       c['--text-primary']       = hslToHex(baseHue, rand(15,35), rand(14,24));
    if (!lockedKeys.has('--text-secondary'))     c['--text-secondary']     = hslToHex(ana, rand(40,60), rand(40,52));
    if (!lockedKeys.has('--border'))             c['--border']             = hslToHex(baseHue, rand(10,25), rand(78,86));
    if (!lockedKeys.has('--hover'))              c['--hover']              = hslToHex(baseHue, rand(65,85), rand(28,38));
    if (!lockedKeys.has('--navigation'))         c['--navigation']         = '#ffffff';
    if (!lockedKeys.has('--header-text'))        c['--header-text']        = '#ffffff';
    if (!lockedKeys.has('--table-header-text'))  c['--table-header-text']  = '#ffffff';
    if (!lockedKeys.has('--editor-bg'))          c['--editor-bg']          = '#f5f7fa';
    if (!lockedKeys.has('--editor-text'))        c['--editor-text']        = '#1a1a1a';
    if (!lockedKeys.has('--link-hover'))         c['--link-hover']         = c['--danger'];
    if (!lockedKeys.has('--link'))               c['--link']               = c['--primary'];
  }

  document.getElementById('previewModeBadge').textContent = mode;
  currentColors = c;
  syncInputs();
  updatePreview();
}

function applyPreset(name) {
  pushUndo();
  const p = PRESETS[name];
  if (!p) return;
  themeVars.forEach(v => {
    // Fallback: si el preset no tiene --link, copiarlo de --primary
    if (!p['--link'] && !lockedKeys.has('--link')) currentColors['--link'] = currentColors['--primary'];
    if (!lockedKeys.has(v.key) && p[v.key]) currentColors[v.key] = p[v.key];
  });
  const isDark = DARK_PRESETS.includes(name);
  document.getElementById('previewModeBadge').textContent = isDark ? 'dark ('+name+')' : 'light ('+name+')';
  syncInputs();
  updatePreview();
}

// ═══════════════════════════════════════════════════════
//  UNDO
// ═══════════════════════════════════════════════════════
function pushUndo() {
  undoStack.push(JSON.stringify(currentColors));
  if (undoStack.length > MAX_UNDO) undoStack.shift();
  document.getElementById('undoBtn').disabled = false;
}
function undo() {
  if (!undoStack.length) return;
  currentColors = JSON.parse(undoStack.pop());
  if (!undoStack.length) document.getElementById('undoBtn').disabled = true;
  syncInputs();
  updatePreview();
  showToast('Generación deshecha', 'info');
}

// ═══════════════════════════════════════════════════════
//  CONTROLES
// ═══════════════════════════════════════════════════════
function renderControls() {
  const grid = document.getElementById('colorGrid');
  grid.innerHTML = '';
  themeVars.forEach(v => {
    const div = document.createElement('div');
    div.className = 'color-item' + (lockedKeys.has(v.key) ? ' locked' : '');
    div.dataset.key = v.key;
    div.innerHTML =
      '<input type="color" data-key="' + v.key + '" value="' + (currentColors[v.key]||'#000000') + '">' +
      '<label>' + v.label + '</label>' +
      '<input type="text" data-key="' + v.key + '-txt" value="' + (currentColors[v.key]||'#000000') + '">' +
      '<button class="lock-btn" data-lock="' + v.key + '" title="Proteger color">' + (lockedKeys.has(v.key) ? '🔒' : '🔓') + '</button>';
    grid.appendChild(div);
  });

  // Delegación de eventos
  grid.addEventListener('input', e => {
    const t = e.target, key = t.dataset.key;
    if (!key || t.type !== 'color') return;
    updateColor(key, t.value);
    const txt = grid.querySelector('input[data-key="' + key + '-txt"]');
    if (txt) txt.value = t.value;
  });
  grid.addEventListener('change', e => {
    const t = e.target, key = t.dataset.key;
    if (!key || t.type !== 'text') return;
    const val = t.value.trim();
    if (!/^#[0-9A-Fa-f]{6}$/.test(val)) { t.value = currentColors[key.replace('-txt','')]; return; }
    const realKey = key.replace('-txt','');
    updateColor(realKey, val);
    const col = grid.querySelector('input[data-key="' + realKey + '"]');
    if (col) col.value = val;
  });
  grid.addEventListener('click', e => {
    const btn = e.target.closest('.lock-btn');
    if (!btn) return;
    const key = btn.dataset.lock;
    if (lockedKeys.has(key)) lockedKeys.delete(key);
    else lockedKeys.add(key);
    const item = grid.querySelector('.color-item[data-key="' + key + '"]');
    if (item) {
      item.classList.toggle('locked', lockedKeys.has(key));
      btn.textContent = lockedKeys.has(key) ? '🔒' : '🔓';
    }
  });

  updateContrastBadges();
}

function syncInputs() {
  const grid = document.getElementById('colorGrid');
  themeVars.forEach(v => {
    const col = grid.querySelector('input[data-key="' + v.key + '"]');
    const txt = grid.querySelector('input[data-key="' + v.key + '-txt"]');
    if (col) col.value = currentColors[v.key] || '#000000';
    if (txt) txt.value = currentColors[v.key] || '#000000';
  });
  updateContrastBadges();
}

function updateColor(key, value) {
  if (!/^#[0-9A-Fa-f]{6}$/.test(value)) return;
  currentColors[key] = value;
  updatePreview();
  updateContrastBadges();
}

// Muestra ratio WCAG: text-primary sobre bg-main, header-text sobre primary, etc.
const CONTRAST_PAIRS = [
  ['--text-primary',      '--bg-main'],
  ['--header-text',       '--primary'],
  ['--table-header-text', '--primary'],
  ['--editor-text',       '--editor-bg'],
  ['--text-secondary',    '--bg-secondary'],
];
function updateContrastBadges() {
  const grid = document.getElementById('colorGrid');
  // limpiar badges previos
  grid.querySelectorAll('.contrast-badge').forEach(b => b.remove());
  CONTRAST_PAIRS.forEach(([fg, bg]) => {
    if (!currentColors[fg] || !currentColors[bg]) return;
    const ratio = contrastRatio(currentColors[fg], currentColors[bg]);
    const {cls, text} = contrastLabel(ratio);
    const item = grid.querySelector('.color-item[data-key="' + fg + '"]');
    if (item) {
      const badge = document.createElement('span');
      badge.className = 'contrast-badge ' + cls;
      badge.title = fg + ' / ' + bg + ' — ratio ' + ratio.toFixed(1) + ':1';
      badge.textContent = text;
      item.appendChild(badge);
    }
  });
}

// ═══════════════════════════════════════════════════════
//  CSS FINAL
// ═══════════════════════════════════════════════════════
function buildCSS(themeName) {
  const c = currentColors;
  const nameComment = themeName ? '/* Theme: ' + themeName + ' — generated by File4 Theme Studio */\n' : '';
  return nameComment +
`* { padding:0; box-sizing:border-box; }
:root {
  --primary:${c['--primary']}; --primary-dark:${c['--primary-dark']}; --secondary:${c['--secondary']};
  --success:${c['--success']}; --danger:${c['--danger']}; --warning:${c['--warning']};
  --bg-main:${c['--bg-main']}; --bg-secondary:${c['--bg-secondary']}; --bg-secondary2:${c['--bg-secondary2']};
  --bg-card:${c['--bg-card']}; --text-primary:${c['--text-primary']}; --text-secondary:${c['--text-secondary']};
  --border:${c['--border']}; --hover:${c['--hover']}; --navigation:${c['--navigation']};
  --header-text:${c['--header-text']}; --table-header-text:${c['--table-header-text']};
  --editor-bg:${c['--editor-bg']}; --editor-text:${c['--editor-text']}; --link-hover:${c['--link-hover']};
  --link:${c['--link']};
}
body{background-color:var(--bg-main);font-family:"Segoe UI",Tahoma,Arial,sans-serif;color:var(--text-primary);margin:0;padding:0;}
a{text-decoration:none;color:var(--link);transition:color .2s;} a:hover{color:var(--link-hover);}
hr{border:none;height:1px;width:100%;max-width:1000px;background:linear-gradient(90deg,transparent,var(--secondary),transparent);margin:24px 0;}
.tabla{display:table;width:1000px;border-collapse:separate;border-spacing:0;background-color:var(--bg-card);box-shadow:0 4px 10px rgba(0,0,0,0.05);border:1px solid var(--secondary);margin:10px 0;border-radius:7px;overflow:hidden;border-left:8px solid var(--secondary);}
.filasinfx{display:table-row;background-color:var(--primary);border-bottom:1px solid var(--primary-dark);}
.fila{display:table-row;border-bottom:1px solid var(--secondary);position:relative;overflow:hidden;z-index:1;}
.fila::before{content:'';position:absolute;top:0;left:-100%;height:100%;width:100%;background-color:var(--bg-secondary);z-index:0;}
.fila:hover::before{left:0;} .fila:nth-child(even):hover::before{background-color:var(--bg-secondary)!important;}
.fila *{position:relative;z-index:1;}
.celda,.celda2,.celda3,.celda4{display:table-cell;padding:5px 5px;border:1px solid var(--secondary);vertical-align:middle; color: var(--table-header-text);}
.celda2{width:170px;} .celda3{width:90px;} .celda4{width:395px;}
.celdab,.celda2b,.celda3b,.celda4b{display:table-cell;background-color:var(--primary);padding:5px 5px;border:1px solid var(--secondary);vertical-align:middle; color: var(--table-header-text);}
.celda2b{width:170px;} .celda3b{width:90px;} .celda4b{width:395px;}
.infotitle{padding:5px 5px;vertical-align:middle; color: var(--table-header-text);}
.fila:nth-child(even){background-color:var(--bg-secondary2);}
button,input[type="submit"]{background:var(--primary);border:none;color:#fff;padding:6px 14px;font-size:14px;font-weight:bold;cursor:pointer;border-radius:3px;transition:background .2s;}
button:hover,input[type="submit"]:hover{background:var(--hover);}
.btn-warning{background:var(--warning);border:none;color:#000;padding:6px 14px;font-size:14px;font-weight:bold;cursor:pointer;border-radius:3px;transition:filter .2s;}
.btn-warning:hover{filter:brightness(.9);}
header{background-color:var(--primary);background-image:none;color:var(--header-text);text-align:left;width:100%;padding:15px;box-sizing:border-box;border-bottom:4px solid var(--secondary);}
footer{width:1000px;background:var(--primary-dark);box-shadow:0 2px 5px rgba(0,0,0,0.05);padding:15px;border-radius:12px;margin-bottom:15px;border:1px solid var(--secondary);color:var(--text-secondary);}
.mensajex{background-color:var(--bg-secondary);color:var(--primary);padding:15px;border:1px solid var(--primary);border-radius:4px;margin-bottom:10px;}
.rojito{background-color:var(--danger);color:#fff;padding:8px 16px;text-decoration:none;border-radius:3px;display:inline-block;}
.verde{background-color:var(--success);color:#fff;padding:8px 16px;text-decoration:none;border-radius:3px;display:inline-block;}
.naranja{background-color:var(--warning);color:#000;padding:8px 16px;text-decoration:none;border-radius:3px;display:inline-block;}
.snaranja{background-color:var(--warning);color:#000;padding:5px 10px;text-decoration:none;border-radius:3px;display:inline-block;}
.azulin,.azulin2,.enlacez{background-color:var(--primary);color:#fff;padding:5px 10px;text-decoration:none;border-radius:3px;display:inline-block;transition:background .3s;}
.enlacez:hover{background-color:var(--primary-dark);color:#fff;}
.formtext,.formtext2{background-color:var(--bg-card);color:var(--text-primary);border:1px solid var(--border);padding:6px;margin:3px;border-radius:3px;}
.mensaje{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background-color:var(--bg-card);padding:25px;border:2px solid var(--primary);box-shadow:0 10px 40px rgba(0,0,0,0.2);width:320px;z-index:10;border-radius:5px;}
.mensaje:target{display:block;} .cerrar{display:inline-block;margin-top:15px;padding:6px 12px;background-color:var(--primary);color:white;text-decoration:none;border-radius:3px;}
.editor-wrapper{display:flex;border:1px solid var(--secondary);overflow:hidden;height:450px;width:1000px;}
.line-numbers{background-color:var(--primary);font-family:Fira Code,Consolas,Courier New,monospace;padding:8px 9px;line-height:1.2491;text-align:right;user-select:none;overflow:hidden;color:var(--text-secondary);border-right:1px solid var(--secondary);min-width:44px;}
.code-editor{font-family:Fira Code,Consolas,Courier New,monospace;width:100%;border:none;outline:none;padding:8px;resize:none;line-height:1.5;overflow-y:scroll;white-space:nowrap;background-color:var(--editor-bg);color:var(--editor-text);}
.editor-container{display:flex;width:100%;height:100%;}
.upload-section{width:1000px;background:var(--bg-card);box-shadow:0 2px 5px rgba(0,0,0,0.05);padding:10px;border-radius:8px;margin-bottom:15px;border:1px solid var(--secondary);}
.upload-form{display:flex;gap:15px;align-items:center;flex-wrap:wrap;}
.file-input-wrapper{position:relative;flex:1;min-width:280px;}
input[type="file"]{width:450px;padding:12px 15px;background:var(--bg-secondary);border:2px dashed var(--secondary);border-radius:8px;color:var(--text-primary);}
input[type="file"]:hover{border-color:var(--primary);background:var(--bg-secondary2);}
.fileperms{color:var(--editor-text);} .fileperms2{color:var(--primary);} .file-time{color:var(--text-secondary);}`;
}

// ═══════════════════════════════════════════════════════
//  VISTA PREVIA (ampliada con más elementos de file4)
// ═══════════════════════════════════════════════════════

// HTML del preview separado — reutilizable desde el editor CSS
function buildPreviewHTML() {
  const c = currentColors;
  return (
    // Header
    '<div style="padding:12px 15px;font-size:16px;font-weight:bold;background:'+c['--primary']+';color:'+c['--header-text']+';border-bottom:4px solid '+c['--secondary']+';display:flex;align-items:center;justify-content:space-between;">'+
      '<span>📁 FILE MANAGER — FILE4 <span style="font-size:11px;opacity:.75;">v4.4.7.x</span></span>'+
      '<span style="font-size:12px;font-weight:400;opacity:.85;">usuario@servidor</span>'+
    '</div>'+
    // Breadcrumb
    '<div style="padding:8px 14px;background:'+c['--bg-secondary']+';color:'+c['--text-primary']+';font-size:13px;border-bottom:1px solid '+c['--secondary']+';">'+
      '<a href="#" style="color:'+c['--link']+';">Inicio</a> / '+
      '<a href="#" style="color:'+c['--primary']+';">uploads</a> / '+
      '<span style="color:'+c['--text-secondary']+';">proyecto</span>'+
    '</div>'+
    // Upload zone
    '<div style="padding:10px 14px;background:'+c['--bg-main']+';">'+
      '<div style="border:2px dashed '+c['--secondary']+';border-radius:7px;padding:10px 14px;background:'+c['--bg-secondary']+';margin-bottom:10px;display:flex;align-items:center;gap:12px;">'+
        '<span style="font-size:18px;">📤</span>'+
        '<span style="color:'+c['--text-secondary']+';font-size:12px;">Arrastra archivos aquí o</span> '+
        '<span style="background:'+c['--primary']+';color:#fff;padding:4px 12px;border-radius:3px;font-size:12px;font-weight:bold;cursor:pointer;">Seleccionar</span>'+
      '</div>'+
    // Table
      '<div style="display:table;width:100%;background:'+c['--bg-card']+';border:1px solid '+c['--secondary']+';border-radius:7px;overflow:hidden;margin-bottom:10px;">'+
        '<div style="display:table-row;background:'+c['--primary']+';">'+
          '<div style="display:table-cell;padding:7px 10px;color:'+c['--table-header-text']+';font-weight:bold;font-size:12px;">Nombre</div>'+
          '<div style="display:table-cell;padding:7px 10px;color:'+c['--table-header-text']+';font-weight:bold;font-size:12px;width:80px;">Tamaño</div>'+
          '<div style="display:table-cell;padding:7px 10px;color:'+c['--table-header-text']+';font-weight:bold;font-size:12px;width:130px;">Modificado</div>'+
          '<div style="display:table-cell;padding:7px 10px;color:'+c['--table-header-text']+';font-weight:bold;font-size:12px;width:200px;">Acciones</div>'+
        '</div>'+
        '<div style="display:table-row;background:'+c['--bg-card']+';">'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';font-size:12px;">📁 <a href="#" style="color:'+c['--link']+';">carpeta-proyecto</a></div>'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';font-size:12px;color:'+c['--text-secondary']+';">Carpeta</div>'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';font-size:11px;color:'+c['--text-secondary']+';">2026-07-01 18:30</div>'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';">'+
            '<span style="background:'+c['--primary']+';color:#fff;padding:3px 8px;border-radius:3px;font-size:11px;font-weight:bold;">Abrir</span>'+
          '</div>'+
        '</div>'+
        '<div style="display:table-row;background:'+c['--bg-secondary2']+';">'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';font-size:12px;">📄 <a href="#" style="color:'+c['--link']+';">archivo.php</a></div>'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';font-size:12px;color:'+c['--text-secondary']+';">28 KB</div>'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';font-size:11px;color:'+c['--text-secondary']+';">2026-07-21 09:15</div>'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';">'+
            '<span style="background:'+c['--primary']+';color:#fff;padding:3px 8px;border-radius:3px;font-size:11px;font-weight:bold;margin-right:3px;">Editar</span>'+
            '<span style="background:'+c['--warning']+';color:#000;padding:3px 8px;border-radius:3px;font-size:11px;font-weight:bold;margin-right:3px;">Copiar</span>'+
            '<span style="background:'+c['--danger']+';color:#fff;padding:3px 8px;border-radius:3px;font-size:11px;font-weight:bold;">Borrar</span>'+
          '</div>'+
        '</div>'+
        '<div style="display:table-row;background:'+c['--bg-card']+';">'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';font-size:12px;">🖼 <a href="#" style="color:'+c['--link']+';">logo.png</a></div>'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';font-size:12px;color:'+c['--text-secondary']+';">142 KB</div>'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';font-size:11px;color:'+c['--text-secondary']+';">2026-06-10 14:00</div>'+
          '<div style="display:table-cell;padding:6px 10px;border-top:1px solid '+c['--secondary']+';">'+
            '<span style="background:'+c['--success']+';color:#fff;padding:3px 8px;border-radius:3px;font-size:11px;font-weight:bold;margin-right:3px;">Preview</span>'+
            '<span style="background:'+c['--danger']+';color:#fff;padding:3px 8px;border-radius:3px;font-size:11px;font-weight:bold;">Borrar</span>'+
          '</div>'+
        '</div>'+
      '</div>'+
      '<div style="background:'+c['--bg-secondary']+';color:'+c['--primary']+';border:1px solid '+c['--primary']+';border-radius:4px;padding:10px 14px;margin-bottom:10px;font-size:12px;">'+
        '✅ Archivo subido correctamente.</div>'+
      '<div style="display:flex;border:1px solid '+c['--secondary']+';border-radius:4px;overflow:hidden;height:120px;margin-bottom:10px;">'+
        '<div style="background:'+c['--editor-bg']+';padding:8px 10px;font-family:Fira Code,monospace;text-align:right;color:'+c['--text-secondary']+';font-size:12px;line-height:1.6;border-right:1px solid '+c['--secondary']+';min-width:40px;user-select:none;">1<br>2<br>3<br>4<br>5</div>'+
        '<div style="flex:1;padding:8px 10px;font-family:Fira Code,monospace;font-size:12px;line-height:1.6;background:'+c['--editor-bg']+';color:'+c['--editor-text']+';">&lt;?php<br>$config = require \'config.php\';<br>$theme  = $config[\'theme\'] ?? \'dark\';<br>echo "Tema activo: " . $theme;<br>?&gt;</div>'+
      '</div>'+
      '<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px;">'+
        '<span style="background:'+c['--primary']+';color:#fff;padding:7px 14px;border-radius:3px;font-size:12px;font-weight:bold;cursor:pointer;">Guardar</span>'+
        '<span style="background:'+c['--success']+';color:#fff;padding:7px 14px;border-radius:3px;font-size:12px;font-weight:bold;cursor:pointer;">Aceptar</span>'+
        '<span style="background:'+c['--danger']+';color:#fff;padding:7px 14px;border-radius:3px;font-size:12px;font-weight:bold;cursor:pointer;">Eliminar</span>'+
        '<span style="background:'+c['--warning']+';color:#000;padding:7px 14px;border-radius:3px;font-size:12px;font-weight:bold;cursor:pointer;">Advertencia</span>'+
      '</div>'+
      '<div style="padding:10px 14px;background:'+c['--primary-dark']+';color:'+c['--text-secondary']+';font-size:11px;border-radius:8px;">'+
        '📁 FILE MANAGER | v4.4.7.x | creado por <a href="#" style="color:'+c['--text-secondary']+';">zidrave</a></div>'+
    '</div>'
  );
}

function updatePreview() {
  const css = buildCSS();
  document.getElementById('cssOutput').textContent = css;
  const frame = document.getElementById('previewFrame');
  frame.innerHTML = '<style>' + css + '</style>' + buildPreviewHTML();
}

// ═══════════════════════════════════════════════════════
//  SAVED THEMES (localStorage)
// ═══════════════════════════════════════════════════════
function loadSaved() {
  try { return JSON.parse(localStorage.getItem(LS_KEY) || '[]'); } catch(e) { return []; }
}
function renderSavedList() {
  const saved = loadSaved();
  const list = document.getElementById('savedList');
  list.innerHTML = '';
  for (let i = 0; i < MAX_SAVED; i++) {
    const slot = document.createElement('div');
    slot.className = 'saved-slot' + (saved[i] ? '' : ' empty');
    slot.title = saved[i] ? (saved[i].name || 'Theme ' + (i+1)) : 'Slot vacío';
    if (saved[i]) {
      const c = saved[i].colors;
      slot.innerHTML =
        '<div class="slot-preview">' +
          '<div class="slot-half" style="background:' + (c['--primary']||'#333') + '"></div>' +
          '<div class="slot-half" style="background:' + (c['--bg-main']||'#000') + '"></div>' +
        '</div>' +
        '<div class="slot-del" title="Click para cargar, click derecho para borrar">▶</div>';
      slot.addEventListener('click', () => {
        pushUndo();
        currentColors = Object.assign({}, saved[i].colors);
        syncInputs();
        updatePreview();
        showToast('Theme "' + (saved[i].name||'Theme '+(i+1)) + '" cargado', 'info');
      });
      slot.addEventListener('contextmenu', e => {
        e.preventDefault(); // Evita que aparezca el menú contextual del navegador
        e.stopPropagation();
        if (!confirm('¿Borrar este theme del slot ' + (i+1) + '?')) return;
        saved.splice(i, 1);
        localStorage.setItem(LS_KEY, JSON.stringify(saved));
        renderSavedList();
        showToast('Theme borrado del slot ' + (i+1), 'info');
      });
    }
    list.appendChild(slot);
  }
}
function saveToLocalStorage() {
  const saved = loadSaved();
  if (saved.length >= MAX_SAVED) {
    showToast('Slots llenos. Borra alguno con doble clic.', 'error');
    return;
  }
  const name = document.getElementById('themeName').value.trim() || ('theme-' + Date.now());
  saved.push({ name, colors: Object.assign({}, currentColors) });
  localStorage.setItem(LS_KEY, JSON.stringify(saved));
  renderSavedList();
  showToast('Guardado en slot ' + saved.length + ': ' + name, 'ok');
}

// ═══════════════════════════════════════════════════════
//  IMPORTAR CSS — estado
// ═══════════════════════════════════════════════════════
let importedCSSText  = '';   // texto raw del CSS cargado (paste o file)
let importedFileName = '';   // nombre del archivo subido

// ── Tabs ──
function switchImportTab(tab) {
  document.getElementById('tabPaste').classList.toggle('active',  tab === 'paste');
  document.getElementById('tabUpload').classList.toggle('active', tab === 'upload');
  document.getElementById('tabPasteBtn').classList.toggle('active',  tab === 'paste');
  document.getElementById('tabUploadBtn').classList.toggle('active', tab === 'upload');
}

// ── Leer CSS activo (paste o archivo) ──
function getImportCSS() {
  const activeTab = document.getElementById('tabPaste').classList.contains('active') ? 'paste' : 'upload';
  if (activeTab === 'paste') return document.getElementById('importCss').value.trim();
  return importedCSSText;
}

// ── Abrir / cerrar modal ──
function openImport() {
  document.getElementById('importModal').classList.add('open');
  switchImportTab('paste');
}
function closeImport() {
  document.getElementById('importModal').classList.remove('open');
}

// ── Drag & Drop + File Input ──
(function setupFileUpload() {
  const zone  = document.getElementById('dropZone');
  const input = document.getElementById('fileInput');

  function readFile(file) {
    if (!file) return;
    if (!file.name.endsWith('.css') && file.type !== 'text/css') {
      showToast('Solo se aceptan archivos .css', 'error'); return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      importedCSSText  = e.target.result;
      importedFileName = file.name;
      document.getElementById('fileLoadedName').textContent = file.name;
      document.getElementById('fileLoadedSize').textContent =
        (file.size > 1024 ? (file.size/1024).toFixed(1) + ' KB' : file.size + ' B');
      document.getElementById('fileLoaded').classList.add('show');
      zone.querySelector('.dz-text').innerHTML =
        '<strong>' + file.name + '</strong><br><span style="color:var(--s-ok)">✓ Archivo listo</span>';
      switchImportTab('upload');
    };
    reader.readAsText(file);
  }

  input.addEventListener('change', () => readFile(input.files[0]));

  zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
  zone.addEventListener('dragleave', ()  => zone.classList.remove('drag-over'));
  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    readFile(e.dataTransfer.files[0]);
  });
})();

// ── Extraer paleta de colores del CSS ──
function extractPaletteFromCSS(css) {
  const rootMatch = css.match(/:root\s*\{([^}]+)\}/s);
  if (!rootMatch) return 0;
  const block = rootMatch[1];
  let found = 0;
  themeVars.forEach(v => {
    const re = new RegExp(v.key.replace('--','--') + '\\s*:\\s*(#[0-9A-Fa-f]{3,6})', 'i');
    const m  = block.match(re);
    if (m) {
      let hex = m[1];
      // Normalizar hex corto (#abc → #aabbcc)
      if (hex.length === 4) hex = '#' + hex[1]+hex[1]+hex[2]+hex[2]+hex[3]+hex[3];
      currentColors[v.key] = hex; found++;
    }
  });
  return found;
}

// ── Botón: Extraer paleta ──
function doImportPalette() {
  const css = getImportCSS();
  if (!css) { showToast('No hay CSS cargado', 'error'); return; }
  pushUndo();
  const found = extractPaletteFromCSS(css);
  if (!found) { showToast('No se encontraron variables --custom compatibles', 'error'); return; }
  syncInputs();
  updatePreview();
  closeImport();
  showToast(found + ' variables extraídas de ' + (importedFileName || 'CSS pegado'), 'ok');
}

// ── Botón: Abrir editor CSS completo ──
function doImportEditor() {
  const css = getImportCSS();
  if (!css) { showToast('No hay CSS cargado', 'error'); return; }
  importedCSSText = css;
  closeImport();
  openCssEditor(css, importedFileName || 'importado.css');
}

// ═══════════════════════════════════════════════════════
//  CSS EDITOR COMPLETO
// ═══════════════════════════════════════════════════════
let cedOriginalCSS = '';

function openCssEditor(css, filename) {
  cedOriginalCSS = css;
  document.getElementById('cedFileName').textContent = filename || 'sin nombre';
  document.getElementById('cedTextarea').value = css;
  document.getElementById('cssEditorOverlay').classList.add('open');
  cedUpdateStats();
  cedRenderLineNums();
}
function closeCssEditor() {
  document.getElementById('cssEditorOverlay').classList.remove('open');
}

// Actualizar números de línea
function cedRenderLineNums() {
  const ta    = document.getElementById('cedTextarea');
  const lines = ta.value.split('\n').length;
  const nums  = document.getElementById('cedLineNums');
  let html = '';
  for (let i = 1; i <= lines; i++) html += i + '\n';
  nums.textContent = html;
}

// Sincronizar scroll líneas ↔ textarea
function cedSyncScroll() {
  const ta   = document.getElementById('cedTextarea');
  const nums = document.getElementById('cedLineNums');
  nums.scrollTop = ta.scrollTop;
}

// Evento: textarea cambia
function cedOnInput() {
  cedRenderLineNums();
  cedUpdateStats();
  cedSyncScroll();
}

function cedUpdateStats() {
  const val = document.getElementById('cedTextarea').value;
  document.getElementById('cedLineCount').textContent = val.split('\n').length;
  document.getElementById('cedCharCount').textContent = val.length.toLocaleString();
}

// ── Re-extraer paleta desde el editor ──
function cedReExtract() {
  const css = document.getElementById('cedTextarea').value;
  pushUndo();
  const found = extractPaletteFromCSS(css);
  if (!found) { showToast('No se encontraron variables compatibles en el CSS', 'error'); return; }
  syncInputs();
  updatePreview();
  setCedStatus('✅ ' + found + ' variables extraídas a la paleta');
  showToast(found + ' variables re-extraídas', 'ok');
}

// ── Generar variante: toma el CSS base y reemplaza solo los valores de color ──
function cedGenerateVariant() {
  const ta  = document.getElementById('cedTextarea');
  let css   = ta.value;
  const mode = document.getElementById('previewModeBadge').textContent.includes('light') ? 'light' : 'dark';
  // Genera nuevos colores SIN tocar los bloqueados
  generateTheme(mode);   // actualiza currentColors y preview normal
  // Aplica los nuevos colores al CSS del editor
  themeVars.forEach(v => {
    const re  = new RegExp('(' + escapeReg(v.key) + '\\s*:\\s*)#[0-9A-Fa-f]{3,6}', 'g');
    if (currentColors[v.key]) css = css.replace(re, '$1' + currentColors[v.key]);
  });
  ta.value = css;
  cedOnInput();
  setCedStatus('🎲 Variante generada — colores actualizados en el CSS');
  showToast('Variante generada y aplicada al editor', 'ok');
}

// ── Aplicar CSS del editor al preview (inyecta el CSS raw) ──
function cedApplyPreview() {
  const css   = document.getElementById('cedTextarea').value;
  const frame = document.getElementById('previewFrame');
  // Actualizar también la paleta si hay vars reconocibles
  extractPaletteFromCSS(css);
  syncInputs();
  // Inyectar el CSS raw en el preview
  frame.innerHTML = '<style>' + css + '</style>' + buildPreviewHTML();
  setCedStatus('▶ Preview actualizada con el CSS del editor');
  showToast('Preview actualizada', 'ok');
}

// ── Copiar CSS del editor ──
function cedCopy() {
  navigator.clipboard.writeText(document.getElementById('cedTextarea').value)
    .then(() => { setCedStatus('📋 Copiado'); showToast('CSS copiado al portapapeles', 'ok'); });
}

// ── Guardar CSS del editor en el servidor ──
async function cedSave() {
  const name = document.getElementById('themeName').value.trim();
  if (!/^[a-zA-Z0-9_-]+$/.test(name)) {
    showToast('Pon un nombre válido en la barra lateral primero', 'error'); return;
  }
  const css = document.getElementById('cedTextarea').value;
  try {
    const res  = await fetch('', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'action=save&name=' + encodeURIComponent(name) + '&css=' + encodeURIComponent(css),
    });
    const data = await res.json();
    if (data.ok) {
      setCedStatus('💾 Guardado: ' + data.file);
      showToast('Guardado: ' + data.file, 'ok');
      saveToLocalStorage();
    } else {
      showToast(data.error || 'Error al guardar', 'error');
    }
  } catch(e) { showToast('Error de red', 'error'); }
}

function setCedStatus(msg) {
  const el = document.getElementById('cedStatus');
  el.textContent = msg;
  clearTimeout(el._t);
  el._t = setTimeout(() => el.textContent = '', 4000);
}

function escapeReg(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

// ── Abrir editor CSS con el CSS generado actual (desde sidebar) ──
function openEditorFromGenerated() {
  const name = document.getElementById('themeName').value.trim() || 'generado';
  openCssEditor(buildCSS(name), name + '.css');
}

// ═══════════════════════════════════════════════════════
//  ACCIONES
// ═══════════════════════════════════════════════════════
function showToast(msg, type) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast ' + (type === 'error' ? 'bad' : type === 'info' ? 'info' : 'ok');
  t.classList.add('show');
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.classList.remove('show'), 3200);
}
function copyCSS() {
  navigator.clipboard.writeText(buildCSS(document.getElementById('themeName').value.trim()))
    .then(() => showToast('CSS copiado al portapapeles', 'ok'));
}
async function saveTheme() {
  const name = document.getElementById('themeName').value.trim();
  if (!/^[a-zA-Z0-9_-]+$/.test(name)) {
    showToast('Nombre inválido. Solo letras, números, guiones y guiones bajos.', 'error');
    return;
  }
  try {
    const res  = await fetch('', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'action=save&name=' + encodeURIComponent(name) + '&css=' + encodeURIComponent(buildCSS(name)),
    });
    const data = await res.json();
    showToast(data.ok ? '✅ Guardado: ' + data.file : (data.error || 'Error al guardar'), data.ok ? 'ok' : 'error');
    if (data.ok) saveToLocalStorage();
  } catch(e) {
    showToast('Error de red al guardar', 'error');
  }
}

// ═══════════════════════════════════════════════════════
//  PRESETS CHIPS
// ═══════════════════════════════════════════════════════
function renderPresetChips() {
  function makeChip(name, container) {
    const btn = document.createElement('button');
    btn.className = 'chip';
    btn.textContent = name;
    btn.style.background = PRESET_CHIP_COLORS[name] || '#555';
    btn.style.color = '#fff';
    btn.style.boxShadow = '0 2px 8px ' + (PRESET_CHIP_COLORS[name]||'#555') + '55';
    btn.onclick = () => { pushUndo(); applyPreset(name); };
    container.appendChild(btn);
  }
  const dContainer = document.getElementById('presetsD');
  const lContainer = document.getElementById('presetsL');
  DARK_PRESETS.forEach(n  => makeChip(n, dContainer));
  LIGHT_PRESETS.forEach(n => makeChip(n, lContainer));
}

// ═══════════════════════════════════════════════════════
//  TECLADO
// ═══════════════════════════════════════════════════════
document.addEventListener('keydown', e => {
  const tag = document.activeElement.tagName;
  if (tag === 'INPUT' || tag === 'TEXTAREA') return;
  if (e.code === 'Space') {
    e.preventDefault();
    const mode = document.getElementById('previewModeBadge').textContent.includes('light') ? 'light' : 'dark';
    generateTheme(mode);
  }
  if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveTheme(); }
  if ((e.ctrlKey || e.metaKey) && e.key === 'z') { e.preventDefault(); undo(); }
});

// Cerrar modal con Esc
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeImport();
});
document.getElementById('importModal').addEventListener('click', e => {
  if (e.target === document.getElementById('importModal')) closeImport();
});

// ═══════════════════════════════════════════════════════
//  INIT — orden correcto: render controls → generate → saved
// ═══════════════════════════════════════════════════════
window.addEventListener('DOMContentLoaded', () => {
  // 1. Paleta inicial vacía para que renderControls() tenga algo
  themeVars.forEach(v => { currentColors[v.key] = '#000000'; });
  // 2. Render controls (crea los inputs)
  renderControls();
  // 3. Genera tema inicial (syncInputs ya encuentra los inputs)
  generateTheme('dark');
  // 4. Chips de presets
  renderPresetChips();
  // 5. Saved themes
  renderSavedList();
});
</script>
</body>
</html>
