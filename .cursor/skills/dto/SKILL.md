---
name: dto
description: Crea o actualiza los DTOs de la API de tareas a partir de docs/analisis-diseño.md - DTOs de entrada validados en UI/Http/Request, commands y queries en Application, DTOs de respuesta (TaskResponse, página) y los modelos TypeScript equivalentes en frontend/src/app/core/models. Usar cuando se pida crear los DTOs, los contratos de la API o los modelos del frontend, o sincronizarlos tras cambiar el análisis.
---

# DTOs de la API

Los DTOs son el contrato entre la API y sus clientes. Nunca exponen la entidad `Task` y no contienen lógica de negocio: las reglas viven en el dominio.

## Fuentes

Leer antes de generar nada:

- [`docs/analisis-diseño.md`](../../../docs/analisis-diseño.md): sección 4 (campos, tipos y reglas) y sección 5 (qué entra y qué sale en cada endpoint, formato de página y de error).
- `.cursor/rules/backend-php.mdc` y `.cursor/rules/api-rest.mdc` (backend), `.cursor/rules/frontend-angular.mdc` (frontend).
- Los DTOs y modelos que ya existan, para no pisar cambios manuales.

Si el análisis no existe o le faltan las secciones 4 y 5, detenerse y pedirlo. Si el análisis y `AGENTS.md` se contradicen, preguntar antes de generar.

## Qué se genera

| Pieza | Ubicación | Uso |
|---|---|---|
| `CreateTaskRequest` | `backend/src/UI/Http/Request/` | Cuerpo de `POST /api/tasks` (`#[MapRequestPayload]`) |
| `UpdateTaskRequest` | `backend/src/UI/Http/Request/` | Cuerpo de `PUT /api/tasks/{id}` |
| `ChangeTaskStatusRequest` | `backend/src/UI/Http/Request/` | Cuerpo de `PATCH /api/tasks/{id}/status` |
| `ListTasksRequest` | `backend/src/UI/Http/Request/` | Query de `GET /api/tasks` (`#[MapQueryString]`) |
| Commands y queries | `backend/src/Application/Task/<CasoDeUso>/` | Entrada de cada handler (`CreateTaskCommand`, `ListTasksQuery`...) |
| `TaskResponse` | `backend/src/Application/Task/` | Salida de los handlers y cuerpo de las respuestas |
| `TaskPageResponse` | `backend/src/Application/Task/` | Respuesta paginada del listado |
| Modelos TypeScript | `frontend/src/app/core/models/` | Tipos de `TaskService` y de los componentes |

Si se pide un solo endpoint o una sola capa, generar solo esa parte.

## Reglas

### Comunes

- Nombres en inglés, como el resto del código (`CreateTaskRequest`, no `CrearTareaDto`).
- Ni el `id` ni `createdAt`/`updatedAt` van en los DTOs de entrada: el `id` llega por la ruta y las fechas las pone el dominio.
- El `status` no va en `CreateTaskRequest` ni en `UpdateTaskRequest`: una tarea nueva nace `pending` y el estado solo cambia por `PATCH /status`.
- Los DTOs solo contienen tipos primitivos, enums o arrays de DTOs; nunca entidades.

### Request (UI)

- `final class` con propiedades públicas `readonly` en el constructor, `declare(strict_types=1);`.
- Validación con atributos de Symfony Validator, derivada de la sección 4 del análisis:
  - Campo obligatorio de texto: `#[Assert\NotBlank]` + `#[Assert\Length(max: N)]` con el límite del análisis (`title`: 255).
  - Campo opcional: tipo nullable y valor por defecto `null`; solo `Length` si tiene límite.
  - Enums: `string` + `#[Assert\Choice(callback: [TaskPriority::class, 'values'])]`. Si el enum no tiene `values()`, añadirlo al enum (`array_column(self::cases(), 'value')`); no copiar la lista de valores a mano.
  - Fechas sin hora (`dueDate`): `?string` + `#[Assert\Date]`.
  - Paginación: `#[Assert\Positive]` en `page` y `#[Assert\Range(min: 1, max: 100)]` en `limit`, con los valores por defecto del análisis.
- Los errores de validación los traduce el `ExceptionListener` a `422 validation_failed`: los DTOs no lanzan excepciones propias.

```php
final class CreateTaskRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $title,
        #[Assert\Choice(callback: [TaskPriority::class, 'values'])]
        public readonly string $priority,
        public readonly ?string $description = null,
        #[Assert\Date]
        public readonly ?string $dueDate = null,
    ) {}
}
```

### Commands y queries (Application)

- `final readonly class` inmutable, sin atributos de Symfony ni de validación: la capa Application no depende del framework.
- Tipos primitivos; la conversión a value objects y enums (`TaskId`, `TaskTitle`, `TaskPriority::from()`) la hace el handler.
- Un command o query por caso de uso, en la carpeta del caso de uso (`Application/Task/Create/CreateTaskCommand.php`).

### Respuestas (Application)

- `TaskResponse`: `final readonly class` con todos los campos de la representación de la sección 5, en el mismo orden y con los mismos nombres JSON.
- Construcción con `public static function fromTask(Task $task): self`. Enums como su `value`; `createdAt`/`updatedAt` en ISO 8601 (`DATE_ATOM`); `dueDate` como `Y-m-d` o `null`.
- `TaskPageResponse`: `items` (`list<TaskResponse>`), `page`, `limit`, `total`.
- Propiedades públicas para que `JsonResponse` las serialice sin configuración extra.

### Modelos TypeScript (frontend)

- Un fichero por concepto en `core/models/` (`task.model.ts`, `page.model.ts`, `api-error.model.ts`).
- Enums como uniones de literales, con los mismos valores que la API: `export type TaskStatus = 'pending' | 'in_progress' | 'done';`.
- `Task` como `interface` con los campos de `TaskResponse`; fechas como `string` (ISO) y opcionales como `string | null`.
- Payloads de entrada separados (`CreateTaskPayload`, `UpdateTaskPayload`, `ChangeTaskStatusPayload`) y `TaskFilters` para el listado.
- `Page<T>` genérico y `ApiError` con la forma `{ error: { code, message, details } }`.
- Prohibido `any`. No generar `*.spec.ts`.

## Flujo

1. Leer las fuentes y listar los DTOs que hacen falta según la sección 5.
2. Comparar con lo que ya existe y mostrar al usuario qué se crea, qué se modifica y qué se borra.
3. Generar los ficheros.
4. Comprobar que backend y frontend usan los mismos nombres de campo y los mismos valores de enum.
5. Si las herramientas están disponibles, ejecutar `vendor/bin/phpstan analyse` y `vendor/bin/php-cs-fixer fix` en `backend/` y `npm run lint` en `frontend/`.
6. Informar de los ficheros creados o modificados y recordar los tests funcionales pendientes: cada endpoint necesita casos 400/404/422 (`.cursor/rules/tests-backend.mdc`).
