import { DatePipe } from '@angular/common';
import { ChangeDetectionStrategy, Component, computed, input, output } from '@angular/core';
import { RouterLink } from '@angular/router';
import { Task, TaskStatus } from '../../../../core/models/task.model';
import { TaskPriorityLabelPipe } from '../../../../shared/pipes/task-priority-label.pipe';
import { TaskStatusSelect } from '../task-status-select/task-status-select';

@Component({
  selector: 'app-task-item',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [DatePipe, RouterLink, TaskPriorityLabelPipe, TaskStatusSelect],
  templateUrl: './task-item.html',
  styleUrl: './task-item.scss',
})
export class TaskItem {
  readonly task = input.required<Task>();
  readonly busy = input(false);
  readonly statusChange = output<TaskStatus>();
  readonly remove = output<void>();

  protected readonly overdue = computed(() => {
    const { dueDate, status } = this.task();
    return dueDate !== null && status !== 'done' && dueDate < today();
  });
}

function today(): string {
  const now = new Date();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');

  return `${now.getFullYear()}-${month}-${day}`;
}
