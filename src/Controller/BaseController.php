<?php


namespace App\Controller;


use Doctrine\Persistence\ObjectManager;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{


    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    protected function createApiResponse($data, $statusCode = 200)
    {
        $json = $this->serialize($data);
        return new Response($json, $statusCode, ["Content-type" => "application/json"]);
    }

    protected function serialize($data)
    {
        //JMS Serializer
        $serializebuilder = SerializerBuilder::create();
        $serializebuilder->setPropertyNamingStrategy(new \JMS\Serializer\Naming\IdenticalPropertyNamingStrategy());
        $serializer = $serializebuilder->build();
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        return $serializer->serialize($data, "json", $context);
    }




}