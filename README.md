
# CATÁLOGO DE PRODUCTOS - Homeland
### DANIEL DE LEÓN 


Aplicación web responsive para el manejo de datos sobre un catálogo de productos, realizado en Laravel y MySQL junto con HTML, CSS y Javascript

![Demo](screenshots/exposicion.gif)



##  Instalación Paso a Paso

### 1. Descargar el código fuente
Abre tu terminal (CMD, PowerShell o Terminal) y ejecuta:

```bash
# Clonar el repositorio (Usamos una url de ejemplo)
git clone https://github.com/photodangt-web/homeland-catalog
# Entrar a la carpeta del proyecto
cd catalogo-homeland
```

### 2. Preparar la Base de Datos
Como el proyecto usará tu MySQL local, necesitas crear la base de datos vacía.

1.  Abre tu gestor de base de datos favorito (phpMyAdmin, TablePlus, DBeaver, etc.).
2.  Crea una nueva base de datos llamada: `homeland_catalog`
3.  Asegúrate de recordar tu usuario (generalmente `root`) y tu contraseña (generalmente vacía o `root`).

### 3. Configurar el entorno
El proyecto necesita un archivo de configuración. Vamos a duplicar el ejemplo:

**En Windows:**
```bash
copy .env.example .env
```
**En Mac/Linux:**
```bash
cp .env.example .env
```

Ahora, abre el archivo `.env` con cualquier editor de texto (Notepad, VS Code) y busca la sección de base de datos. **Asegúrate de que quede así:**

```ini
DB_CONNECTION=mysql
DB_HOST=host.docker.internal
DB_PORT=3306
DB_DATABASE=homeland_catalog
DB_USERNAME=root
DB_PASSWORD=  <-- Pon tu contraseña aquí si tienes una, si no, déjalo vacío
```
> **Ojo:** `host.docker.internal` es el truco mágico que permite a Docker "ver" el MySQL de tu computadora.

### 4. Levantar los Contenedores (Docker)
Este paso construirá la imagen y encenderá el servidor. Puede tardar unos minutos la primera vez.

```bash
docker-compose up -d --build
```
*   Si todo sale bien, verás mensajes en verde diciendo "Started" o "Running".

### 5. Instalar dependencias y preparar Laravel
Ahora entraremos a la "computadora virtual" (contenedor) para instalar lo que falta.

Ejecuta este comando para entrar a la consola del proyecto:
```bash
docker exec -it homeland-app bash
```

Una vez dentro (verás que el prompt cambia), ejecuta estos 4 comandos en orden:

```bash
# 1. Instalar librerías de PHP (Esto puede tardar un poco)
composer install

# 2. Generar la llave de seguridad de la app
php artisan key:generate

# 3. Crear las tablas en la base de datos
php artisan migrate

# 4. Vincular la carpeta de imágenes públicas
php artisan storage:link
```

Cuando termines, escribe `exit` para salir del contenedor.

---

##  ¡Listo! A probar la App

Abre tu navegador favorito e ingresa a la siguiente dirección:

**http://localhost:8080**

Deberías ver el Dashboard de bienvenida de Homeland.

---

##  Comandos Útiles / Solución de Problemas

**¿Cómo detengo la aplicación?**
```bash
docker-compose down
```

**¿Cómo vuelvo a iniciarla mañana?**
Solo necesitas abrir Docker Desktop y ejecutar:
```bash
docker-compose up -d
```

**Error: "Connection Refused" o problema de base de datos**
1. Asegúrate de que tu XAMPP/MySQL local esté **encendido**.
2. Verifica que en el archivo `.env` la contraseña (`DB_PASSWORD`) sea la correcta.
3. Asegúrate de haber creado la base de datos `homeland_catalog` manualmente.

**Error de permisos en las carpetas (Linux/Mac)**
Si tienes problemas para subir imágenes, ejecuta esto dentro del contenedor:
```bash
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage
```

---
*Documentación generada para Homeland Project - 2025*
