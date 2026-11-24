# Análisis y diseño — Lista de tareas

## 1. Objetivo del proyecto

Aplicación web de gestión de tareas (to-do list) construida como demo didáctica para el curso sobre agentes y skills en Cursor.

Permite crear, consultar, filtrar, editar, cambiar de estado y eliminar tareas. Un backend Symfony expone una API REST JSON que consume una SPA en Angular.

El objetivo prioritario es mostrar una arquitectura por capas limpia y reglas de negocio bien testeadas, más que añadir muchas funcionalidades.

---

## 2. Stack tecnológico

| Tecnología | Versión | Rol |
|---|---|---|
| PHP | 8.3 | Lenguaje del backend |
| Symfony | 7.4 | Framework de la API REST |
| Doctrine ORM + Migrations | ORM 3.x, DBAL 4.x | Persistencia y evolución del esquema |
| SQLite | 3.x | Base de datos de desarrollo (`var/data_dev.db`) y de tests (`var/data_test.db`) |
| PostgreSQL | — | Opcional para producción; el esquema no usa nada específico de SQLite |
| Angular | 22 | SPA (standalone components, signals, zoneless) |
| PHPUnit + WebTestCase | 12 | Tests unitarios, de integración y funcionales |
| PHP-CS-Fixer, PHPStan (nivel 8), ESLint, Prettier | — | Calidad de código |

---

## 3. Arquitectura

### Backend: capas

Arquitectura por capas con el dominio aislado del framework. Cada caso de uso es un command/query con su handler.

| Capa | Carpeta | Responsabilidad |
|---|---|---|
| Dominio | `src/Domain/Task/` | Entidad `Task`, value objects, enums, excepciones e interfaz `TaskRepository`. Sin dependencias de Symfony ni Doctrine |
| Aplicación | `src/Application/Task/` | Casos de uso (commands/queries + handlers) y DTOs de salida. Solo depende del dominio |
| Infraestructura | `src/Infrastructure/Persistence/Doctrine/` | Mapeo XML de `Task` y `DoctrineTaskRepository` |
| UI HTTP | `src/UI/Http/` | Controladores invocables, DTOs de entrada validados y `ExceptionListener` |

```
backend/
├── src/
│   ├── Domain/Task/
│   │   ├── Task.php
│   │   ├── TaskId.php
│   │   ├── TaskTitle.php
│   │   ├── TaskDueDate.php
│   │   ├── TaskStatus.php
│   │   ├── TaskPriority.php
│   │   ├── TaskRepository.php
│   │   └── Exception/
│   │       ├── TaskNotFound.php
│   │       ├── InvalidTaskId.php
│   │       ├── InvalidTaskTitle.php
│   │       ├── InvalidTaskDueDate.php
│   │       └── InvalidTaskTransition.php
│   ├── Application/Task/
│   │   ├── Create/      # CreateTaskCommand + CreateTaskHandler
│   │   ├── List/        # ListTasksQuery + ListTasksHandler
│   │   ├── Get/         # GetTaskQuery + GetTaskHandler
│   │   ├── Update/      # UpdateTaskCommand + UpdateTaskHandler
│   │   ├── ChangeStatus/# ChangeTaskStatusCommand + ChangeTaskStatusHandler
│   │   ├── Delete/      # DeleteTaskCommand + DeleteTaskHandler
│   │   ├── TaskResponse.php
│   │   └── TaskPageResponse.php
│   ├── Infrastructure/Persistence/Doctrine/
│   │   ├── Mapping/Task.orm.xml
│   │   ├── Repository/DoctrineTaskRepository.php
│   │   └── Type/TaskIdType.php   # Tipo DBAL `task_id`
│   └── UI/Http/
│       ├── Controller/  # Un controlador por endpoint
│       ├── Request/     # CreateTaskRequest, UpdateTaskRequest, ChangeTaskStatusRequest, ListTasksRequest
│       └── EventListener/ExceptionListener.php + ApiError.php
├── migrations/
└── tests/
    ├── Unit/
    ├── Integration/
    ├── Functional/
    ├── Mother/TaskMother.php
    ├── Double/InMemoryTaskRepository.php
    └── Support/RecreatesDatabaseSchema.php
```

**Reglas de diseño (backend):**

- `declare(strict_types=1);`, clases `final` y propiedades `readonly` siempre que sea posible. Excepción: la entidad `Task` no es `final` porque Doctrine genera proxies que la extienden.
- Las reglas de negocio viven en la entidad `Task`, no en los handlers ni en los controladores.
- Los handlers tienen un único método `__invoke` y devuelven DTOs (`TaskResponse`), nunca la entidad.
- Los controladores solo validan la entrada (`#[MapRequestPayload]`, `#[MapQueryString]`), invocan el handler y serializan.
- Excepciones de dominio específicas; nunca `\Exception` genérica.

### Frontend: organización

