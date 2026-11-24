# Paso 5: Crear la estructura de carpetas

## Objetivo

`AGENTS.md` describía la estructura del proyecto, pero en disco no existía. Creamos el esqueleto para que la definición (el candado) y el repositorio coincidan desde el principio.

## Qué hicimos

1. Comprobamos las herramientas instaladas:

| Herramienta | Estado |
|-------------|--------|
| Node.js | v24 instalado |
| PHP | No instalado |
| Composer | No instalado |
| Angular CLI (`ng`) | No instalado |

   Sin PHP, Composer ni Angular CLI no se pueden generar aún los proyectos Symfony y Angular, así que creamos solo las carpetas.

2. Creamos el árbol según `AGENTS.md` y las reglas de `.cursor/rules/`:

```
backend/
├── src/
│   ├── Domain/Task/
│   ├── Application/Task/
│   ├── Infrastructure/Persistence/Doctrine/{Mapping,Repository}/
│   └── UI/Http/{Controller,Request,EventListener}/
├── migrations/
├── config/
└── tests/{Unit,Integration,Functional,Mother}/
frontend/src/
├── app/
│   ├── core/{models,interceptors}/
│   ├── features/tasks/{pages,components,services}/
│   └── shared/{components,pipes}/
└── environments/
docs/funcional/
```

3. Añadimos un `.gitkeep` en cada carpeta hoja: Git no versiona carpetas vacías.
4. Actualizamos el árbol de `AGENTS.md` con el detalle de cada carpeta y su propósito.

## Pendiente

Cuando estén instaladas las herramientas:

- **Symfony**: `composer create-project` exige un directorio vacío. Generar en una carpeta temporal y mover el contenido a `backend/`:
  ```bash
  composer create-project symfony/skeleton backend-tmp
  # mover backend-tmp/* a backend/ y borrar backend-tmp
  ```
- **Angular**: generar desde la raíz sin sobrescribir `src/app/` existente:
  ```bash
  npx @angular/cli new frontend --directory frontend --standalone --routing --style=scss --skip-git
  ```
- Borrar los `.gitkeep` de las carpetas que ya tengan ficheros.

## Lo aprendido

- La estructura descrita en `AGENTS.md` y la real deben coincidir: si el agente lee una estructura que no existe, puede inventarse rutas.
- Cada carpeta refleja una decisión de arquitectura (capas del backend, `features/` en el frontend); crearlas antes que el código guía al agente sobre dónde colocar cada cosa.
- Git solo versiona ficheros: los `.gitkeep` son la convención para conservar carpetas vacías.
