import { ChangeDetectionStrategy, Component, computed, inject, input, signal } from '@angular/core';
import { rxResource } from '@angular/core/rxjs-interop';
import { Router, RouterLink } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import { ApiRequestError, errorMessage } from '../../../../core/models/api-error.model';
import { TaskStatus, UpdateTaskPayload } from '../../../../core/models/task.model';
import { ErrorMessage } from '../../../../shared/components/error-message/error-message';
import { TaskForm } from '../../components/task-form/task-form';
import { TaskStatusSelect } from '../../components/task-status-select/task-status-select';
import { TaskService } from '../../services/task.service';

@Component({
  selector: 'app-task-edit-page',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, ErrorMessage, TaskForm, TaskStatusSelect],
  templateUrl: './task-edit-page.html',
})
export class TaskEditPage {
  private readonly taskService = inject(TaskService);
  private readonly router = inject(Router);

  /** Parámetro `:id` de la ruta. */
  readonly id = input.required<string>();

  protected readonly task = rxResource({
    params: () => this.id(),
    stream: ({ params }) => this.taskService.get(params),
  });

  protected readonly saving = signal(false);
  protected readonly error = signal<unknown>(null);
  protected readonly apiError = computed(() => {
    const error = this.error();
    return error instanceof ApiRequestError ? error : null;
  });
  protected readonly message = computed(() => (this.error() ? errorMessage(this.error()) : null));
  protected readonly notFound = computed(() => {
    const error = this.task.error();
    return error instanceof ApiRequestError && error.status === 404;
  });
  protected readonly loadError = computed(() => {
    const error = this.task.error();
    return error && !this.notFound() ? errorMessage(error) : null;
  });

  protected async update(payload: UpdateTaskPayload): Promise<void> {
    await this.run(async () => {
      await firstValueFrom(this.taskService.update(this.id(), payload));
      await this.router.navigateByUrl('/tasks');
    });
  }

  protected async changeStatus(status: TaskStatus): Promise<void> {
    await this.run(async () => {
      const task = await firstValueFrom(this.taskService.changeStatus(this.id(), status));
      this.task.set(task);
    });
  }

  private async run(action: () => Promise<void>): Promise<void> {
    this.saving.set(true);
    this.error.set(null);

    try {
      await action();
    } catch (error) {
      this.error.set(error);
    } finally {
      this.saving.set(false);
    }
  }
}
