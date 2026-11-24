<?php

declare(strict_types=1);

namespace App\UI\Http\EventListener;

use App\Domain\Task\Exception\InvalidTaskDueDate;
use App\Domain\Task\Exception\InvalidTaskId;
use App\Domain\Task\Exception\InvalidTaskTitle;
use App\Domain\Task\Exception\InvalidTaskTransition;
use App\Domain\Task\Exception\TaskNotFound;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
final readonly class ExceptionListener
{
    public function __construct(
        #[Autowire('%kernel.debug%')]
        private bool $debug,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api')) {
            return;
        }

        $error = $this->toError($event->getThrowable());

        $event->setResponse(new JsonResponse(
            ['error' => ['code' => $error->code, 'message' => $error->message, 'details' => $error->details]],
            $error->status,
        ));
    }

    private function toError(\Throwable $e): ApiError
    {
        return match (true) {
            $e instanceof TaskNotFound => new ApiError(Response::HTTP_NOT_FOUND, 'task_not_found', $e->getMessage()),
            $e instanceof InvalidTaskId => new ApiError(Response::HTTP_NOT_FOUND, 'task_not_found', 'La tarea no existe'),
            $e instanceof InvalidTaskTitle => new ApiError(Response::HTTP_UNPROCESSABLE_ENTITY, 'invalid_task_title', $e->getMessage()),
            $e instanceof InvalidTaskTransition => new ApiError(Response::HTTP_UNPROCESSABLE_ENTITY, 'invalid_task_transition', $e->getMessage()),
            $e instanceof InvalidTaskDueDate => new ApiError(Response::HTTP_UNPROCESSABLE_ENTITY, 'invalid_task_due_date', $e->getMessage()),
            $e instanceof HttpExceptionInterface => $this->fromHttpException($e),
            default => new ApiError(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'internal_error',
                $this->debug ? $e->getMessage() : 'Error interno del servidor',
            ),
        };
    }

    private function fromHttpException(HttpExceptionInterface&\Throwable $e): ApiError
    {
        $previous = $e->getPrevious();

        if ($previous instanceof ValidationFailedException) {
            $details = [];
            foreach ($previous->getViolations() as $violation) {
                $details[] = ['field' => $violation->getPropertyPath(), 'message' => (string) $violation->getMessage()];
            }

            return new ApiError(Response::HTTP_UNPROCESSABLE_ENTITY, 'validation_failed', 'Los datos enviados no son válidos', $details);
        }

        $status = $e->getStatusCode();

        return match ($status) {
            Response::HTTP_BAD_REQUEST => $previous instanceof NotEncodableValueException
                ? new ApiError($status, 'invalid_json', 'El cuerpo de la petición no es un JSON válido')
                : new ApiError($status, 'bad_request', 'La petición no es válida'),
            Response::HTTP_NOT_FOUND => new ApiError($status, 'not_found', 'Recurso no encontrado'),
            Response::HTTP_METHOD_NOT_ALLOWED => new ApiError($status, 'method_not_allowed', 'Método no permitido'),
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE => new ApiError($status, 'unsupported_media_type', 'El cuerpo debe enviarse como application/json'),
            Response::HTTP_UNPROCESSABLE_ENTITY => new ApiError($status, 'validation_failed', 'Los datos enviados no son válidos'),
            default => new ApiError($status, 'http_error', Response::$statusTexts[$status] ?? 'Error'),
        };
    }
}
