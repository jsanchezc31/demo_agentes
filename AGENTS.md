# AGENTS.md

Definición del proyecto para el agente de Cursor. Responde siempre en español.

Las convenciones detalladas por capa están en `.cursor/rules/`.

## Proyecto

Aplicación de **lista de tareas** (to-do list):

- **Backend**: API REST en PHP con Symfony y Doctrine ORM.
- **Frontend**: SPA en Angular que consume la API.
- **Tests**: PHPUnit en backend.

## Stack

| Capa | Tecnología |
|------|------------|
| Lenguaje backend | PHP 8.3 |
| Framework backend | Symfony 7.4 |
| Persistencia | Doctrine ORM + Doctrine Migrations |
| Base de datos | SQLite en desarrollo y en tests (un fichero por entorno en `backend/var/`) |
| Frontend | Angular 22 (standalone components, signals, zoneless) |
| Tests backend | PHPUnit, Symfony WebTestCase |
| Calidad | PHP-CS-Fixer, PHPStan (nivel 8), ESLint, Prettier |

## Estructura del repositorio

```
/
├── backend/                          # Proyecto Symfony
│   ├── src/
│   │   ├── Domain/Task/              # Entidad, value objects, enums, excepciones, interfaz de repositorio
│   │   ├── Application/Task/         # Casos de uso (commands/queries + handlers), DTOs
│   │   ├── Infrastructure/Persistence/Doctrine/
│   │   │   ├── Mapping/              # Mapeo XML de entidades
│   │   │   └── Repository/           # Implementaciones Doctrine de los repositorios
│   │   └── UI/Http/
│   │       ├── Controller/           # Un controlador invocable por endpoint
│   │       ├── Request/              # DTOs de entrada validados
│   │       └── EventListener/        # Traducción de excepciones a errores JSON
│   ├── migrations/
│   ├── config/
│   └── tests/
│       ├── Unit/
│       ├── Integration/
│       ├── Functional/
│       └── Mother/                   # Object Mothers para construir entidades
├── frontend/                         # Proyecto Angular
│   └── src/
│       ├── app/
│       │   ├── core/
│       │   │   ├── models/           # Interfaces y tipos (Task, TaskStatus...)
│       │   │   └── interceptors/     # Interceptores HTTP (errores)
│       │   ├── features/tasks/
│       │   │   ├── pages/            # Componentes enrutados (listado, alta, edición)
│       │   │   ├── components/       # Componentes de presentación
│       │   │   └── services/         # Servicio HTTP de tareas
│       │   └── shared/
│       │       ├── components/       # Componentes reutilizables sin lógica de negocio
│       │       └── pipes/
│       └── environments/             # URL de la API por entorno
├── docs/
│   ├── NN-titulo.md                  # Bitácora del curso, un fichero por paso
│   ├── funcional/                    # Documentación funcional que origina los issues de GitHub
│   └── img/
└── .cursor/
    ├── rules/               # Reglas del agente por capa
    └── skills/              # Skills del proyecto
```

## Dominio

Entidad principal `Task`:

- `id` (UUID)
- `title` (obligatorio, 1–255 caracteres)
- `description` (opcional)
- `status`: `pending` | `in_progress` | `done`
- `priority`: `low` | `medium` | `high`
- `dueDate` (opcional)
- `createdAt`, `updatedAt`

Reglas de negocio:

- Una tarea nueva se crea en estado `pending`.
- Una tarea `done` no puede volver a `pending` sin pasar por `in_progress`.
- `title` no puede quedar vacío tras editar.

## API REST

Prefijo `/api`, JSON en peticiones y respuestas.

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/tasks` | Listar (filtros `status`, `priority`; paginación `page`, `limit`) |
| GET | `/api/tasks/{id}` | Detalle |
| POST | `/api/tasks` | Crear |
| PUT | `/api/tasks/{id}` | Actualizar |
| PATCH | `/api/tasks/{id}/status` | Cambiar estado |
| DELETE | `/api/tasks/{id}` | Eliminar |

## Comandos

### Backend (`cd backend`)

```bash
composer install
php -S 127.0.0.1:8000 -t public              # o: symfony server:start
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
php bin/phpunit
php bin/phpunit --testsuite=Unit
vendor/bin/phpstan analyse
vendor/bin/php-cs-fixer fix
```

### Frontend (`cd frontend`)

```bash
npm install
npm start          # ng serve con proxy de /api a http://127.0.0.1:8000
npm run build
npm run lint
npm run format     # Prettier
```

## Reglas de Cursor

| Regla | Se aplica |
|-------|-----------|
| `backend-php.mdc` | Al trabajar con `backend/**/*.php` |
| `doctrine.mdc` | En `backend/src/Infrastructure/**` y `backend/migrations/**` |
| `api-rest.mdc` | En `backend/src/UI/Http/**` |
| `tests-backend.mdc` | En `backend/tests/**` |
| `frontend-angular.mdc` | En `frontend/src/**` |
| `flujo-github.mdc` | Cuando la tarea parte de un issue de GitHub o termina en rama/PR |

## Skills de Cursor

| Skill | Uso |
|-------|-----|
| `commit-message` | Redactar el mensaje de cada commit (`tipo: descripción`) |
| `bitacora` | Documentar cada paso en `docs/` y retomar el trabajo en un chat nuevo |
| `dto` | Generar los DTOs de la API y los modelos TypeScript a partir de `docs/analisis-diseño.md` |

## Bitácora en `docs/`

Cada paso se documenta en `docs/` con la skill `bitacora`. Para retomar el trabajo en un chat nuevo, empezar por `HANDOFF.md`.

## Lista de tareas inicial

- [x] Crear estructura `backend/` con Symfony 7 (skeleton + ORM pack + validator + serializer).
- [x] Configurar Doctrine con SQLite (desarrollo y tests).
- [x] Configurar PHPUnit, PHPStan y PHP-CS-Fixer.
- [x] Modelar entidad `Task` en dominio y su mapeo Doctrine.
- [x] Crear migración inicial.
- [x] Implementar casos de uso: crear, listar, obtener, actualizar, cambiar estado, eliminar.
- [x] Exponer endpoints REST y manejo uniforme de errores.
- [x] Tests unitarios, de integración y funcionales del backend.
- [x] Configurar CORS para el frontend.
- [x] Crear estructura `frontend/` con Angular (standalone, routing, ESLint, Prettier).
- [x] Servicio HTTP de tareas y modelos tipados.
- [x] Pantallas: listado con filtros, alta, edición y cambio de estado.
- [x] Proxy de desarrollo hacia la API.
- [x] Documentar arranque del proyecto en `README.md`.

## Qué no hacer

- No hacer commit ni push sin que se pida.
- No modificar `.env` con secretos reales; usar `.env.local`.
- No introducir dependencias nuevas sin justificarlo.
- No saltarse los tests ni desactivar reglas de PHPStan o ESLint para que pase el build.
- No generar archivos `*.spec.ts` salvo petición explícita.
