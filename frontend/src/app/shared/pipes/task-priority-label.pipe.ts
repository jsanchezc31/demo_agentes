import { Pipe, PipeTransform } from '@angular/core';
import { TaskPriority } from '../../core/models/task.model';

const LABELS: Record<TaskPriority, string> = {
  low: 'Baja',
  medium: 'Media',
  high: 'Alta',
};

@Pipe({ name: 'taskPriorityLabel' })
export class TaskPriorityLabelPipe implements PipeTransform {
  transform(priority: TaskPriority): string {
    return LABELS[priority];
  }
}
