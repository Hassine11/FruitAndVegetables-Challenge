<?php

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

uses(WebTestCase::class);

beforeEach(/**
 * @throws Doctrine\DBAL\Exception
 */ function () {
    static::ensureKernelShutdown();
    $this->client = static::createClient();

    $this->conn = static::getContainer()->get(Connection::class);
    $this->conn->executeStatement('TRUNCATE TABLE inventory');
});

it('create single item', function () {
    $this->client->request(
        'POST',
        '/inventory/create',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            [
                'name' => 'testing',
                'category' => 'fruit',
                'weight' => 1000,
                'unit' => 'g',
            ],
        ])
    );

    $response = $this->client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED);

    $data = json_decode($response->getContent(), true);

    expect($data)->toBeArray()
        ->and($data)->toHaveKey('itemsIds')
        ->and($data['itemsIds'])->toHaveCount(1);
});

it('create multiple items (bulk)', function () {
    $this->client->request(
        'POST',
        '/inventory/create',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            [
                'name' => 'for testing',
                'category' => 'fruit',
                'weight' => 1000,
                'unit' => 'g',
            ],
            [
                'name' => 'new',
                'category' => 'vegetable',
                'weight' => 15,
                'unit' => 'kg',
            ],
        ])
    );

    $response = $this->client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED);

    $data = json_decode($response->getContent(), true);

    expect($data)->toBeArray()
        ->and($data)->toHaveKey('itemsIds')
        ->and($data['itemsIds'])->toHaveCount(2);
});

it('return invalid response because of bad data', function () {
    $this->client->request(
        'POST',
        '/inventory/create',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            [
                'INVALID' => 'for testing',
                'category' => 'fruit',
                'weight' => 1000,
                'unit' => 'g',
            ],
        ])
    );

    $response = $this->client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);
});

it('return invalid response because of item name already exist (created)', function () {
    $this->client->request(
        'POST',
        '/inventory/create',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            [
                'name' => 'for testing',
                'category' => 'fruit',
                'weight' => 1000,
                'unit' => 'g',
            ],
        ])
    );

    $response = $this->client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED);

    $this->client->request(
        'POST',
        '/inventory/create',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            [
                'name' => 'for testing', // same name already inserted
                'category' => 'fruit',
                'weight' => 1000,
                'unit' => 'g',
            ],
        ])
    );

    $response = $this->client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);
});
