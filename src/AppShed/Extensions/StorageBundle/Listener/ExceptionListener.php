<?php

namespace AppShed\Extensions\StorageBundle\Listener;

use AppShed\Extensions\StorageBundle\Exception\JsonHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Templating\EngineInterface;

/**
 * Kernel exception listener
 */
class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof JsonHttpException) {
            $data = $exception->getData();
            $response = new JsonResponse(['error' => array_merge(['code'=> $exception->getStatusCode(), 'message' => $exception->getMessage()], $data ? ['info' => $exception->getData()] : [])]);
            $event->setResponse($response);
        }
    }
}