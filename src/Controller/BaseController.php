<?php


namespace App\Controller;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Persistence\ObjectManager;
use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BaseController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $reader = new AnnotationReader();
        AnnotationReader::addGlobalIgnoredName('alias');
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return UserPasswordEncoderInterface
     */
    protected function getPasswordEncoder()
    {
        return $this->passwordEncoder;
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
        $hateoas = HateoasBuilder::create($serializebuilder)->build();
        return $hateoas->serialize($data, "json", $context);
    }


}