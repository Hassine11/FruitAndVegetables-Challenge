<?php

declare(strict_types=1);

namespace App\Trait;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait HttpResponseTrait
{
    /**
     * @psalm-param  Exception $exception
     *
     * @psalm-return  JsonResponse
     */
    public function badHttpResponse(\Exception $exception): JsonResponse
    {
        return new JsonResponse([
            'error' => $exception->getMessage(),
        ], Response::HTTP_BAD_REQUEST);
    }
}
