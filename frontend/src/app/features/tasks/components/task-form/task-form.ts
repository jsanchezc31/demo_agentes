import {
  ChangeDetectionStrategy,
  Component,
  computed,
  effect,
  inject,
  input,
  output,
} from '@angular/core';
import {
  AbstractControl,
  NonNullableFormBuilder,
  ReactiveFormsModule,
  ValidationErrors,
  Validators,
} from '@angular/forms';
import { RouterLink } from '@angular/router';
import { ApiRequestError } from '../../../../core/models/api-error.model';
import {
  CreateTaskPayload,
  TASK_PRIORITIES,
  Task,
  TaskPriority,
} from '../../../../core/models/task.model';
import { TaskPriorityLabelPipe } from '../../../../shared/pipes/task-priority-label.pipe';

export const TITLE_MAX_LENGTH = 255;

function notBlank(control: AbstractControl<string>): ValidationErrors | null {
  return control.value.trim() === '' ? { blank: true } : null;
}

@Component({
  selector: 'app-task-form',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [ReactiveFormsModule, RouterLink, TaskPriorityLabelPipe],
  templateUrl: './task-form.html',
  styleUrl: './task-form.scss',
})
export class TaskForm {
  readonly task = input<Task | null>(null);
  readonly saving = input(false);
  readonly serverError = input<ApiRequestError | null>(null);
  readonly submitLabel = input('Guardar');
  readonly save = output<CreateTaskPayload>();

  protected readonly titleMaxLength = TITLE_MAX_LENGTH;
  protected readonly priorities = TASK_PRIORITIES;
  protected readonly fieldErrors = computed(() => this.serverError()?.fieldErrors() ?? {});

  protected readonly form = inject(NonNullableFormBuilder).group({
    title: ['', [Validators.required, Validators.maxLength(TITLE_MAX_LENGTH), notBlank]],
    description: [''],
    priority: ['medium' as TaskPriority, Validators.required],
    dueDate: [''],
  });

  private loadedTaskId: string | null = null;

  constructor() {
    effect(() => {
      const task = this.task();

      if (task && task.id !== this.loadedTaskId) {
        this.loadedTaskId = task.id;
        this.form.setValue({
          title: task.title,
          description: task.description ?? '',
          priority: task.priority,
          dueDate: task.dueDate ?? '',
        });
      }
    });
  }

  protected showError(field: 'title'): boolean {
    const control = this.form.controls[field];
    return control.invalid && (control.touched || control.dirty);
  }

  protected submit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const { title, description, priority, dueDate } = this.form.getRawValue();

    this.save.emit({
      title: title.trim(),
      description: description.trim() === '' ? null : description,
      priority,
      dueDate: dueDate === '' ? null : dueDate,
    });
  }
}
