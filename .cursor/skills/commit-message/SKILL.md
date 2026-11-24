---
name: commit-message
description: Redacta mensajes de commit específicos en castellano con formato "tipo: descripción corta" y cuerpo opcional, analizando los cambios reales del repositorio. Usar cuando se vaya a hacer un commit, el usuario pida un mensaje de commit o quiera revisar los cambios preparados (staged).
---

# Mensajes de commit

## Características

1) Sé específico con lo que cambió: no vale "actualizar fichero", di qué se tocó exactamente.
2) El cuerpo del commit es opcional pero valioso: la primera línea es el resumen (tipo: descripción corta), el cuerpo explica el detalle; si el cambio es trivial, una sola línea basta.
3) Escueto no es lo mismo que vago: "docs: cambiar idioma de código de inglés a castellano" es corto pero dice exactamente qué ocurrió; "actualizar instrucciones" no dice nada.

## Flujo

1. Ver qué entra en el commit:
   ```bash
   git status --short
   git diff --staged --stat
   git diff --staged
   ```
   Si no hay nada preparado, revisar `git status` y `git diff` y preguntar qué ficheros incluir. Los ficheros nuevos sin seguimiento (`??`) no aparecen en `git diff`: leerlos directamente.
2. Leer el diff completo. El mensaje se basa en el diff, no en lo que se recuerda de la conversación.
3. Si el diff mezcla cambios sin relación, proponer separarlo en varios commits.
4. Redactar el mensaje con el formato de abajo y mostrarlo al usuario.
5. Commitear solo si el usuario lo confirma (ver `AGENTS.md`).

## Formato

```
tipo(alcance opcional): descripción corta

Cuerpo opcional: qué cambió y por qué, en líneas de máximo 72 caracteres.
Puede usar viñetas con "-".

Refs #12
```

- **Primera línea**: máximo 72 caracteres, minúscula tras los dos puntos, sin punto final.
- **Verbo en infinitivo**: "añadir", "corregir", "eliminar", "cambiar".
- **Alcance**: la parte tocada (`backend`, `frontend`, `task`, `api`, `rules`, `docs`...). Omitir si no aporta.
- **Cuerpo**: separado por una línea en blanco. Explica el porqué y los detalles que no caben en el resumen.
- **Pie** `Refs #<n-issue>` solo si el trabajo viene de un issue de GitHub. Usar `Closes #<n-issue>` solo si ese commit completa el issue.
- **Cambio incompatible** (rompe la API o el contrato): `!` tras el tipo (`feat(api)!: ...`) y pie `BREAKING CHANGE: qué cambia y cómo migrar`.

## Tipos

| Tipo | Uso |
|------|-----|
| `feat` | Funcionalidad nueva |
| `fix` | Corrección de un error |
| `docs` | Solo documentación (`docs/`, `AGENTS.md`, `README.md`) |
| `refactor` | Cambio de código sin cambiar comportamiento |
| `test` | Añadir o corregir tests |
| `chore` | Configuración, dependencias, reglas y skills del agente, `.gitignore` |
| `style` | Formato, sin cambio de lógica |
| `perf` | Mejora de rendimiento |
| `build` / `ci` | Sistema de build o integración continua |

## Ejemplos

❌ Vagos:

```
update files
docs: actualizar instrucciones
fix: arreglar bug
```

✅ Una línea (cambio trivial):

```
docs: corregir enlace roto a la bitácora en README
```

✅ Con cuerpo:

```
feat(task): impedir que una tarea completada vuelva a pendiente

La transición done → pending lanza InvalidTaskTransition; ahora debe
pasar por in_progress, como define la regla de negocio de AGENTS.md.

Refs #12
```

✅ Varios cambios relacionados:

```
chore(rules): añadir reglas de Cursor por capa del proyecto

- backend-php, doctrine y api-rest para el backend Symfony
- tests-backend con las suites de PHPUnit
- frontend-angular con standalone, signals y OnPush
```

## Ejecutar el commit

- **Una sola línea**: `git commit -m "docs: corregir enlace roto a la bitácora en README"`
- **Con cuerpo**: escribir el mensaje en `.git/COMMIT_MSG_DRAFT` (UTF-8, sin BOM) con la herramienta de escritura de ficheros y commitear con:

  ```bash
  git commit -F .git/COMMIT_MSG_DRAFT
  ```

  No usar varios `-m` para el cuerpo: cada `-m` crea un párrafo (las viñetas quedarían separadas por líneas en blanco) y en PowerShell los acentos pueden corromperse.

Tras el commit, comprobar el resultado con `git log -1 --format=%B`.
