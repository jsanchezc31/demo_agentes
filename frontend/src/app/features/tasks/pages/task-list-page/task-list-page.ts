import { ChangeDetectionStrategy, Component, computed, inject, signal } from '@angular/core';
import { rxResource } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import { errorMessage } from '../../../../core/models/api-error.model';
import { Task, TaskFilters as Filters, TaskStatus } from '../../../../core/models/task.model';
import { ErrorMessage } from '../../../../shared/components/error-message/error-message';
import { Paginator } from '../../../../shared/components/paginator/paginator';
import { TaskFilters } from '../../components/task-filters/task-filters';
import { TaskItem } from '../../components/task-item/task-item';
import { TaskService } from '../../services/task.service';

const PAGE_SIZE = 10;

@Component({
  selector: 'app-task-list-page',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, ErrorMessage, Paginator, TaskFilters, TaskItem],
  templateUrl: './task-list-page.html',
  styleUrl: './task-list-page.scss',
})
export class TaskListPage {
  private readonly taskService = inject(TaskService);

  protected readonly filters = signal<Filters>({ status: null, priority: null });
  protected readonly page = signal(1);
  protected readonly limit = PAGE_SIZE;
  protected readonly busyTaskId = signal<string | null>(null);
  protected readonly actionError = signal<string | null>(null);

  protected readonly tasks = rxResource({
    params: () => ({ ...this.filters(), page: this.page(), limit: this.limit }),
    stream: ({ params }) => this.taskService.list(params),
  });

  protected readonly loadError = computed(() => {
    const error = this.tasks.error();
    return error ? errorMessage(error) : null;
  });

  protected readonly hasFilters = computed(() => {
    const { status, priority } = this.filters();
    return status !== null || priority !== null;
  });

  protected changeFilters(filters: Filters): void {
    this.filters.set(filters);
    this.page.set(1);
  }

  protected async changeStatus(task: Task, status: TaskStatus): Promise<void> {
    await this.run(task, () => firstValueFrom(this.taskService.changeStatus(task.id, status)));
    this.tasks.reload();
  }

  protected async remove(task: Task): Promise<void> {
    if (!confirm(`¿Eliminar la tarea «${task.title}»?`)) {
      return;
    }

    const removed = await this.run(task, () => firstValueFrom(this.taskService.delete(task.id)));
    const wasLastOnPage = this.tasks.hasValue() && this.tasks.value().items.length === 1;

    if (removed && wasLastOnPage && this.page() > 1) {
      this.page.update((page) => page - 1);
    } else {
      this.tasks.reload();
    }
  }

  private async run(task: Task, action: () => Promise<unknown>): Promise<boolean> {
    this.busyTaskId.set(task.id);
    this.actionError.set(null);

    try {
      await action();
      return true;
    } catch (error) {
      this.actionError.set(errorMessage(error));
      return false;
    } finally {
      this.busyTaskId.set(null);
    }
  }
}
