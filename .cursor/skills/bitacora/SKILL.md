---
name: bitacora
description: Mantiene la bitácora del curso en docs/ (un documento por paso con objetivo, qué hicimos y lo aprendido) y permite retomar el trabajo en un chat nuevo leyendo el estado actual. Usar cuando el usuario pida documentar un paso, actualizar la bitácora, o pregunte dónde nos quedamos, qué toca ahora o pida retomar el proyecto.
---

# Bitácora del curso

La bitácora vive en `docs/`. El índice es `docs/README.md` y contiene la tabla de pasos y la sección **Estado actual**, que es lo que permite a un chat nuevo continuar sin contexto previo.

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
4. En `docs/README.md`:
   - Añadir la fila a la tabla de pasos.
   - Reescribir **Estado actual** (último paso, siguiente paso, bloqueos).
5. Si el paso cambia decisiones del proyecto, actualizar también `AGENTS.md` (y sus casillas de la lista de tareas).
6. Imágenes en `docs/img/` y enlazadas con ruta relativa (`img/nombre.png`).

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

### Plantilla de Estado actual

```markdown
## Estado actual

- **Último paso**: N. Título
- **Siguiente paso**: acción concreta (primera casilla sin marcar de la lista de tareas de AGENTS.md, salvo que se haya acordado otra cosa)
- **Bloqueos**: lo que impide avanzar (herramientas sin instalar, decisiones pendientes) o "Ninguno"
```

No incluir el hash del último commit: quedaría desfasado en cuanto se commitee la propia bitácora; se consulta con `git log`.

## Modo retomar

1. Leer en este orden:
   - `docs/README.md`: tabla de pasos y **Estado actual**.
   - El último documento de paso `docs/NN-*.md`.
   - `AGENTS.md`: lista de tareas inicial (casillas marcadas y pendientes).
2. Contrastar con la realidad del repositorio:
   ```bash
   git log --oneline -10
   git status --short
   ```
   Si hay commits o cambios posteriores al último paso documentado, señalarlo.
3. Responder al usuario con:
   - Dónde nos quedamos (último paso y último commit).
   - Siguiente paso propuesto y bloqueos.
   - Cambios sin documentar o sin commitear, si los hay.
4. No empezar el siguiente paso sin que el usuario lo confirme.
