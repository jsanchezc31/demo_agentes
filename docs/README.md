# Bitácora del curso

Registro de los pasos dados y lo aprendido en cada uno. Se mantiene con la skill `bitacora`.

El estado actual, el siguiente paso y los bloqueos están en [`HANDOFF.md`](../HANDOFF.md).

![Flujo de trabajo de la pizarra](img/pizarra-flujo.png)

## Pasos

| # | Paso | Documento |
|---|------|-----------|
| 1 | Definir el proyecto con `CLAUDE.md` (Claude Code) | [01-definicion-proyecto-claude-md.md](01-definicion-proyecto-claude-md.md) |
| 2 | Llevar la definición a Cursor: `AGENTS.md` + reglas `.mdc` | [02-migracion-a-cursor.md](02-migracion-a-cursor.md) |
| 3 | Crear el repositorio Git en local | [03-repositorio-git-local.md](03-repositorio-git-local.md) |
| 4 | Skill para los mensajes de commit | [04-skill-mensajes-commit.md](04-skill-mensajes-commit.md) |
| 5 | Crear la estructura de carpetas | [05-estructura-de-carpetas.md](05-estructura-de-carpetas.md) |
| 6 | Skill para la bitácora y retomar en un chat nuevo | [06-skill-bitacora.md](06-skill-bitacora.md) |
| 7 | Commit con la skill y repositorio remoto en GitHub | [07-repositorio-remoto-github.md](07-repositorio-remoto-github.md) |
| 8 | Documentos de referencia y cierre de sesión (handoff) | [08-documentos-referencia-y-cierre.md](08-documentos-referencia-y-cierre.md) |
| 9 | Adaptar el análisis y revisar las skills nuevas | [09-analisis-y-revision-de-skills.md](09-analisis-y-revision-de-skills.md) |
| 10 | Aplicación 0.1 (backend y frontend) | [10-aplicacion-0-1.md](10-aplicacion-0-1.md) |

## Estructura de `docs/`

- `NN-titulo.md`: un documento por paso, numerado en orden.
- `img/`: imágenes de apoyo (pizarra, capturas, diagramas).
- `funcional/`: documentación funcional del producto que alimenta los issues de GitHub.
- `analisis-diseño.md`: análisis y diseño de este proyecto (capas, modelo `Task`, API, tests y decisiones), actualizado con lo implementado en la versión 0.1.
- `ARQUITECTURA-AGENTES-V2.md`, `skills-orquestacion-v2.md`: **referencia de otros proyectos**, no describen este. La definición de este proyecto está en `AGENTS.md`.
