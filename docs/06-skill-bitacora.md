# Paso 6: Skill para la bitácora y retomar en un chat nuevo

## Objetivo

- Documentar cada paso siempre igual, sin depender de que el agente lo recuerde.
- Poder abrir un chat nuevo, que no tiene memoria del anterior, y continuar donde lo dejamos.

## Qué hicimos

1. Creamos la skill `.cursor/skills/bitacora/SKILL.md` con dos modos:
   - **Registrar**: crea `docs/NN-titulo.md` con la plantilla (objetivo, qué hicimos, lo aprendido), añade la fila al índice y actualiza el estado actual.
   - **Retomar**: lee `docs/README.md`, el último paso y la lista de tareas de `AGENTS.md`, lo contrasta con `git log` y `git status`, y resume dónde estamos y qué toca.
2. Añadimos la sección **Estado actual** a `docs/README.md`: último paso, siguiente paso y bloqueos.
3. Sustituimos en `AGENTS.md` las instrucciones sueltas de la bitácora por una referencia a la skill y la añadimos a la tabla de skills.

## Cómo retomar en un chat nuevo

1. Abrir un chat nuevo en Cursor.
2. Escribir `/bitacora` y pedir: "retoma el proyecto" o "¿dónde nos quedamos?".
3. El agente lee la bitácora y el repositorio y propone el siguiente paso; no empieza sin confirmación.

## Lo aprendido

- Cada chat empieza sin memoria: la continuidad tiene que estar **escrita en el repositorio**, no en la conversación.
- `AGENTS.md` dice qué es el proyecto; la bitácora dice **en qué punto está**. Las dos piezas son necesarias para retomar.
- Una skill puede tener varios modos; la `description` debe nombrar los disparadores de cada uno ("documenta", "dónde nos quedamos", "retoma").
- No guardar datos que se desfasan solos (como el hash del último commit) si se pueden consultar con Git.
- Las skills creadas durante un chat no se cargan en ese chat; se prueban en uno nuevo.
