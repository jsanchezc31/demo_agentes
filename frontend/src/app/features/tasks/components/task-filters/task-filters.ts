import { ChangeDetectionStrategy, Component, input, output } from '@angular/core';
import {
  TASK_PRIORITIES,
  TASK_STATUSES,
  TaskFilters as Filters,
  TaskPriority,
  TaskStatus,
} from '../../../../core/models/task.model';
import { TaskPriorityLabelPipe } from '../../../../shared/pipes/task-priority-label.pipe';
import { TaskStatusLabelPipe } from '../../../../shared/pipes/task-status-label.pipe';

@Component({
  selector: 'app-task-filters',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [TaskStatusLabelPipe, TaskPriorityLabelPipe],
  template: `
    <form class="filters" role="search" (submit)="$event.preventDefault()">
      <label for="filter-status">Estado</label>
      <select id="filter-status" #status (change)="emit(status.value, priority.value)">
        <option value="" [selected]="filters().status === null">Todos</option>
        @for (option of statuses; track option) {
          <option [value]="option" [selected]="option === filters().status">
            {{ option | taskStatusLabel }}
          </option>
        }
      </select>

      <label for="filter-priority">Prioridad</label>
      <select id="filter-priority" #priority (change)="emit(status.value, priority.value)">
        <option value="" [selected]="filters().priority === null">Todas</option>
        @for (option of priorities; track option) {
          <option [value]="option" [selected]="option === filters().priority">
            {{ option | taskPriorityLabel }}
          </option>
        }
      </select>
    </form>
  `,
  styles: `
    .filters {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.5rem 0.75rem;
      margin-bottom: 1.25rem;
    }

    select {
      min-width: 9rem;
    }
  `,
})
export class TaskFilters {
  readonly filters = input.required<Filters>();
  readonly filtersChange = output<Filters>();

  protected readonly statuses = TASK_STATUSES;
  protected readonly priorities = TASK_PRIORITIES;

  protected emit(status: string, priority: string): void {
    this.filtersChange.emit({
      status: status === '' ? null : (status as TaskStatus),
      priority: priority === '' ? null : (priority as TaskPriority),
    });
  }
}
