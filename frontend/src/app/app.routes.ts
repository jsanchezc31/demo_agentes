import { Routes } from '@angular/router';

export const routes: Routes = [
  { path: '', pathMatch: 'full', redirectTo: 'tasks' },
  {
    path: 'tasks',
    title: 'Tareas',
    loadComponent: () =>
      import('./features/tasks/pages/task-list-page/task-list-page').then((m) => m.TaskListPage),
  },
  {
    path: 'tasks/new',
    title: 'Nueva tarea',
    loadComponent: () =>
      import('./features/tasks/pages/task-create-page/task-create-page').then(
        (m) => m.TaskCreatePage,
      ),
  },
  {
    path: 'tasks/:id/edit',
    title: 'Editar tarea',
    loadComponent: () =>
      import('./features/tasks/pages/task-edit-page/task-edit-page').then((m) => m.TaskEditPage),
  },
  { path: '**', redirectTo: 'tasks' },
];
