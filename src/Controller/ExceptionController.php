<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionController extends AbstractController
{
    #[Route('/error', name: 'error')]
    public function showException(\Throwable $exception): Response
    {
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        $template = ($statusCode === Response::HTTP_NOT_FOUND) ? 'error/404.html.twig' : 'error/500.html.twig';

        return $this->render($template, [
            'exception' => $exception,
        ]);
    }
}
