# backend_joticius

Microservicios de CargamEsta con Slim 4 y Eloquent.

## Requisitos previos
- PHP 7.4 o superior
- Composer instalado
- MySQL con la base de datos `cargamesta` importada

## Instalación común
Dentro de cada carpeta de microservicio ejecutar:

```bash
composer install
composer dump-autoload
```

Si Composer falla por auditoría de seguridad, puede usar:

```bash
composer update --no-audit
```

## Variables de entorno opcionales
Crear un archivo `.env` en la raíz de cada microservicio con:

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=cargamesta
DB_USER=root
DB_PASSWORD=
APP_ENV=local
APP_DEBUG=true
```

> Nota: cada microservicio puede tener su propio archivo `.env` local en su carpeta raíz. Si existe, éste se usa para carga de configuración en lugar de valores globales.

## Ejecución local
Abrir una terminal por microservicio:

- Terminal 1: `ms-auth` (8000)
  ```bash
  cd ms_auth
  php -S localhost:8001 -t public
  ```

- Terminal 2: `ms-conductores` (8001)
  ```bash
  cd ms_conductores
  php -S localhost:8002 -t public
  ```

- Terminal 3: `ms-vehiculos` (8002)
  ```bash
  cd ms_vehiculos
  php -S localhost:8003 -t public
  ```

- Terminal 4: `ms-rutas` (8003)
  ```bash
  cd ms_rutas
  php -S localhost:8004 -t public
  ```

- Terminal 5: `ms-viajes` (8004)
  ```bash
  cd ms_viajes
  php -S localhost:8005 -t public
  ```

## `ms_viajes` - API de seguimientos de viajes
Base URL: `http://localhost:8004`

### Endpoints
- `GET /` - Health check
- `GET /viajes` - Listar registros
  - Query params opcionales: `programacion_viaje_id`, `estado`, `fecha`, `novedad`
- `GET /viajes/{id}` - Obtener seguimiento por ID
- `POST /viajes` - Crear nuevo seguimiento
- `PUT /viajes/{id}` - Actualizar seguimiento
- `DELETE /viajes/{id}` - Eliminar seguimiento

### Modelo de datos
Tabla: `seguimientos_viajes`
Campos principales:
- `programacion_viaje_id`
- `fecha`
- `hora`
- `estado`
- `novedad`

### Ejemplos curl
- Health check:
  ```bash
  curl http://localhost:8004/
  ```

- Listar registros:
  ```bash
  curl http://localhost:8004/viajes
  ```

- Crear seguimiento:
  ```bash
  curl -X POST http://localhost:8004/viajes \
    -H "Content-Type: application/json" \
    -d '{"programacion_viaje_id":1,"fecha":"2026-06-20","hora":"08:00:00","estado":"programado","novedad":"Inicio de ruta"}'
  ```

- Actualizar seguimiento:
  ```bash
  curl -X PUT http://localhost:8004/viajes/1 \
    -H "Content-Type: application/json" \
    -d '{"estado":"en_transito","novedad":"En camino"}'
  ```

- Eliminar seguimiento:
  ```bash
  curl -X DELETE http://localhost:8004/viajes/1
  ```

## Notas
- Asegúrate de tener la base de datos `cargamesta` importada antes de iniciar los microservicios.
- Si trabajas con XAMPP, usa el `php` de `C:\xampp\php` o configura PATH para que lo detecte.
