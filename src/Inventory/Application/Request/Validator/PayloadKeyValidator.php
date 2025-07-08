<?php

namespace App\Inventory\Application\Request\Validator;

use App\Inventory\Application\Request\Dto\InventoryRequestDto;
use App\Inventory\Exception\Request\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;

class PayloadKeyValidator
{
    /**
     * this method validate if the request body respects the open api specification otherwise it throws exception.
     *
     * @psalm-param Request $request
     * @psalm-param string $dtoClass
     *
     * @throws InvalidRequestException
     */
    public function assertOnlyAllowedKeys(Request $request, string $dtoClass): void
    {
        $input = (array) json_decode($request->getContent(), true);
        $isAssoc = array_keys($input) !== range(0, count($input) - 1);
        $input = $isAssoc ? [$input] : $input;

        /** @var InventoryRequestDto $dto */
        $dto = new $dtoClass();
        $dtoProperties = $dto::keys();

        foreach ($input as $data) {
            $extraKeys = array_diff(array_keys($data), $dtoProperties);
            if (!empty($extraKeys)) {
                throw new InvalidRequestException(sprintf('Unexpected fields: %s', implode(', ', $extraKeys)));
            }
        }
    }
}
