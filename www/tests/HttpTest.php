<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

require_once __DIR__ . '/../vendor/autoload.php';

class HttpTest extends TestCase
{
    public function testIndexPageIsAvailable()
    {
        $client = new Client(['base_uri' => 'http://nginx/']);

        $response = $client->request('GET', 'index.php');

        $this->assertEquals(200, $response->getStatusCode());
        
        $this->assertNotEmpty((string)$response->getBody());
    }
}
