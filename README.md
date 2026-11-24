# Lista de tareas

Aplicación de lista de tareas (to-do list) creada como demo de un curso sobre agentes y skills en Cursor.

- `backend/`: API REST en PHP 8.3 con Symfony 7.4, Doctrine ORM y SQLite.
- `frontend/`: SPA en Angular 22 que consume la API.

La definición del proyecto está en [`AGENTS.md`](AGENTS.md), el análisis y diseño en [`docs/analisis-diseño.md`](docs/analisis-diseño.md) y la bitácora del curso en [`docs/`](docs/README.md).

## Requisitos

| Herramienta | Versión | Instalación en Windows |
|---|---|---|
| PHP | 8.3 con las extensiones `pdo_sqlite`, `sqlite3`, `intl`, `mbstring`, `openssl`, `curl`, `zip` y `fileinfo` | `winget install PHP.PHP.8.3` y activarlas en `php.ini` |
| Composer | 2.x | [getcomposer.org](https://getcomposer.org/download/) |
| Node.js | 22.22, 24.15 o superior, con npm (lo exige Angular 22) | `winget install OpenJS.NodeJS.LTS` |

No hace falta instalar SQLite aparte: PHP lo trae con la extensión `pdo_sqlite`.

## Arranque

### 1. Backend (`http://127.0.0.1:8000`)

```bash
cd backend
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php -S 127.0.0.1:8000 -t public
```

La base de datos de desarrollo se crea en `backend/var/data_dev.db`. Para cambiar la configuración local (por ejemplo `DATABASE_URL`), usar `backend/.env.local`, que no se versiona.

### 2. Frontend (`http://localhost:4200`)

En otra terminal:

```bash
cd frontend
npm install
npm start
```

`npm start` reenvía las peticiones a `/api` hacia el backend (`frontend/proxy.conf.json`), así que el backend tiene que estar arrancado.

## Calidad y tests

### Backend (`cd backend`)

```bash
php bin/phpunit                        # Unit, Integration y Functional (SQLite en var/data_test.db)
php bin/phpunit --testsuite=Unit
vendor/bin/phpstan analyse             # nivel 8
vendor/bin/php-cs-fixer fix
```

### Frontend (`cd frontend`)

```bash
npm run lint
npm run format:check
npm run build
```

## API

Prefijo `/api`, JSON en peticiones y respuestas.

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/tasks` | Listar (filtros `status`, `priority`; paginación `page`, `limit`) |
| GET | `/api/tasks/{id}` | Detalle |
| POST | `/api/tasks` | Crear |
| PUT | `/api/tasks/{id}` | Actualizar título, descripción, prioridad y fecha límite |
| PATCH | `/api/tasks/{id}/status` | Cambiar estado |
| DELETE | `/api/tasks/{id}` | Eliminar |

Los errores siempre tienen el formato `{ "error": { "code", "message", "details" } }`. Los códigos están en la sección 5 de [`docs/analisis-diseño.md`](docs/analisis-diseño.md).
