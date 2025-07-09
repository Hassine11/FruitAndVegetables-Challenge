<?php

declare(strict_types=1);

namespace App\Service\Request\Validator;

use App\Inventory\Application\Dto\CreateArticlesRequestDTO;
use App\Inventory\Application\Dto\ShowArticlesRequestDTO;
use App\Inventory\Domain\Unit;
use App\Inventory\Exception\Request\InvalidRequestException;
use App\Service\Request\RequestDto;
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
     * @psalm-param RequestDto $inventoryRequestDto
     *
     * @throws ExceptionInterface
     * @throws InvalidRequestException
     */
    public function fromHttpRequest(Request $request, RequestDto $inventoryRequestDto): RequestDto|array
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
     * @psalm-param RequestDto $dto
     *
     * @psalm-return RequestDto|array
     *
     * @throws ExceptionInterface
     */
    public function buildDto(Request $request, RequestDto $dto): RequestDto|array
    {
        if ($dto instanceof ShowArticlesRequestDTO) {
            $dto = $this->serializer->deserialize($request->getContent(), ShowArticlesRequestDTO::class, 'json');
        }

        if ($dto instanceof CreateArticlesRequestDTO) {
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                CreateArticlesRequestDTO::class.'[]',
                'json'
            );
        }

        return $dto;
    }
}
