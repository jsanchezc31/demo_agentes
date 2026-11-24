export type TaskStatus = 'pending' | 'in_progress' | 'done';

export type TaskPriority = 'low' | 'medium' | 'high';

export const TASK_STATUSES: readonly TaskStatus[] = ['pending', 'in_progress', 'done'];

export const TASK_PRIORITIES: readonly TaskPriority[] = ['low', 'medium', 'high'];

export interface Task {
  id: string;
  title: string;
  description: string | null;
  status: TaskStatus;
  priority: TaskPriority;
  /** Fecha sin hora, `YYYY-MM-DD`. */
  dueDate: string | null;
  /** ISO 8601. */
  createdAt: string;
  /** ISO 8601. */
  updatedAt: string;
}

export interface CreateTaskPayload {
  title: string;
  description: string | null;
  priority: TaskPriority;
  dueDate: string | null;
}

export type UpdateTaskPayload = CreateTaskPayload;

export interface ChangeTaskStatusPayload {
  status: TaskStatus;
}

export interface TaskFilters {
  status: TaskStatus | null;
  priority: TaskPriority | null;
}

export interface TaskListQuery extends TaskFilters {
  page: number;
  limit: number;
}

/** Refleja la regla del dominio: una tarea `done` no vuelve a `pending` sin pasar por `in_progress`. */
export function canChangeStatus(from: TaskStatus, to: TaskStatus): boolean {
  return !(from === 'done' && to === 'pending');
}
