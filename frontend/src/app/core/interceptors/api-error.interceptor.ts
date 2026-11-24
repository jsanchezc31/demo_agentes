import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { catchError, throwError } from 'rxjs';
import { ApiRequestError, isApiErrorBody } from '../models/api-error.model';

const UNREACHABLE_STATUSES = new Set([0, 502, 503, 504]);

export const apiErrorInterceptor: HttpInterceptorFn = (request, next) =>
  next(request).pipe(catchError((error: unknown) => throwError(() => toApiRequestError(error))));

function toApiRequestError(error: unknown): unknown {
  if (!(error instanceof HttpErrorResponse)) {
    return error;
  }

  if (isApiErrorBody(error.error)) {
    const { code, message, details } = error.error.error;
    return new ApiRequestError(error.status, code, message, details ?? []);
  }

  if (UNREACHABLE_STATUSES.has(error.status)) {
    return new ApiRequestError(
      error.status,
      'api_unreachable',
      'No se puede conectar con la API. Comprueba que el backend está arrancado.',
    );
  }

  return new ApiRequestError(
    error.status,
    'unexpected_error',
    `Error inesperado (${error.status})`,
  );
}
