<?php

namespace App\EventSubscriber;

use App\Exception\ValidationViolationException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = [
            'code' => -1,
        ];

        $flatten = FlattenException::createFromThrowable($event->getThrowable());
        $response['error'] = $flatten->getMessage();

        if ($exception instanceof ValidationViolationException) {
            foreach ($exception->getViolations() as $violation)
                $response['validation_errors'][] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
        }

        $response = new JsonResponse($response, 200);

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}