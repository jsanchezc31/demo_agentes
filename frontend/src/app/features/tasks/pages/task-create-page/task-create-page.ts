import { ChangeDetectionStrategy, Component, computed, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import { ApiRequestError, errorMessage } from '../../../../core/models/api-error.model';
import { CreateTaskPayload } from '../../../../core/models/task.model';
import { ErrorMessage } from '../../../../shared/components/error-message/error-message';
import { TaskForm } from '../../components/task-form/task-form';
import { TaskService } from '../../services/task.service';

@Component({
  selector: 'app-task-create-page',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [ErrorMessage, TaskForm],
  template: `
    <header class="page-header">
      <h1>Nueva tarea</h1>
    </header>

    <app-error-message [message]="message()" />

    <app-task-form
      submitLabel="Crear tarea"
      [saving]="saving()"
      [serverError]="apiError()"
      (save)="create($event)"
    />
  `,
})
export class TaskCreatePage {
  private readonly taskService = inject(TaskService);
  private readonly router = inject(Router);

  protected readonly saving = signal(false);
  protected readonly error = signal<unknown>(null);
  protected readonly apiError = computed(() => {
    const error = this.error();
    return error instanceof ApiRequestError ? error : null;
  });
  protected readonly message = computed(() => (this.error() ? errorMessage(this.error()) : null));

  protected async create(payload: CreateTaskPayload): Promise<void> {
    this.saving.set(true);
    this.error.set(null);

    try {
      await firstValueFrom(this.taskService.create(payload));
      await this.router.navigateByUrl('/tasks');
    } catch (error) {
      this.error.set(error);
    } finally {
      this.saving.set(false);
    }
  }
}
