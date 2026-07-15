![image](https://github.com/user-attachments/assets/1e43e024-08a2-4cff-900c-75ebcb50e2cf)
![image](https://github.com/user-attachments/assets/8ed71c49-a816-4fde-89dd-2350833e270f)
![image](https://github.com/user-attachments/assets/70c94efe-c31d-4968-b0a1-0b7108a4fa12)

[![Miniatura de mi video](https://i.ytimg.com/vi/wvbwX_QGi48/hqdefault.jpg)](https://www.youtube.com/watch?v=wvbwX_QGi48)

**Video Demo en Youtube**

# FILE MANAGER PHP (1 archivo) — Administrador de Archivos en PHP

Un administrador de archivos simple pero potente escrito en un único archivo PHP (no usa base de datos / SQL). Pensado para servidores sin panel de control, donde solo tenés acceso vía FTP/SSH y necesitás gestionar archivos desde el navegador.

Requiere crear la carpeta `/uploads/` con permisos `755` (o `777` si tu hosting lo exige) para funcionar.

---

## ENGLISH

A simple but powerful file manager in PHP (does not use SQL). Requires creating the `/uploads/` folder with `755` permissions (or `777` depending on your hosting).

---

## ESPAÑOL

Un sencillo pero potente administrador de archivos en PHP (no usa SQL). Requiere crear la carpeta `/uploads/` con permisos `755` únicamente (o `777` según tu hosting).

Si desea mejorarlo lo puede hacer, ¡gracias!

**Versión actual: 4.4.5**

---

## ✨ Características

### Gestión de archivos y carpetas
- Navegación completa de carpetas con **breadcrumb** (ruta de migas de pan) clicable.
- Subida de archivo único, con opción de **permitir o bloquear subida de archivos `.php`**.
- Subida **múltiple** vía arrastrar y soltar (drag & drop), con barra de progreso en tiempo real (AJAX).
- Crear, renombrar y copiar archivos.
- Crear y eliminar carpetas (elimina solo si están vacías).
- Descarga directa de archivos.
- Vista previa de imágenes en modal, con botón para copiar la URL completa al portapapeles.
- Panel de información detallada por archivo: tamaño, fecha de creación/acceso/modificación, permisos, propietario, grupo, tipo MIME y hash MD5.
- Iconos automáticos según tipo de archivo (imágenes, ejecutables, texto, PDF, Office, comprimidos, audio, video).
- Resumen de carpeta: peso total, cantidad de carpetas y archivos.

### Editor de código integrado
- **Editor Simple**: textarea básico para edición rápida.
- **Editor Plus**: editor con numeración de líneas, guardado por AJAX sin recargar la página, activable/desactivable por archivo vía cookie.

### Compresión
- Comprimir archivos **o carpetas completas** a formato ZIP.
- Contraseña opcional en el ZIP (cifrado AES-256).
- Comentario/descripción incrustado en el archivo comprimido.

### Seguridad
- **Sistema de login** con usuario y contraseña (hash bcrypt + pepper, sin texto plano).
- Bloqueo progresivo (backoff exponencial) tras intentos fallidos de acceso.
- Campo honeypot anti-bots en el formulario de login.
- Auto-login mediante cookie, atado a la IP del dispositivo.
- Cookies con flags `HttpOnly`, `Secure` y `SameSite` configurados.
- Sistema de **desbloqueo de emergencia** con token configurable y límite de intentos por 24 horas, con reconocimiento automático de la IP del dueño.
- Modo "sistema sin usuario creado" que avisa claramente si aún no configuraste una contraseña.
- Opción de borrar toda la configuración (reset de fábrica) con confirmación de contraseña.

### Personalización
- **Multi-idioma**: Español, Inglés y Alemán (cargables desde archivos JSON externos, fácil de agregar más).
- **Sistema de temas (skins)**: detecta automáticamente archivos CSS `fmstyle_*.css` en el directorio y permite elegir el tema activo desde el panel de configuración.
- Color de acento dinámico generado a partir del dominio del sitio.

### Panel de sistema
- Espacio usado y disponible en disco.
- Memoria usada y total.
- Carga del procesador (promedio) y porcentaje estimado según núcleos disponibles.
- Temperatura del núcleo (si el servidor la expone).
- Tiempo de actividad (uptime) del servidor.
- Sistema operativo detectado.

### Actualización
- **Auto-actualización** desde GitHub con un clic, protegida por confirmación de contraseña.
- El parcheo conserva automáticamente tus valores de seguridad personalizados (tokens, nombre del archivo de configuración) tras actualizar.
- Descarga también los archivos de idioma y todos los temas disponibles junto con la actualización.

---

## 📋 Cambios agregados (Changelog)

```
==========================================
v4.4.5
+ Sistema de login para mayor seguridad
+ Compresión ZIP para archivos y carpetas, con opción de contraseña
+ Auto-actualización rápida desde GitHub
+ Sistema de temas (skins) personalizables
+ Soporte multi-idioma (ES / EN / DE)
+ Editor de código con numeración de líneas (Editor Plus)
+ Subida múltiple con drag & drop y barra de progreso
+ Panel de información de sistema (disco, memoria, CPU, uptime)
+ Vista previa de imágenes con copia rápida de URL
==========================================
```

---

## ⚙️ Instalación

1. Descargá `file4.php` y subilo a tu servidor.
2. Creá la carpeta `uploads/` con permisos `755` en el mismo directorio.
3. Abrí el script en tu navegador — te va a pedir que configures tu usuario y contraseña la primera vez.
4. **Importante:** antes de crear tu usuario, editá al inicio del archivo los valores de `$tokenplus`, `$pepper` y `$configFile` para tu propia instalación. No dejes los valores por defecto del repositorio.

---

## 📬 Contacto

¿Sugerencias o mejoras? Escribime a: **zidravex@gmail.com**

**Link para descargar:**
[https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/file4.php](https://raw.githubusercontent.com/zidrave/filemanager_1filephp/main/file4.php)

![image](https://github.com/user-attachments/assets/443d9e76-a7a6-4548-9370-efad1dd8d717)
