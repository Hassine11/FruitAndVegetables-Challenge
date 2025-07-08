<?php

use App\Tests\DataHelper\TestDataHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

uses(WebTestCase::class);

beforeEach(/**
 * @throws Doctrine\DBAL\Exception
 */ function () {
    static::ensureKernelShutdown();
    $this->client = static::createClient();
    $this->conn = static::getContainer()->get('doctrine.dbal.default_connection');

    // Inject test data
    TestDataHelper::insertDummyData($this->conn);
});

it('returns all the data', function () {
    $this->client->request(
        'GET',
        '/inventory/list',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([])
    );

    $response = $this->client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);

    $data = json_decode($response->getContent(), true)['collections'];

    expect($data)->toBeArray()
        ->and($data)->toHaveCount(3) // 'fruit & vegetable & totalItems'
        ->and($data)->toHaveKeys(['fruits', 'vegetables', 'totalItems'])
        ->and($data['fruits'])->toHaveCount(1)
        ->and($data['vegetables'])->toHaveCount(2);
});

it('returns filtered fruits articles from inventory', function () {
    $this->client->request(
        'GET',
        '/inventory/list?unit=g',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            'name' => 'dummy1',
            'category' => 'fruit',
        ])
    );

    $response = $this->client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_OK);

    $data = json_decode($response->getContent(), true)['collections'];

    expect($data)->toBeArray()
        ->and($data)->toHaveCount(3)
        ->and($data['fruits'][0]['name'])->toBe('dummy1')
        ->and($data['fruits'][0]['weight'])->toBe(1000)
        ->and($data['fruits'][0]['unit'])->toBe('g');
});

it('returns filtered bad http response because of invalid query parameter value', function () {
    $this->client->request(
        'GET',
        '/inventory/list?unit=INVALID',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            'name' => 'dummy1',
            'category' => 'fruit',
        ])
    );

    $response = $this->client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);
});

it('returns filtered bad http response because of invalid payload category value', function () {
    $this->client->request(
        'GET',
        '/inventory/list?unit=INVALID',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            'name' => 'dummy1',
            'category' => 'INVALID',
        ])
    );

    $response = $this->client->getResponse();

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST);
});
