# Banco Central - Sistema Bancario Distribuido

API REST y panel administrativo del Banco Central del sistema bancario distribuido. Núcleo de cuentas, transacciones y gestión de nodos (sucursales y cajeros automáticos).

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)](https://php.net)
[![Supabase](https://img.shields.io/badge/Supabase-PostgreSQL-3ECF8E?logo=supabase&logoColor=white)](https://supabase.com)
[![Render](https://img.shields.io/badge/Render-Deployed-46E3B7?logo=render&logoColor=white)](https://render.com)

---

## Descripción

Sistema bancario distribuido compuesto por 3 nodos independientes:

- **Banco Central** (este repositorio): Laravel + Supabase
- **Nodo Sucursal**: Express + Vercel
- **Nodo ATM**: Express + Vercel

Los nodos se comunican vía API REST autenticada con API Keys emitidas por el Banco Central. Todos comparten la misma base de datos centralizada en Supabase (PostgreSQL).

---

## Link del despliegue

**Producción:** https://banco-central.onrender.com

| Recurso | URL |
|---------|-----|
| API (URL base) | https://banco-central.onrender.com/api |
| Health check | https://banco-central.onrender.com/api/health |
| Panel Admin | https://banco-central.onrender.com/admin/login |
| Documentación (Swagger UI) | https://banco-central.onrender.com/docs |
| Especificación OpenAPI (YAML) | https://banco-central.onrender.com/openapi.yaml |

> **Nota:** el plan Free de Render duerme el servicio tras un rato sin tráfico; la primera petición puede tardar unos segundos en responder.

---

## Arquitectura del sistema

```text
+------------------------------------------------------------------------+
|                    SISTEMA BANCARIO DISTRIBUIDO                        |
+------------------------------------------------------------------------+

              +------------------------------+
              |      NODO 2: SUCURSAL        |
              |      Express + Vercel        |
              |                              |
              |  - Abrir cuentas             |
              |  - Historial local           |
              |  - Transferencias            |
              |  - Reportes administrativos  |
              +--------------+---------------+
                             |
                             | API Key: sk_branch_...
                             | HTTPS
                             v
+-----------------------------------------------------------+
|                                                           |
|              NODO 1: BANCO CENTRAL (Laravel)              |
|                                                           |
|   +-----------------+      +----------------------+       |
|   |    API REST     |      | Panel Admin (Blade)  |       |
|   | - /api/health   |      | - Dashboard          |       |
|   | - /api/accounts |      | - Nodos              |       |
|   | - /api/balance  |      | - Cuentas            |       |
|   | - /api/transfer |      | - Transacciones      |       |
|   | - /api/withdraw |      | - Movimientos efect. |       |
|   | - /api/deposit  |      | - Reportes CSV/PDF   |       |
|   | - /api/nodes/*  |      |                      |       |
|   +--------+--------+      +----------+-----------+       |
|            |                          |                   |
|            +------------+-------------+                   |
|                         v                                 |
|           +---------------------------+                   |
|           |      Middleware Auth      |                   |
|           | - API Key (nodos)         |                   |
|           | - Supabase Auth (admins)  |                   |
|           +-------------+-------------+                   |
|                         v                                 |
|           +---------------------------+                   |
|           |    Servicios de negocio   |                   |
|           | - AccountService          |                   |
|           | - TransactionService      |                   |
|           | - NodeService             |                   |
|           | - CashService             |                   |
|           | - ExportService           |                   |
|           +-------------+-------------+                   |
|                         v                                 |
|           +----------------------------+                  |
|           |  RPC Functions (Postgres)  |                  |
|           | - process_bank_transaction |                  |
|           | - adjust_node_cash         |                  |
|           +-------------+--------------+                  |
|                         v                                 |
|           +---------------------------+                   |
|           |    Supabase PostgreSQL    |                   |
|           |        + RLS + Auth       |                   |
|           +---------------------------+                   |
+-----------------------------------------------------------+
                             ^
                             |
                             | API Key: sk_atm_...
                             | HTTPS
                             |
              +--------------+---------------+
              |         NODO 3: ATM          |
              |      Express + Vercel        |
              |                              |
              |  - Retiros                   |
              |  - Depósitos                 |
              |  - Consulta de saldo         |
              |  - Control de efectivo       |
              +------------------------------+
```

### Responsabilidades por nodo

| Nodo | Responsabilidad | Stack | Despliegue |
|------|----------------|-------|------------|
| Banco Central | Núcleo de cuentas, transacciones y gestión de nodos | Laravel + Supabase | Render |
| Sucursal | Apertura de cuentas, historial local, reportes | Express | Vercel |
| ATM | Retiros, depósitos, consultas | Express | Vercel |

---

## Stack tecnológico

### Banco Central (este repositorio)

- **Backend:** Laravel + PHP 8.4
- **Base de datos:** Supabase (PostgreSQL 17)
- **Autenticación admin:** Supabase Auth
- **Autenticación nodos:** API Keys (SHA-256)
- **Panel admin:** Blade + CSS puro
- **PDF:** barryvdh/laravel-dompdf
- **Documentación:** OpenAPI 3.0 (l5-swagger)
- **Despliegue:** Render (Docker)

### Nodos Sucursal y ATM

- **Backend:** Express.js (Node.js)
- **Cliente HTTP:** fetch / axios
- **Despliegue:** Vercel

---

## Endpoints del Banco Central

URL base: `https://banco-central.onrender.com`
URL base api: `https://banco-central.onrender.com/api`

### Públicos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/health` | Estado del nodo |

### Autenticados con API Key

| Método | Endpoint | Quién | Descripción |
|--------|----------|-------|-------------|
| GET | `/nodes/me` | branch, atm | Datos del nodo autenticado |
| GET | `/nodes/me/transactions` | branch, atm | Historial del nodo |
| GET | `/nodes/me/reports` | branch, atm | Reporte administrativo |
| GET | `/accounts/search?q=...` | branch, atm | Buscar cuentas |
| GET | `/accounts/{num}/transactions` | branch, atm | Historial por cuenta |
| GET | `/balance/{num}` | branch, atm | Consultar saldo |
| POST | `/accounts` | branch | Abrir cuenta |
| POST | `/transfer` | branch | Transferir entre cuentas |
| POST | `/withdraw` | atm | Retiro |
| POST | `/deposit` | atm | Depósito |

### Headers requeridos

```http
Content-Type: application/json
X-API-Key: sk_branch_...        (o sk_atm_...)
Idempotency-Key: <uuid-v4>      (opcional, para transfer/withdraw/deposit)
```

---

## Instalación local

### Requisitos

- PHP 8.4+
- Composer
- Cuenta en Supabase
- Docker (para producción)

### Pasos

```bash
# 1. Clonar
git clone https://github.com/4bram/banco-central.git
cd banco-central

# 2. Instalar dependencias
composer install

# 3. Copiar el .env
cp .env.example .env

# 4. Generar APP_KEY
php artisan key:generate

# 5. Configurar el .env con las credenciales de Supabase

# 6. Levantar el servidor
php artisan serve --host=0.0.0.0 --port=8001
```

Acceder a:

- API: http://localhost:8001/api/health
- Panel: http://localhost:8001/admin/login
- Swagger: http://localhost:8001/api/documentation
- Documentación OpenAPI: http://localhost:8001/docs
- Especificación: http://localhost:8001/openapi.yaml

---

## Variables de entorno

```env
APP_NAME="Banco Central"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://banco-central.onrender.com
APP_TIMEZONE=America/Mexico_City
APP_LOCALE=es

LOG_CHANNEL=stack
LOG_LEVEL=error

# Supabase PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=aws-0-us-east-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.PROJECT-REF
DB_PASSWORD=...
DB_SSL_MODE=require

# Supabase API
SUPABASE_URL=https://PROJECT-REF.supabase.co
SUPABASE_PUBLISHABLE_KEY=sb_publishable_...
SUPABASE_SECRET_KEY=sb_secret_...

# Configuración del nodo
NODE_ID=banco-central
NODE_URL=https://banco-central.onrender.com

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

> **Importante:** nunca subas el archivo `.env` al repositorio. Las claves reales (`DB_PASSWORD`, `SUPABASE_SECRET_KEY`, `APP_KEY`) solo se configuran en Render.

---

## Despliegue

### Render (Banco Central)

1. Crear cuenta en Render con GitHub.
2. **New +** -> **Web Service**.
3. Conectar el repositorio `banco-central`.
4. Configurar:
   - **Language:** Docker
   - **Branch:** main
   - **Region:** Virginia (US East)
   - **Instance Type:** Free
   - **Health Check Path:** `/api/health`
   - **Auto-Deploy:** Yes
5. Agregar todas las variables de entorno.
6. **Create Web Service**.

El `Dockerfile` se encarga de todo (instalar PHP, extensiones, composer, arrancar nginx + php-fpm).

### Vercel (Sucursal y ATM)

1. Crear cuenta en Vercel con GitHub.
2. **Add New Project** -> importar el repo del nodo.
3. Configurar variables de entorno:

   ```env
   BANCO_URL=https://banco-central.onrender.com
   BANCO_API_KEY=sk_branch_...   # o sk_atm_...
   NODO_ID=<uuid-del-nodo>
   ```

4. **Deploy**.

---

## Pruebas y demo

### Flujo completo del examen

```bash
# 1. El admin del Banco Central crea la sucursal y el ATM
#    (desde el panel: /admin/nodes/create)

# 2. La sucursal abre una cuenta nueva
curl -X POST https://banco-central.onrender.com/api/accounts \
  -H "Content-Type: application/json" \
  -H "X-API-Key: sk_branch_..." \
  -d '{
    "account_number": "10000001",
    "holder_name": "Carlos Ramirez",
    "pin": "1234",
    "initial_balance": 1000
  }'

# 3. Verificar saldo
curl https://banco-central.onrender.com/api/balance/10000001 \
  -H "X-API-Key: sk_branch_..."

# 4. El ATM retira $300
curl -X POST https://banco-central.onrender.com/api/withdraw \
  -H "Content-Type: application/json" \
  -H "X-API-Key: sk_atm_..." \
  -H "Idempotency-Key: 550e8400-e29b-41d4-a716-446655440001" \
  -d '{
    "account_number": "10000001",
    "pin": "1234",
    "amount": 300
  }'

# 5. Verificar saldo final ($700)
curl https://banco-central.onrender.com/api/balance/10000001 \
  -H "X-API-Key: sk_atm_..."
```

### Prueba de idempotencia

Enviar dos veces la misma petición con la misma `Idempotency-Key`: la segunda no vuelve a aplicar la operación.

```bash
# Primera petición
curl -X POST https://banco-central.onrender.com/api/withdraw \
  -H "Content-Type: application/json" \
  -H "X-API-Key: sk_atm_..." \
  -H "Idempotency-Key: 550e8400-e29b-41d4-a716-446655440001" \
  -d '{"account_number": "10000001", "pin": "1234", "amount": 300}'
# -> replayed: false

# Segunda petición con la misma key
curl -X POST https://banco-central.onrender.com/api/withdraw \
  -H "Content-Type: application/json" \
  -H "X-API-Key: sk_atm_..." \
  -H "Idempotency-Key: 550e8400-e29b-41d4-a716-446655440001" \
  -d '{"account_number": "10000001", "pin": "1234", "amount": 300}'
# -> replayed: true (mismo transaction_id, mismo saldo)
```

---

## Estructura del repositorio

```text
banco-central/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          <- Panel admin (Blade)
│   │   │   └── Api/            <- API REST (JSON)
│   │   └── Middleware/
│   │       ├── AdminAuth.php
│   │       └── AuthenticateApiKey.php
│   ├── Models/                 <- 8 modelos Eloquent
│   └── Services/               <- Lógica de negocio
├── bootstrap/
├── config/
├── database/
├── docker/
│   ├── nginx.conf
│   └── start.sh
├── resources/
│   └── views/
│       ├── admin/              <- Vistas del panel
│       ├── auth/               <- Login
│       └── layouts/
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
├── Dockerfile
├── composer.json
└── README.md
```

---

## Equipo

| Rol | Nombre | 
|-----|--------|
| Banco Central | Abraham y Gael |
| Nodo Sucursal | Hugo | 
| Nodo ATM | Jesús | 
