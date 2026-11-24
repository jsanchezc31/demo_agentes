# HANDOFF

Punto de entrada para continuar el trabajo en un chat nuevo. Se mantiene con la skill `bitacora`.

**Para retomar**: abrir un chat nuevo en Cursor y escribir `/bitacora retoma el proyecto`. Si la skill no aparece, pedir al agente que lea este fichero.

## Contexto en 30 segundos

- **Qué es**: aplicación de lista de tareas (to-do list) para un curso sobre agentes y skills en Cursor.
- **Stack**: Symfony 7.4 + Doctrine + SQLite (PHP 8.3) en `backend/`, Angular 22 en `frontend/`, PHPUnit.
- **Definición del proyecto**: `AGENTS.md` (stack, dominio `Task`, API REST, convenciones, lista de tareas).
- **Arranque**: `README.md` (backend en `127.0.0.1:8000`, frontend en `localhost:4200` con proxy de `/api`).
- **Flujo de trabajo** (pizarra en `docs/img/pizarra-flujo.png`): documento funcional → issues de GitHub → análisis con criterios de aceptación → implementación → PR.
- **Remoto**: https://github.com/jsanchezc31/demo_agentes (`origin/main`).

## Estado actual

- **Último paso**: 10. Aplicación 0.1 (backend y frontend) → `docs/10-aplicacion-0-1.md`
- **Hecho**:
  - `AGENTS.md`, reglas por capa en `.cursor/rules/` y skills `commit-message`, `bitacora` y `dto` en `.cursor/skills/` (`docx`, `pdf` y `pptx` de Anthropic están en `~/.cursor/skills/`, fuera del repositorio por su licencia)
  - `docs/analisis-diseño.md` adaptado al proyecto y con las preguntas abiertas resueltas
  - Backend completo: dominio, casos de uso, 6 endpoints, errores uniformes, CORS y 81 tests; PHPUnit, PHPStan nivel 8 y PHP-CS-Fixer en verde
  - Frontend completo: listado con filtros y paginación, alta, edición, cambio de estado y borrado; build, ESLint y Prettier en verde
  - Toda la lista de tareas inicial de `AGENTS.md` marcada
  - Trabajo commiteado en la rama `feature/app-0.1` y subido a GitHub
- **Siguiente paso**: abrir desde la web de GitHub el PR de `feature/app-0.1` hacia `main` (si no está abierto ya), revisarlo y mergearlo. Después, redactar la documentación funcional de la siguiente versión en `docs/funcional/`.

## Bloqueos

- Sin CLI `gh` (`winget install GitHub.cli` + `gh auth login`) ni MCP de GitHub, el agente no puede crear issues ni PR: el PR se abre desde la web de GitHub tras el push.

## Decisiones pendientes

- Documentos de otros proyectos en `docs/` (`ARQUITECTURA-AGENTES-V2.md`, `skills-orquestacion-v2.md`): adaptarlos a este proyecto o moverlos a `docs/referencias/`. **No describen este proyecto.**
- Redactar la documentación funcional en `docs/funcional/` para generar los issues de GitHub de las siguientes versiones.
- Detalle menor de la API: un `{id}` que no es UUID devuelve `404 not_found` (lo rechaza la ruta) en lugar de `404 task_not_found`.

## Acuerdos de trabajo

- Responder en español.
- No hacer commit ni push sin que el usuario lo pida; los mensajes con la skill `commit-message`.
- Documentar cada paso en `docs/` con la skill `bitacora` y actualizar este fichero.
- No generar archivos `*.spec.ts`.
- En Windows (PowerShell 5) encadenar comandos con `;`, no con `&&`.

## Ficheros clave

| Fichero | Para qué |
|---------|----------|
| `AGENTS.md` | Definición del proyecto (se carga siempre) |
| `HANDOFF.md` | Estado y siguiente paso (este fichero) |
| `README.md` | Requisitos, arranque, tests y API |
| `docs/README.md` | Índice de la bitácora |
| `docs/analisis-diseño.md` | Análisis y diseño: capas, modelo `Task`, API, tests y decisiones |
| `.cursor/rules/*.mdc` | Convenciones por capa |
| `.cursor/skills/*/SKILL.md` | Skills `commit-message`, `bitacora` y `dto` |
