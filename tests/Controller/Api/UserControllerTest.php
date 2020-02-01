<?php
declare(strict_types=1);

namespace App\Tests\Controller\Api;


use App\Tests\ApiTestCase;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\JsonResponse;


class UserControllerTest extends ApiTestCase
{

    public function testRegister()
    {
        $data = [
            'nickname' => 'Programmer',
            'avatarNumber' => 2,
            'tagLine' => 'A good Programmer'
        ];


        try {
            $response = $this->client->post("/api/user/registers", [
                'body' => json_encode($data)
            ]);
        } catch (\Exception $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                dump((string)$response->getBody());
            }
        }


        $this->assertEquals(201, $response->getStatusCode());
    }

}
