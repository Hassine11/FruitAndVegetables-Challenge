<?php

declare(strict_types=1);

namespace App\Inventory\Application\Http;

use App\Inventory\Application\Database\InventoryNormalizer;
use App\Inventory\Application\Request\Dto\CreateInventoryArticlesRequestDTO;
use App\Inventory\Application\Request\Validator\RequestValidatorService;
use App\Inventory\Application\Service\InventoryService;
use App\Inventory\Domain\ItemName;
use App\Inventory\Exception\Item\ItemAlreadyExistException;
use App\Repository\InventoryRepository;
use App\Trait\HttpResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** @psalm-suppress UnusedClass */
class CreateInventoryArticlesAction extends AbstractController
{
    use HttpResponseTrait;

    public function __construct(
        private readonly InventoryNormalizer $inventoryNormalizer,
        private readonly InventoryRepository $inventoryRepository,
        private readonly InventoryService $inventoryService,
        private readonly RequestValidatorService $requestValidatorService,
        private readonly EntityManagerInterface $entityManager,
        private readonly Stopwatch $stopwatch,
    ) {
    }

    /**
     * @psalm-suppress PossiblyUnusedParam
     *
     * @throws \Exception|ExceptionInterface
     */
    #[Route('/inventory/create', name: 'inventory_create', methods: ['POST'])]
    public function __invoke(Request $request, ValidatorInterface $validator, SerializerInterface $serializer): JsonResponse
    {
        $this->stopwatch->start('request body validation');
        try {
            /** @var CreateInventoryArticlesRequestDTO[] $payload */
            $payload = $this->requestValidatorService->fromHttpRequest($request, new CreateInventoryArticlesRequestDTO());
        } catch (\Exception $exception) {
            return $this->badHttpResponse($exception);
        }
        $this->stopwatch->stop('request body validation');

        $this->stopwatch->start('items existence validation');
        foreach ($payload as $payloadItem) {
            if ($this->inventoryRepository->itemExists(ItemName::fromString($payloadItem->name))) {
                return $this->badHttpResponse(new ItemAlreadyExistException('Item with this name '.$payloadItem->name.' already exists !'));
            }
        }
        $this->stopwatch->stop('items existence validation');

        $this->stopwatch->start('building items from payload');
        try {
            $items = $this->inventoryService->buildItemsFromPayload($payload);
        } catch (\Exception $exception) {
            return $this->badHttpResponse($exception);
        }
        $this->stopwatch->stop('building items from payload');

        $this->stopwatch->start('inserting items into database');
        $this->entityManager->beginTransaction();
        foreach ($items as $item) {
            try {
                $insertedIds[] = $this->inventoryRepository->storeInventoryArticle($item);
            } catch (\Exception $exception) {
                $this->entityManager->rollback();

                return $this->badHttpResponse($exception);
            }
        }
        $this->entityManager->commit();
        $this->stopwatch->stop('inserting items into database');

        if (empty($insertedIds)) {
            return $this->badHttpResponse(new \RuntimeException('issue with insert data no inserted ids returned , please verify !'));
        }

        return new JsonResponse([
            'itemsIds' => $insertedIds,
        ], Response::HTTP_CREATED);
    }
}