```
frontend/src/app/
├── core/
│   ├── models/        # Task, TaskStatus, TaskPriority, Page<T>, ApiError
│   └── interceptors/  # Interceptor de errores HTTP
├── features/tasks/
│   ├── pages/         # TaskListPage, TaskCreatePage, TaskEditPage
│   ├── components/    # TaskItem, TaskForm, TaskFilters, TaskStatusSelect
│   └── services/      # TaskService (única capa que llama a la API)
└── shared/
    ├── components/    # Paginador, mensajes, etc.
    └── pipes/         # Etiquetas de estado y prioridad
```

**Reglas de diseño (frontend):**

- Componentes standalone con `ChangeDetectionStrategy.OnPush` y estado con signals.
- Inyección con `inject()`; control de flujo nativo (`@if`, `@for` con `track`).
- Modelos tipados en `core/models`; prohibido `any`.
- La URL de la API sale de `environments/` (`/api`); en desarrollo, `npm start` usa `proxy.conf.json` para reenviar `/api` a `http://127.0.0.1:8000`.
- Carga de datos con `rxResource` y peticiones de escritura con `firstValueFrom`; formularios reactivos tipados.

---

## 4. Modelo de dominio

### `Task`

| Campo | Tipo PHP | Columna | Descripción |
|---|---|---|---|
| `id` | `TaskId` (UUID v7) | `task_id` (`char(36)`) PK | Lo genera `TaskRepository::nextIdentity()` antes de persistir, no la BD |
| `title` | `string` validado con `TaskTitle` | `varchar(255)` | Obligatorio, 1–255 caracteres tras recortar espacios |
| `description` | `?string` | `text` nullable | Opcional; una cadena en blanco se guarda como `null` |
| `status` | `TaskStatus` | `varchar` (`enumType`) | `pending` \| `in_progress` \| `done` |
| `priority` | `TaskPriority` | `varchar` (`enumType`) | `low` \| `medium` \| `high` |
| `dueDate` | `?\DateTimeImmutable` | `date` nullable | Fecha límite opcional (sin hora) |
| `createdAt` | `\DateTimeImmutable` | `datetime_immutable` | Se asigna al crear |
| `updatedAt` | `\DateTimeImmutable` | `datetime_immutable` | Se actualiza en cada cambio |

```php
class Task
{
    private function __construct(
        private readonly TaskId $id,
        private string $title,
        private ?string $description,
        private TaskStatus $status,
        private TaskPriority $priority,
        private ?\DateTimeImmutable $dueDate,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        TaskId $id,
        string $title,
        ?string $description,
        TaskPriority $priority,
        ?\DateTimeImmutable $dueDate,
        \DateTimeImmutable $now,
    ): self {
        return new self($id, TaskTitle::assert($title), $description, TaskStatus::Pending, $priority, $dueDate, $now, $now);
    }

    public function update(string $title, ?string $description, TaskPriority $priority, ?\DateTimeImmutable $dueDate, \DateTimeImmutable $now): void;

    public function changeStatus(TaskStatus $status, \DateTimeImmutable $now): void;
}
```

### Enums

```php
enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Done = 'done';
}

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
```

### Reglas de negocio

| Regla | Dónde se aplica | Excepción |
|---|---|---|
| Una tarea nueva se crea en `pending` | `Task::create()` | — |
| `title` obligatorio, 1–255 caracteres, también al editar | `TaskTitle` | `InvalidTaskTitle` |
| Una tarea `done` no puede volver a `pending` sin pasar por `in_progress` | `Task::changeStatus()` | `InvalidTaskTransition` |
| Cambiar al mismo estado no hace nada (no toca `updatedAt`) | `Task::changeStatus()` | — |
| `dueDate` con formato `YYYY-MM-DD` y fecha real; puede estar en el pasado | `TaskDueDate` | `InvalidTaskDueDate` |

Transiciones de estado:

| Desde \ Hacia | `pending` | `in_progress` | `done` |
|---|---|---|---|
| `pending` | — | ✅ | ✅ |
| `in_progress` | ✅ | — | ✅ |
| `done` | ❌ | ✅ | — |

---

## 5. API REST

Prefijo `/api`, JSON en peticiones y respuestas. Fechas en ISO 8601 (`dueDate` como `YYYY-MM-DD`).

| Método | Ruta | Descripción | Respuesta OK | Errores |
|---|---|---|---|---|
| GET | `/api/tasks` | Listar con filtros `status`, `priority` y paginación `page` (por defecto 1), `limit` (por defecto 20, máximo 100). Orden: `createdAt` descendente | `200` + página de tareas | `422` filtros inválidos |
| GET | `/api/tasks/{id}` | Detalle | `200` + tarea | `404` |
| POST | `/api/tasks` | Crear (`title`, `description`, `priority` (por defecto `medium`), `dueDate`) | `201` + tarea | `400` / `422` |
| PUT | `/api/tasks/{id}` | Actualizar `title`, `description`, `priority` (obligatoria), `dueDate` (no el estado) | `200` + tarea | `400` / `404` / `422` |
| PATCH | `/api/tasks/{id}/status` | Cambiar estado (`{ "status": "in_progress" }`) | `200` + tarea | `400` / `404` / `422` |
| DELETE | `/api/tasks/{id}` | Eliminar | `204` | `404` |

