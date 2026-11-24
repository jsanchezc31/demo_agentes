---
name: bitacora
description: Mantiene la bitácora del curso en docs/ (un documento por paso con objetivo, qué hicimos y lo aprendido) y el fichero HANDOFF.md con el estado actual, y permite retomar el trabajo en un chat nuevo. Usar cuando el usuario pida documentar un paso, actualizar la bitácora, hacer un handoff o cerrar la sesión, o pregunte dónde nos quedamos, qué toca ahora o pida retomar el proyecto.
---

# Bitácora del curso

Dos piezas:

- `docs/`: un documento por paso; el índice `docs/README.md` contiene la tabla de pasos.
- `HANDOFF.md` (raíz): estado actual, siguiente paso, bloqueos y decisiones pendientes. Es lo que permite a un chat nuevo continuar sin contexto previo.

Determinar el modo:

- **¿Pide documentar o registrar un paso?** → Modo registrar.
- **¿Pide retomar, dónde nos quedamos o qué toca?** → Modo retomar.

## Modo registrar

1. Ver qué se ha hecho desde el último paso documentado:
   ```bash
   git log --oneline -10
   git status --short
   ```
   Contrastar con la conversación y con la tabla de `docs/README.md`.
2. Calcular el número: último `NN` de la tabla + 1, con dos dígitos.
3. Crear `docs/NN-titulo-en-kebab-case.md` con la plantilla de abajo.
4. Añadir la fila a la tabla de pasos de `docs/README.md`.
5. Actualizar `HANDOFF.md`: secciones **Estado actual**, **Bloqueos** y **Decisiones pendientes**. Mantener el resto (contexto, acuerdos, ficheros clave) salvo que el paso los cambie.
6. Si el paso cambia decisiones del proyecto, actualizar también `AGENTS.md` (y sus casillas de la lista de tareas).
7. Imágenes en `docs/img/` y enlazadas con ruta relativa (`img/nombre.png`).

Si el paso corrige uno anterior (por ejemplo, cambiar Jira por GitHub), actualizar los documentos afectados en lugar de dejar información contradictoria.

### Plantilla de paso

```markdown
# Paso N: Título

## Objetivo

Qué queríamos conseguir y por qué.

## Qué hicimos

1. Acciones concretas, con los comandos o ficheros relevantes.

## Lo aprendido

- Conclusiones reutilizables, no repetir el "qué hicimos".
```

Secciones opcionales cuando aporten: `Pendiente`, tablas comparativas, ejemplos de código.

### Secciones de `HANDOFF.md` que se actualizan

```markdown
## Estado actual

- **Último paso**: N. Título → `docs/NN-titulo.md`
- **Hecho**: lista breve de lo que ya existe
- **Siguiente paso**: acción concreta (primera casilla sin marcar de la lista de tareas de AGENTS.md, salvo que se haya acordado otra cosa)

## Bloqueos

- Lo que impide avanzar (herramientas sin instalar, accesos) o "Ninguno"

## Decisiones pendientes

- Preguntas abiertas que debe resolver el usuario
```

No incluir el hash del último commit: quedaría desfasado en cuanto se commitee el propio handoff; se consulta con `git log`.

## Modo retomar

1. Leer en este orden:
   - `HANDOFF.md`: estado, siguiente paso, bloqueos y decisiones pendientes.
   - El último documento de paso `docs/NN-*.md` (enlazado desde `HANDOFF.md`).
   - `AGENTS.md`: lista de tareas inicial (casillas marcadas y pendientes).
2. Contrastar con la realidad del repositorio:
   ```bash
   git log --oneline -10
   git status --short
   ```
   Si hay commits o cambios posteriores al último paso documentado, señalarlo.
3. Responder al usuario con:
   - Dónde nos quedamos (último paso y último commit).
   - Siguiente paso propuesto, bloqueos y decisiones pendientes.
   - Cambios sin documentar o sin commitear, si los hay.
4. No empezar el siguiente paso sin que el usuario lo confirme.
