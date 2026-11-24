import { ChangeDetectionStrategy, Component, input, output } from '@angular/core';
import { TASK_STATUSES, TaskStatus, canChangeStatus } from '../../../../core/models/task.model';
import { TaskStatusLabelPipe } from '../../../../shared/pipes/task-status-label.pipe';

@Component({
  selector: 'app-task-status-select',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [TaskStatusLabelPipe],
  template: `
    <select
      #select
      [class]="'status status--' + status()"
      [disabled]="disabled()"
      [attr.aria-label]="label()"
      (change)="onChange(select)"
    >
      @for (option of statuses; track option) {
        <option
          [value]="option"
          [selected]="option === status()"
          [disabled]="!canChangeStatus(status(), option)"
        >
          {{ option | taskStatusLabel }}
        </option>
      }
    </select>
  `,
})
export class TaskStatusSelect {
  readonly status = input.required<TaskStatus>();
  readonly label = input('Estado');
  readonly disabled = input(false);
  readonly statusChange = output<TaskStatus>();

  protected readonly statuses = TASK_STATUSES;
  protected readonly canChangeStatus = canChangeStatus;

  protected onChange(select: HTMLSelectElement): void {
    const next = select.value as TaskStatus;

    if (next !== this.status()) {
      this.statusChange.emit(next);
    }
  }
}
