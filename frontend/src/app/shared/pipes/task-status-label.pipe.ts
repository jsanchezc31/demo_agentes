import { Pipe, PipeTransform } from '@angular/core';
import { TaskStatus } from '../../core/models/task.model';

const LABELS: Record<TaskStatus, string> = {
  pending: 'Pendiente',
  in_progress: 'En curso',
  done: 'Hecha',
};

@Pipe({ name: 'taskStatusLabel' })
export class TaskStatusLabelPipe implements PipeTransform {
  transform(status: TaskStatus): string {
    return LABELS[status];
  }
}
