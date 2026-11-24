# Paso 4: Skill para los mensajes de commit

## Objetivo

Que el agente redacte siempre los mensajes de commit con el mismo criterio:

1. Ser específico con lo que cambió.
2. Primera línea `tipo: descripción corta`; cuerpo opcional con el detalle.
3. Escueto, pero nunca vago.

## Adaptación a Cursor

| Otras herramientas | Cursor |
|--------------------|--------|
| `.github/skills/<skill>/SKILL.md` | `.cursor/skills/<skill>/SKILL.md` (skill de proyecto) |
| `~/.claude/skills/` (personal) | `~/.cursor/skills/` (personal, todos los proyectos) |

Como la queremos **solo para este proyecto**, va en `.cursor/skills/commit-message/SKILL.md` y se versiona con el repositorio.

## Qué hicimos

1. Creamos la skill `commit-message` con:
   - Las tres características, tal como las definimos.
   - Flujo: `git status` → `git diff --staged` → redactar → mostrar → commitear solo si se confirma.
   - Formato, tipos (`feat`, `fix`, `docs`, `chore`...) y ejemplos buenos y malos.
   - Cómo ejecutar el commit: `-m` para una línea; `git commit -F` con un fichero UTF-8 si hay cuerpo.
2. Unificamos el formato con la regla del flujo (`flujo-github.mdc`): la referencia a la tarea no va en el resumen sino en el pie, `Refs #<n-issue>`.
3. Añadimos la tabla de skills a `AGENTS.md`.

## Anatomía de una skill

```markdown
---
name: commit-message          # minúsculas y guiones, máx. 64 caracteres
description: Qué hace y CUÁNDO usarla (en tercera persona)
---

# Instrucciones, formato y ejemplos
```

- La `description` es lo que el agente lee para decidir si la carga: debe incluir el **qué** y el **cuándo** ("cuando se vaya a hacer un commit").
- `disable-model-invocation: true` haría que solo se use al invocarla por nombre (`/commit-message`). No la pusimos para que se aplique sola al commitear.

## Regla frente a skill

| | Regla (`.mdc`) | Skill (`SKILL.md`) |
|-|----------------|--------------------|
| Para qué | Convenciones que condicionan cómo se escribe código | Procedimientos para una tarea concreta |
| Cuándo se carga | Siempre, por `globs` o por descripción | Cuando la tarea encaja con su descripción o se invoca |
| Ejemplo | `backend-php.mdc` | `commit-message` |

## Lo aprendido

- Una skill empaqueta un **procedimiento repetible** con su formato y ejemplos.
- Los ejemplos ❌ / ✅ son lo que más mejora el resultado: "docs: actualizar instrucciones" frente a "docs: cambiar idioma de código de inglés a castellano".
- Hay que revisar que reglas y skills no se contradigan; aquí el formato de commit estaba definido en dos sitios distintos.
- Al revisar la skill vimos que varios `-m` no sirven para cuerpos con viñetas (cada `-m` es un párrafo) y que en PowerShell los acentos pueden corromperse; por eso los mensajes con cuerpo se pasan con `git commit -F <fichero UTF-8>`.

## Cómo comprobar que la skill funciona

- **Que Cursor la detecta**: en el chat, escribir `/` y debe aparecer `commit-message`. También se ve en *Cursor Settings → Rules, Skills, Subagents*.
- **Que se aplica**: preparar cambios (`git add ...`) y pedir "haz un commit" o "propón un mensaje de commit"; el agente debe seguir el flujo de la skill y mostrar el mensaje antes de commitear.
- **Previsualizar el `SKILL.md`**: `Ctrl+Shift+V` (vista previa en la pestaña) o `Ctrl+K V` (vista previa al lado).