### Representación de una tarea

```json
{
  "id": "0193a1b2-7c3d-7e4f-8a9b-0c1d2e3f4a5b",
  "title": "Preparar la demo",
  "description": null,
  "status": "pending",
  "priority": "medium",
  "dueDate": "2026-12-01",
  "createdAt": "2026-11-24T12:00:00+01:00",
  "updatedAt": "2026-11-24T12:00:00+01:00"
}
```

### Listado paginado

```json
{
  "items": [ /* tareas */ ],
  "page": 1,
  "limit": 20,
  "total": 42
}
```

### Errores

Un único `ExceptionListener` traduce las excepciones a este formato:

```json
{ "error": { "code": "task_not_found", "message": "La tarea no existe", "details": [] } }
```

| Caso | Código HTTP | `code` |
|---|---|---|
| JSON mal formado | 400 | `invalid_json` |
| Tarea inexistente | 404 | `task_not_found` |
| Ruta inexistente o `{id}` que no es un UUID | 404 | `not_found` |
| Método no permitido | 405 | `method_not_allowed` |
| Validación de entrada (`details` con `field` y `message`) | 422 | `validation_failed` |
| Título inválido | 422 | `invalid_task_title` |
| Fecha límite inválida | 422 | `invalid_task_due_date` |
| Transición de estado no permitida | 422 | `invalid_task_transition` |
| Error no previsto (el mensaje solo se muestra en `debug`) | 500 | `internal_error` |

---

## 6. Estrategia de tests

| Suite | Qué prueba | Ejemplos |
|---|---|---|
| `Unit` | Entidad, value objects y handlers con dobles del repositorio | `test_new_task_is_pending`, `test_done_task_cannot_go_back_to_pending`, `test_it_does_not_allow_empty_title` |
| `Integration` | `DoctrineTaskRepository` contra SQLite | Guardar, buscar, filtrar y paginar |
| `Functional` | Cada endpoint con caso feliz y errores 400/404/422 | `POST /api/tasks` sin título → 422 |

Las entidades de los tests se construyen con `TaskMother` (`pending()`, `inProgress()`, `done()`).

---

## 7. Decisiones de diseño

- **Dominio sin framework**: `Task` no conoce Doctrine; el mapeo va en XML en Infraestructura. Permite testear las reglas de negocio sin base de datos.
- **UUID generado antes de persistir**: el handler pide el `id` a `TaskRepository::nextIdentity()` (UUID v7 de `symfony/uid` en la implementación Doctrine), así el dominio no depende de Symfony y el `id` existe sin esperar a la BD.
- **Un controlador por endpoint**: controladores pequeños y fáciles de leer en pantalla durante el curso.
- **`PUT` no cambia el estado; `PATCH /status` sí**: el cambio de estado tiene reglas propias (transiciones), así que tiene su propio caso de uso y endpoint.
- **422 para reglas de negocio**: distingue "petición mal formada" (400) de "petición bien formada pero no válida" (422).
- **SQLite en desarrollo y en tests**: se arranca sin Docker ni servidor de base de datos. Cada entorno usa su propio fichero en `var/` y los tests recrean el esquema antes de cada caso. Pasar a PostgreSQL solo exige cambiar `DATABASE_URL` y generar una migración nueva.
- **Borrado físico**: sin historial ni papelera en esta versión.
- **Código en inglés, documentación en español**: nombres de clases y métodos en inglés (`Task`, `changeStatus`) como en `AGENTS.md`; textos de la interfaz y la documentación en español.

---

## 8. Fuera de alcance

- Autenticación y tareas por usuario.
- Plantillas de tareas y tareas repetitivas (presentes en el análisis original de otro curso; posibles extensiones futuras).
- Búsqueda por texto, etiquetas o categorías.
- Borrado lógico (`deletedAt`).

---

## 9. Preguntas resueltas en la versión 0.1

| Pregunta | Decisión |
|---|---|
| Paginación | `page=1` y `limit=20` por defecto, `limit` máximo `100` (más da `422`) |
| Orden del listado | `createdAt` descendente y, a igualdad, `id` descendente |
| Transición `pending → done` directa | Se permite; solo se prohíbe `done → pending` |
| Cambiar al mismo estado | Se ignora: `200` con la tarea sin cambios y sin tocar `updatedAt` |
| `dueDate` en el pasado | Se permite; el frontend marca la tarea como vencida si no está `done` |
