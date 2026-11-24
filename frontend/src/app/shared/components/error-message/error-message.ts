import { ChangeDetectionStrategy, Component, input } from '@angular/core';

@Component({
  selector: 'app-error-message',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @if (message(); as text) {
      <p class="error-message" role="alert">{{ text }}</p>
    }
  `,
  styles: `
    .error-message {
      margin: 1rem 0;
      padding: 0.75rem 1rem;
      border: 1px solid var(--color-danger);
      border-radius: var(--radius);
      background: var(--color-danger-soft);
      color: var(--color-danger);
    }
  `,
})
export class ErrorMessage {
  readonly message = input<string | null>(null);
}
