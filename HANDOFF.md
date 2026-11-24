# HANDOFF

Punto de entrada para continuar el trabajo en un chat nuevo. Se mantiene con la skill `bitacora`.

**Para retomar**: abrir un chat nuevo en Cursor y escribir `/bitacora retoma el proyecto`. Si la skill no aparece, pedir al agente que lea este fichero.

## Contexto en 30 segundos

- **Qué es**: aplicación de lista de tareas (to-do list) para un curso sobre agentes y skills en Cursor.
- **Stack**: Symfony 7 + Doctrine (PHP 8.3) en `backend/`, Angular 18+ en `frontend/`, PHPUnit.
- **Definición del proyecto**: `AGENTS.md` (stack, dominio `Task`, API REST, convenciones, lista de tareas).
- **Flujo de trabajo** (pizarra en `docs/img/pizarra-flujo.png`): documento funcional → issues de GitHub → análisis con criterios de aceptación → implementación → PR.
- **Remoto**: https://github.com/jsanchezc31/demo_agentes (`origin/main`).

## Estado actual

- **Último paso**: 8. Documentos de referencia y cierre de sesión → `docs/08-documentos-referencia-y-cierre.md`
- **Hecho**:
  - `AGENTS.md` y reglas por capa en `.cursor/rules/`
  - Skills `commit-message` y `bitacora` en `.cursor/skills/`
  - Esqueleto de carpetas de `backend/` y `frontend/` (solo `.gitkeep`, sin código)
  - Repositorio en GitHub con todo subido
- **Siguiente paso**: crear el proyecto Symfony 7 en `backend/` (primera tarea pendiente de `AGENTS.md`). `composer create-project` exige carpeta vacía: generar en `backend-tmp/` y mover; ver `docs/05-estructura-de-carpetas.md`.

## Bloqueos

- PHP 8.3, Composer y Angular CLI no están instalados (solo Node 24). Angular puede generarse con `npx @angular/cli`.
- Sin CLI `gh` (`winget install GitHub.cli` + `gh auth login`) ni MCP de GitHub, el agente no puede crear issues ni PR. Sí puede hacer push.

## Decisiones pendientes

- Documentos de otros proyectos en `docs/` (`ARQUITECTURA-AGENTES-V2.md`, `skills-orquestacion-v2.md`, `analisis-diseño.md`): adaptarlos a este proyecto o moverlos a `docs/referencias/`. **No describen este proyecto.**
- Redactar la documentación funcional en `docs/funcional/` para generar los issues de GitHub.

## Acuerdos de trabajo

- Responder en español.
- No hacer commit ni push sin que el usuario lo pida; los mensajes con la skill `commit-message`.
- Documentar cada paso en `docs/` con la skill `bitacora` y actualizar este fichero.
- No generar archivos `*.spec.ts`.

## Ficheros clave

| Fichero | Para qué |
|---------|----------|
| `AGENTS.md` | Definición del proyecto (se carga siempre) |
| `HANDOFF.md` | Estado y siguiente paso (este fichero) |
| `docs/README.md` | Índice de la bitácora |
| `.cursor/rules/*.mdc` | Convenciones por capa |
| `.cursor/skills/*/SKILL.md` | Skills `commit-message` y `bitacora` |
