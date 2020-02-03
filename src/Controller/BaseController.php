<?php


namespace App\Controller;


use App\Entity\Task\Task;
use App\Entity\Task\TaskLog;
use App\Entity\User\ApiToken;
use App\Entity\User\User;
use App\Tools\ApiProblem;
use App\Tools\ApiProblemException;
use Carbon\Carbon;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Persistence\ObjectManager;
use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\SymfonyUrlGenerator;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BaseController extends AbstractController
{

    private $passwordEncoder;
    private $urlGenerator;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UrlGeneratorInterface $urlGenerator)
    {
        $reader = new AnnotationReader();
        AnnotationReader::addGlobalIgnoredName('alias');
        $this->passwordEncoder = $passwordEncoder;
        $this->urlGenerator = $urlGenerator;
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

    protected function createApiResponse($data, $statusCode = 200, $isHateoas = true)
    {
        if ($isHateoas == false) {
            $json = $this->serializeWithoutHateoas($data);
        } else {
            $json = $this->serialize($data);
        }
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
        $hateoas = HateoasBuilder::create($serializebuilder)->setUrlGenerator(null, new SymfonyUrlGenerator($this->urlGenerator))->build();
        return $hateoas->serialize($data, "json", $context);
    }

    protected function serializeWithoutHateoas($data)
    {
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        // Serialize your object in Json
        $jsonObject = $serializer->serialize($data, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        return $jsonObject;
    }

    protected function processForm(Request $request, FormInterface $form)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        if (null === $data) {
            $apiProblem = new ApiProblem(
                400,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
            );

            throw new ApiProblemException($apiProblem);
        }
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
    }

    protected function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }


    protected function throwApiProblemValidationException(FormInterface $form)
    {
        $errors = $this->getErrorsFromForm($form);
        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_VALIDATION_ERROR
        );

        $apiProblem->set("errors", $errors);
        throw new ApiProblemException($apiProblem);
    }

    protected function createToken(User $user)
    {
        $apiToken = new ApiToken();
        $apiToken->setCreatedBy($user);
        $now = Carbon::instance(new \DateTime('now'));
        $now->addMonths(1);
        $expiredAt = $now->toDateTimeString();
        $apiToken->setExpiredAt(new \DateTime($expiredAt));
        $this->getEntityManager()->persist($apiToken);
        $this->getEntityManager()->flush();
        return $apiToken;
    }

    /**
     * @return User
     */
    protected function getUserByAccessToken($accessToken)
    {
        $token = $this->getEntityManager()->getRepository(ApiToken::class)->findOneBy(['accessToken' => $accessToken]);
        return $token->getCreatedBy();
    }

    /**
     * @return TaskLog
     */
    protected function createTaskLog(Task $task, User $user, $description)
    {
        $taskLog = new TaskLog();
        $taskLog->setTask($task);
        $taskLog->setCreatedBy($user);
        $taskLog->setDescription($description);
        $this->getEntityManager()->persist($taskLog);
        $task->addLog($taskLog);
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
        return $taskLog;
    }


}