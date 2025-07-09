<?php

declare(strict_types=1);

namespace App\Inventory\Application\Http;

use App\Inventory\Application\Database\InventoryLoader;
use App\Inventory\Application\Database\InventoryNormalizer;
use App\Inventory\Application\Dto\ShowArticlesRequestDTO;
use App\Inventory\Application\Service\InventoryFilterService;
use App\Inventory\Exception\Request\InvalidRequestException;
use App\Service\Request\Validator\RequestValidatorService;
use App\Trait\HttpResponseTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Stopwatch\Stopwatch;

/** @psalm-suppress UnusedClass */
class ShowInventoryArticlesAction extends AbstractController
{
    use HttpResponseTrait;

    public function __construct(
        private readonly InventoryLoader $inventoryLoader,
        private readonly InventoryFilterService $inventoryFilterService,
        private readonly RequestValidatorService $requestValidatorService,
        private readonly InventoryNormalizer $inventoryNormalizer,
        private readonly LoggerInterface $logger,
        private readonly Stopwatch $stopwatch,
    ) {
    }

    /**
     * @throws \Exception
     */
    #[Route('/inventory/list', name: 'inventory_list', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $response = [];
        $this->stopwatch->start('request body validation');
        try {
            $unit = $this->requestValidatorService->validateUnit($request);
            $requestDto = $this->requestValidatorService->fromHttpRequest($request, new ShowArticlesRequestDTO());
        } catch (InvalidRequestException $exception) {
            $this->logger->warning($exception->getMessage());

            return $this->badHttpResponse($exception);
        } catch (\Throwable $e) {
            $this->logger->warning($e->getMessage(), [
                'area' => __METHOD__.' request body validation',
                'payload' => json_encode($request->request->all()),
            ]);

            return $this->badHttpResponse($e);
        }

        $this->stopwatch->stop('request body validation');

        try {
            $this->stopwatch->start('loading inventory articles');
            $items = $this->inventoryLoader->loadInventory();
            $this->stopwatch->stop('loading inventory articles');

            if (!empty($items->toArray())) {
                $this->stopwatch->start('applying filters');
                $items = $this->inventoryFilterService->applyFilters($requestDto, $items);
                $this->stopwatch->stop('applying filters');

                $this->stopwatch->start('response normalization');
                $response = $this->inventoryNormalizer->normalize($items, $unit);
                $this->stopwatch->stop('response normalization');
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'area' => 'loading inventory articles and applying filters',
                'payload' => json_encode($requestDto),
            ]);

            return $this->badHttpResponse($exception);
        }

        return new JsonResponse([
            'collections' => $response,
        ], Response::HTTP_OK);
    }
}
