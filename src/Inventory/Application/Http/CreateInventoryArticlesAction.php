<?php

declare(strict_types=1);

namespace App\Inventory\Application\Http;

use App\Inventory\Application\Database\InventoryNormalizer;
use App\Inventory\Application\Dto\CreateArticlesRequestDTO;
use App\Inventory\Application\Service\InventoryService;
use App\Repository\InventoryRepository;
use App\Service\Request\Validator\RequestValidatorService;
use App\Trait\HttpResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Stopwatch\Stopwatch;

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
        private readonly LoggerInterface $logger,
        private readonly Stopwatch $stopwatch,
    ) {
    }

    /**
     * @psalm-suppress PossiblyUnusedParam
     *
     * @throws \Exception|ExceptionInterface
     */
    #[Route('/inventory/create', name: 'inventory_create', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $insertedIds = [];
        $this->stopwatch->start('request body validation');
        try {
            /** @var CreateArticlesRequestDTO[] $payload */
            $payload = $this->requestValidatorService->fromHttpRequest($request, new CreateArticlesRequestDTO());
        } catch (\Exception $exception) {
            $this->logger->warning($exception->getMessage(), [
                'area' => __METHOD__.' request body validation',
                'payload' => json_encode($request->request->all()),
            ]);

            return $this->badHttpResponse($exception);
        }
        $this->stopwatch->stop('request body validation');

        $this->stopwatch->start('building items from payload');
        try {
            $items = $this->inventoryService->buildItemsFromPayload($payload);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'area' => __METHOD__.' building items from payload',
                'payload' => json_encode($payload),
            ]);

            return $this->badHttpResponse($exception);
        }
        $this->stopwatch->stop('building items from payload');

        try {
            $this->stopwatch->start('inserting items into database');
            $this->entityManager->beginTransaction();
            foreach ($items as $item) {
                $insertedIds[] = $this->inventoryRepository->storeInventoryArticle($item);
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'area' => __METHOD__.' inserting items into database',
                'payload' => json_encode($payload),
            ]);
            $this->entityManager->rollback();

            return $this->badHttpResponse($exception);
        }
        $this->entityManager->commit();
        $this->stopwatch->stop('inserting items into database');

        if (empty($insertedIds)) {
            $exception = new \RuntimeException('issue with insert data no inserted ids returned , please verify !');
            $this->logger->error($exception->getMessage());

            return $this->badHttpResponse($exception);
        }

        return new JsonResponse([
            'itemsIds' => $insertedIds,
        ], Response::HTTP_CREATED);
    }
}
