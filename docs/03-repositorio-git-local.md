# Paso 3: Crear el repositorio Git en local

## Objetivo

Versionar el proyecto desde el principio, de momento solo en local (sin remoto). Es la base del último tramo de la pizarra: **GIT → PR**.

## Qué hicimos

1. Comprobamos que Git está instalado (`git --version`) y que la carpeta no era ya un repositorio.
2. Creamos `.gitignore` para el stack:
   - Backend Symfony: `vendor/`, `var/`, cachés de PHPUnit y PHP-CS-Fixer.
   - Frontend Angular: `node_modules/`, `dist/`, `.angular/`, `coverage/`.
   - Entorno: `.env.local` y logs (los secretos nunca se versionan).
3. Creamos `.gitattributes` con `* text=auto eol=lf` para que los finales de línea sean LF aunque trabajemos en Windows.
4. Inicializamos el repositorio con la rama principal `main`:

```bash
git init -b main
```

## Qué se versiona

- `AGENTS.md`: definición del proyecto (el candado).
- `.cursor/rules/`: reglas del agente. **Se versionan** para que todo el equipo y el agente compartan las mismas convenciones.
- `docs/`: bitácora del curso.

## Lo aprendido

- El `.gitignore` conviene crearlo **antes** del primer commit, para no subir dependencias ni ficheros generados.
- `.gitattributes` evita diferencias falsas por finales de línea (CRLF en Windows frente a LF en Linux/Mac).
- `AGENTS.md` y `.cursor/rules/` son parte del proyecto: versionarlos hace que el contexto de la IA sea reproducible.
- Según `AGENTS.md`, el agente no hace commit ni push sin que se le pida.
