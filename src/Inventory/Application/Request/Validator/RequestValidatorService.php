<?php

declare(strict_types=1);

namespace App\Inventory\Application\Request\Validator;

use App\Inventory\Application\Request\Dto\CreateInventoryArticlesRequestDTO;
use App\Inventory\Application\Request\Dto\InventoryRequestDto;
use App\Inventory\Application\Request\Dto\ShowInventoryArticlesRequestDTO;
use App\Inventory\Domain\Unit;
use App\Inventory\Exception\Request\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class RequestValidatorService
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
        private PayloadKeyValidator $payloadKeyValidator,
    ) {
    }

    /**
     * this method validate the request body and return DTO.
     *
     * @psalm-param Request $request
     * @psalm-param InventoryRequestDto $inventoryRequestDto
     *
     * @throws ExceptionInterface
     * @throws InvalidRequestException
     */
    public function fromHttpRequest(Request $request, InventoryRequestDto $inventoryRequestDto): InventoryRequestDto|array
    {
        // step 1 - validate keys from request
        $this->payloadKeyValidator->assertOnlyAllowedKeys($request, $inventoryRequestDto::class);
        // step 2 - build dto from request
        $dto = $this->buildDto($request, $inventoryRequestDto);

        // step 3 - validate dto
        $violations = [];
        if (is_array($dto)) {
            foreach ($dto as $dtoItem) {
                $violations[] = $this->validator->validate($dtoItem);
            }
        } else {
            $violations[] = $this->validator->validate($dto);
        }

        $errorMessages = [];

        foreach ($violations as $violation) {
            if (count($violation) > 0) {
                foreach ($violation as $errorViolation) {
                    $errorMessages[$errorViolation->getPropertyPath()] = $errorViolation->getMessage();
                }
                throw new InvalidRequestException(json_encode($errorMessages));
            }
        }

        return $dto;
    }

    /**
     * this method validate query parameter unit.
     *
     * @psalm-param Request $request
     *
     * @psalm-return string
     *
     * @throws InvalidRequestException
     */
    public function validateUnit(Request $request): string
    {
        $unit = $request->get('unit') ?? Unit::GRAM;
        if (!in_array($unit, [Unit::GRAM, Unit::KG])) {
            throw new InvalidRequestException('Invalid unit must be either kg or g');
        }

        return $unit;
    }

    /**
     * @psalm-param Request $request
     * @psalm-param InventoryRequestDto $dto
     *
     * @psalm-return InventoryRequestDto|array
     *
     * @throws ExceptionInterface
     */
    public function buildDto(Request $request, InventoryRequestDto $dto): InventoryRequestDto|array
    {
        if ($dto instanceof ShowInventoryArticlesRequestDTO) {
            $dto = $this->serializer->deserialize($request->getContent(), ShowInventoryArticlesRequestDTO::class, 'json');
        }

        if ($dto instanceof CreateInventoryArticlesRequestDTO) {
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                CreateInventoryArticlesRequestDTO::class.'[]',
                'json'
            );
        }

        return $dto;
    }
}
