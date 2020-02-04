<?php


namespace App\Controller\Web;


use App\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
{

    /**
     * @Route("/" , name="api_homepage" , methods={"GET"})
     * @Template("web/default/homepage.html.twig")
     */
    public function homepageAction()
    {
        return [];
    }

}