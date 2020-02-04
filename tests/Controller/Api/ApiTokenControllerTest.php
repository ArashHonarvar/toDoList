<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;


use App\Controller\Api\ProgrammerController;

use App\Tests\ApiTestCase;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;


class ApiTokenControllerTest extends ApiTestCase
{

    public function testShowToken()
    {
        try {
            $response = $this->client->get("/token/14n2z8egic3k4wgsogss4k8og088044/");
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'accessToken');
        $this->asserter()->assertResponsePropertyExists($response, 'refreshToken');
    }

    public function testRefreshToken()
    {
        try {
            $response = $this->client->post("/token/refresh", [
                'headers' => ['AUTH-REFRESH-TOKEN' => '9hgi9a2s9iosow0kg4kg444swwcsksw']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }

        $this->assertEquals(201, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'accessToken');
        $this->asserter()->assertResponsePropertyExists($response, 'refreshToken');
    }

}
