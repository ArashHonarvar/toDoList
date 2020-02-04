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
            'title' => 'tesk 1',
            'description' => "desc",
            'dueDate' => '2020-03-03T00:00:00',
        ];

        try {
            $response = $this->client->post("/api/task/create", [
                'body' => json_encode($data),
                'headers' => ['AUTH-ACCESS-TOKEN' => 'rot5clsypeogggcossw8s4ocw8s8k40']
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
            'title' => 'tesk1',
            'description' => "desc new",
            'status' => 'doing',
            'dueDate' => '2020-03-03T00:00:00',
        ];

        try {
            $response = $this->client->put("/api/task/1", [
                'body' => json_encode($data),
                'headers' => ['AUTH-ACCESS-TOKEN' => 'rot5clsypeogggcossw8s4ocw8s8k40']
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
            $response = $this->client->put("/api/task/1/change/status/ready", [
                'headers' => ['AUTH-ACCESS-TOKEN' => 'rot5clsypeogggcossw8s4ocw8s8k40']
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
                'headers' => ['AUTH-ACCESS-TOKEN' => 'rot5clsypeogggcossw8s4ocw8s8k40']
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->debugResponse($response); // Body
            }
        }
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'total', "2");
        $this->asserter()->assertResponsePropertyEquals($response, '_embedded.items[1].id', "2");
    }


}
