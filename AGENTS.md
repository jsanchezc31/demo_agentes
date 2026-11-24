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
| Framework backend | Symfony 7.x |
| Persistencia | Doctrine ORM + Doctrine Migrations |
| Base de datos | PostgreSQL (SQLite en tests) |
| Frontend | Angular 18+ (standalone components, signals) |
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
symfony server:start                         # o: php -S localhost:8000 -t public
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
npm start          # ng serve con proxy a http://localhost:8000/api
npm run build
npm run lint
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

## Bitácora en `docs/`

Cuando el usuario lo pida, documentar el paso dado y lo aprendido:

- Un fichero por paso: `docs/NN-titulo-en-kebab-case.md` (numeración correlativa).
- Secciones: objetivo, qué hicimos, lo aprendido.
- Añadir el paso a la tabla de `docs/README.md`.
- Imágenes en `docs/img/`.

## Lista de tareas inicial

- [ ] Crear estructura `backend/` con Symfony 7 (skeleton + ORM pack + validator + serializer).
- [ ] Configurar Doctrine con PostgreSQL y SQLite para tests.
- [ ] Configurar PHPUnit, PHPStan y PHP-CS-Fixer.
- [ ] Modelar entidad `Task` en dominio y su mapeo Doctrine.
- [ ] Crear migración inicial.
- [ ] Implementar casos de uso: crear, listar, obtener, actualizar, cambiar estado, eliminar.
- [ ] Exponer endpoints REST y manejo uniforme de errores.
- [ ] Tests unitarios, de integración y funcionales del backend.
- [ ] Configurar CORS para el frontend.
- [ ] Crear estructura `frontend/` con Angular (standalone, routing, ESLint, Prettier).
- [ ] Servicio HTTP de tareas y modelos tipados.
- [ ] Pantallas: listado con filtros, alta, edición y cambio de estado.
- [ ] Proxy de desarrollo hacia la API.
- [ ] Documentar arranque del proyecto en `README.md`.

## Qué no hacer

- No hacer commit ni push sin que se pida.
- No modificar `.env` con secretos reales; usar `.env.local`.
- No introducir dependencias nuevas sin justificarlo.
- No saltarse los tests ni desactivar reglas de PHPStan o ESLint para que pase el build.
- No generar archivos `*.spec.ts` salvo petición explícita.
