# Paso 9: Adaptar el análisis y revisar las skills nuevas

## Objetivo

- Retomar el proyecto en un chat nuevo con `/bitacora retoma el proyecto`.
- Adaptar a este proyecto `docs/analisis-diseño.md`, que venía de otro curso.
- Revisar las skills que se habían añadido a `.cursor/skills/` antes de usarlas.

## Qué hicimos

1. **Retomar**: la skill `bitacora` leyó `HANDOFF.md`, el paso 8 y `AGENTS.md`, comparó con `git status` y propuso el siguiente paso sin empezarlo.
2. **Análisis y diseño**: `docs/analisis-diseño.md` describía una lista de tareas en ASP.NET Core y EF Core, con IDs `int`, plantillas y tareas repetitivas. Lo reescribimos a partir de `AGENTS.md`:
   - Capas del backend (Domain, Application, Infrastructure, UI/Http) y organización del frontend.
   - Modelo `Task` con UUID, value objects, enums y tabla de transiciones de estado.
   - API REST, formato de errores, estrategia de tests y decisiones de diseño.
   - Lo que no cuadraba (plantillas, tareas repetitivas) pasó a "Fuera de alcance".
   - Lo que `AGENTS.md` no decidía quedó como preguntas abiertas en la sección 9.
3. **Revisión de skills**:

| Skill | Problema | Decisión |
|-------|----------|----------|
| `dto` | Pensada para C# y otro stack | Reescrita para Symfony (Request DTOs, commands/queries, `TaskResponse`) y modelos TypeScript |
| `docx`, `pdf`, `pptx` | Skills de Anthropic con licencia propietaria; no tienen que ver con la app | Movidas a `~/.cursor/skills/` (globales, fuera del repositorio) |

4. Añadimos `dto` a la tabla de skills de `AGENTS.md`.

## Lo aprendido

- Un documento heredado de otro proyecto confunde más que ayuda si no se adapta: el agente lo lee como verdad.
- Las preguntas abiertas escritas en el análisis sirven para decidir más tarde sin perderlas; en el paso 10 se resolvieron al implementar.
- Antes de usar una skill hay que revisarla igual que el código: stack, rutas y licencia.
- Las skills genéricas, que no dependen del proyecto, van mejor en `~/.cursor/skills/`. En el repositorio solo las que conocen el proyecto.
