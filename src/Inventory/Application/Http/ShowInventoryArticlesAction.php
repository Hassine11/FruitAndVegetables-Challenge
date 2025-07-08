<?php

declare(strict_types=1);

namespace App\Inventory\Application\Http;

use App\Inventory\Application\Database\InventoryLoader;
use App\Inventory\Application\Database\InventoryNormalizer;
use App\Inventory\Application\Request\Dto\ShowInventoryArticlesRequestDTO;
use App\Inventory\Application\Request\Validator\RequestValidatorService;
use App\Inventory\Application\Service\InventoryFilterService;
use App\Inventory\Exception\Request\InvalidRequestException;
use App\Trait\HttpResponseTrait;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** @psalm-suppress UnusedClass */
class ShowInventoryArticlesAction extends AbstractController
{
    use HttpResponseTrait;

    public function __construct(
        private readonly InventoryLoader $inventoryLoader,
        private readonly InventoryFilterService $inventoryFilterService,
        private readonly RequestValidatorService $requestValidatorService,
        private readonly InventoryNormalizer $inventoryNormalizer,
        private readonly Stopwatch $stopwatch,
    ) {
    }

    /**
     * @psalm-suppress PossiblyUnusedParam
     *
     * @throws Exception
     * @throws \Exception
     */
    #[Route('/inventory/list', name: 'inventory_list', methods: ['GET'])]
    public function __invoke(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->stopwatch->start('request body validation');
        try {
            $unit = $this->requestValidatorService->validateUnit($request);
            $requestDto = $this->requestValidatorService->fromHttpRequest($request, new ShowInventoryArticlesRequestDTO());
        } catch (InvalidRequestException $exception) {
            return $this->badHttpResponse($exception);
        } catch (ExceptionInterface $e) {
            return $this->badHttpResponse($e);
        }

        $this->stopwatch->stop('request body validation');

        $this->stopwatch->start('loading inventory articles');
        $items = $this->inventoryLoader->loadInventory();
        $this->stopwatch->stop('loading inventory articles');

        $this->stopwatch->start('applying filters');
        $items = $this->inventoryFilterService->applyFilters($requestDto, $items);
        $this->stopwatch->stop('applying filters');

        $this->stopwatch->start('response normalization');
        $response = $this->inventoryNormalizer->normalize($items, $unit);
        $this->stopwatch->stop('response normalization');

        return new JsonResponse([
            'collections' => $response,
        ], Response::HTTP_OK);
    }
}
