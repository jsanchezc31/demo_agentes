# Paso 2: Llevar la definición a Cursor

## Objetivo

El candado de la pizarra (la definición del proyecto) estaba en `CLAUDE.md`. A partir de aquí trabajamos con **Cursor**, así que lo trasladamos a sus mecanismos equivalentes.

## Equivalencias Claude Code → Cursor

| Claude Code | Cursor | Cuándo se carga |
|-------------|--------|-----------------|
| `CLAUDE.md` en la raíz | `AGENTS.md` en la raíz | Siempre, en cada conversación |
| Secciones de convenciones dentro de `CLAUDE.md` | Reglas `.cursor/rules/*.mdc` | Según `globs`, `alwaysApply` o `description` |

## Qué hicimos

1. Creamos `AGENTS.md` con la definición del proyecto (el candado): qué es, stack, estructura, dominio, API, comandos, lista de tareas y límites.
2. Separamos las convenciones por capa en reglas `.mdc`:

| Regla | Se activa |
|-------|-----------|
| `backend-php.mdc` | `backend/**/*.php` |
| `doctrine.mdc` | `Infrastructure/`, `migrations/`, configuración de Doctrine |
| `api-rest.mdc` | `backend/src/UI/Http/**` |
| `tests-backend.mdc` | `backend/tests/**` |
| `frontend-angular.mdc` | `frontend/src/**` |
| `flujo-github.mdc` | Por descripción: cuando la tarea viene de un issue de GitHub o termina en PR |

3. Eliminamos `CLAUDE.md` para no tener dos fuentes de verdad.

## Formato de una regla `.mdc`

```markdown
---
description: Qué hace la regla (el agente la usa para decidir si cargarla)
globs: backend/**/*.php
alwaysApply: false
---

# Título

Contenido breve, accionable y con ejemplos ✅ / ❌.
```

Tipos de activación:

- `alwaysApply: true`: siempre presente.
- `globs`: se carga cuando se trabaja con archivos que coinciden.
- Solo `description`: el agente decide cargarla si la tarea encaja con la descripción.

## Lo aprendido

- `AGENTS.md` es el equivalente directo de `CLAUDE.md` en Cursor.
- Las reglas `.mdc` permiten **contexto bajo demanda**: cada capa aporta sus normas solo cuando se toca, sin llenar el contexto de todo lo demás.
- Reglas cortas (menos de 50 líneas), una por tema y con ejemplos concretos funcionan mejor.
- Una regla sin `globs` y con buena `description` sirve para procesos (como el flujo issue de GitHub → PR) que no dependen de un tipo de archivo.
