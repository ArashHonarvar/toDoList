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


class TaskControllerTest extends ApiTestCase
{

    public function testCreateTask()
    {
        $data = [
            'title' => 'test new',
            'description' => "desc new",
            'status' => 'done',
            'dueDate' => '2020-03-03T00:00:00',
        ];

        try {
            $response = $this->client->post("/api/task/create", [
                'body' => json_encode($data),
                'headers' => ['AUTH-ACCESS-TOKEN' => '98xfgegqok4cwogcs4kwsgwgo4kgg8g']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($response->getHeader("Content-type")[0], "application/json");
        $this->asserter()->assertResponsePropertiesExist($response,
            [
                'title',
                'description',
                'status'
            ]);
    }

    public function testUpdateTask()
    {
        $data = [
            'title' => 'test neww',
            'description' => "desc neww",
            'status' => 'doing',
            'dueDate' => '2020-03-03T00:00:00',
        ];

        try {
            $response = $this->client->put("/api/task/14", [
                'body' => json_encode($data),
                'headers' => ['AUTH-ACCESS-TOKEN' => '98xfgegqok4cwogcs4kwsgwgo4kgg8g']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($response->getHeader("Content-type")[0], "application/json");
        $this->asserter()->assertResponsePropertiesExist($response,
            [
                'title',
                'description',
                'status'
            ]);
        $this->asserter()->assertResponsePropertyEquals($response, 'status', "doing");
    }

    public function testChangeStatus()
    {
        try {
            $response = $this->client->put("/api/task/14/change/status/ready", [
                'headers' => ['AUTH-ACCESS-TOKEN' => '98xfgegqok4cwogcs4kwsgwgo4kgg8g']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'status', "ready");
    }

    public function testTaskList()
    {
        try {
            $response = $this->client->get("/api/task/list?status=ready", [
                'headers' => ['AUTH-ACCESS-TOKEN' => '98xfgegqok4cwogcs4kwsgwgo4kgg8g']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'total', "7");
        $this->asserter()->assertResponsePropertyEquals($response, '_embedded.items[0].id', "7");
    }


}
