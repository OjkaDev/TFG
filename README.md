# Sistema de Fichaje - Time Tracking Application

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat-square&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue-3.5-4FC08D?style=flat-square&logo=vue.js)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-AGPL--3.0-blue?style=flat-square)


Una aplicación web completa y flexible diseñada para que cada usuario pueda registrarse, gestionar sus horas trabajadas y obtener el cálculo automático de su salario en función de sus propias condiciones laborales. Permite configurar distintos tipos de jornada, contratos parciales y parámetros específicos como horas complementarias, nocturnas o bonificaciones adicionales. Está especialmente pensada para entornos donde la nómina depende de múltiples variables y se requiere un control preciso y personalizado del tiempo trabajado.

## Características

✨ **Registro de Horas**
- Interfaz intuitiva para marcar entrada y salida.
- Historial completo de fichajes.
- Validación automática de registros.

📊 **Dashboard**
- Resumen global de actividad laboral.
- Resumen por empresa, jornada o tipo de hora.
- Vista rápida del progreso mensual hacia el salario esperado.

⚙️ **Configuración**
- Definición de múltiples trabajos/empresas por usuario.
- Parámetros personalizados para cada configuración: horas complementarias, nocturnas, festivas, bonificaciones y más.
- Ajuste flexible de la nómina: rango de fechas, horas de contrato, cálculos específicos.

👤 **Gestión de Usuarios**
- Autenticación segura con Sanctum.
- Cada usuario gestiona sus propios trabajos, configuraciones y fichajes.
- Datos totalmente independientes entre usuarios.

📈 **Cálculo de Nóminas**
- Cálculo automático del salario según las condiciones de cada trabajo.
- Distinción entre distintos tipos de horas y sus valores correspondientes.

## Requisitos Previos

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **npm** o **yarn**
- **SQLite** o **MySQL** (base de datos)
- **Git**

## Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/OjkaDev/TFG.git
cd TFG
```

### 2. Configurar Backend (Laravel)

```bash
# Instalar dependencias de PHP
composer install

# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Crear base de datos SQLite (opcional, si no usas MySQL)
touch database/database.sqlite

# Ejecutar migraciones
php artisan migrate

# (Opcional) Ejecutar seeders
php artisan db:seed
```

### 3. Configurar Frontend (Vue.js)

```bash
# Instalar dependencias de Node.js
npm install

# Compilar assets
npm run build

# O para desarrollo con hot reload
npm run dev
```

### 4. Configurar Variables de Entorno

Editar `.env` y configurar:

```env
APP_NAME="Sistema de Fichaje"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
# o para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tfg_vue
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=cookie
SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

## Uso

### Desarrollo Local

```bash
# Terminal 1: Iniciar servidor Laravel
php artisan serve

# Terminal 2: Compilar assets con Vite
npm run dev
```

Acceder a `http://localhost:8000`

## Estructura del Proyecto

```
tfg_vue/
├── app/                          # Código PHP/Laravel
│   ├── Events/                   # Eventos
│   ├── Http/Controllers/         # Controladores
│   ├── Listeners/                # Escuchadores de eventos
│   ├── Models/                   # Modelos Eloquent
│   └── Providers/                # Proveedores de servicios
├── resources/
│   ├── css/                      # Estilos Tailwind CSS
│   ├── js/
│   │   ├── pages/                # Componentes de páginas Vue
│   │   ├── composables/          # Lógica reutilizable
│   │   ├── app.vue              # Componente raíz
│   │   └── bootstrap.js         # Inicialización
│   └── views/                    # Vistas Blade
├── database/
│   ├── migrations/               # Migraciones de BD
│   ├── seeders/                  # Datos iniciales
│   └── factories/                # Factorías de prueba
├── routes/
│   ├── api.php                   # Rutas API
│   ├── web.php                   # Rutas web
│   └── console.php               # Comandos
├── tests/                        # Tests unitarios y funcionales
├── storage/                      # Archivos, logs, sesiones
├── public/                       # Punto de entrada público
├── composer.json                 # Dependencias PHP
├── package.json                  # Dependencias Node.js
├── vite.config.js               # Configuración Vite
├── tailwind.config.js           # Configuración Tailwind
└── .env                         # Variables de entorno
```

## Tecnologías Utilizadas

- **Backend**
  - [Laravel 12](https://laravel.com) - Framework PHP
  - [Sanctum](https://laravel.com/docs/sanctum) - Autenticación API
  - [Eloquent ORM](https://laravel.com/docs/eloquent) - ORM
  - [Spatie Permission](https://spatie.be/docs/laravel-permission) - Gestión de permisos

- **Frontend**
  - [Vue.js 3](https://vuejs.org) - Framework JavaScript
  - [Vue Router 4](https://router.vuejs.org) - Enrutamiento
  - [Vite](https://vitejs.dev) - Build tool
  - [Tailwind CSS](https://tailwindcss.com) - Framework CSS
  - [Day.js](https://day.js.org) - Librería de fechas

- **Base de Datos**
  - SQLite (desarrollo)
  - MySQL/PostgreSQL (producción)

## Pruebas

```bash
# Ejecutar tests unitarios
php artisan test

# Ejecutar tests con coverage
php artisan test --coverage
```


## Licencia

Este proyecto está licenciado bajo la licencia GNU AFFERO GENERAL PUBLIC LICENSE - ver el archivo [LICENSE](LICENSE) para más detalles.


## 💬 Contacto

Si quieres contactar conmigo para ver más proyectos o colaborar:

* **GitHub:** [https://github.com/OjkaDev](https://github.com/OjkaDev)
* **LinkedIn:** *www.linkedin.com/in/óscar-calvellido-gil-145522207*

---


**Última actualización:** Mayo 2025
