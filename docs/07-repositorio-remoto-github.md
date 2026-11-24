# Paso 7: Commit con la skill y repositorio remoto en GitHub

## Objetivo

- Commitear la skill `bitacora` usando la skill `commit-message`.
- Publicar el repositorio en GitHub: https://github.com/jsanchezc31/demo_agentes

## Qué hicimos

1. Commit `chore(skills): añadir skill bitacora para documentar y retomar pasos`, redactado con la skill `commit-message`.
2. El primer intento falló: se escribió el fichero del mensaje y se lanzó el commit **en paralelo**; la escritura falló y Git abortó por no tener mensaje. Se repitió en secuencia y se añadió esa precaución a la skill (commit `fix(skills): ...`).
3. Sin CLI `gh` ni MCP de GitHub, el repositorio se creó a mano en github.com (público, vacío).
4. Comprobamos que el remoto estaba vacío y publicamos:

```bash
git ls-remote https://github.com/jsanchezc31/demo_agentes.git   # vacío: nada que pisar
git remote add origin https://github.com/jsanchezc31/demo_agentes.git
git push -u origin main
```

## Lo aprendido

- Las skills se mejoran con el uso: cada fallo real se convierte en una instrucción nueva de la skill.
- Pasos dependientes (escribir el mensaje → commitear) deben ejecutarse en secuencia.
- Antes del primer push, `git ls-remote` confirma que el remoto está vacío y evita sobrescribir contenido.
- En PowerShell, `git push` muestra su progreso en rojo como si fuera un error: Git escribe en stderr aunque todo vaya bien. Hay que leer el mensaje (`* [new branch] main -> main`), no el color.
- Sin `gh` ni MCP de GitHub el agente puede hacer push (Git usa las credenciales de Windows), pero no crear repositorios, issues ni PR.
