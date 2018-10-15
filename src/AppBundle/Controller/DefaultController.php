<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Statistic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
//        Http example:
//          $url = 'http://gadget-it.ru/';
//          $this->get('url_parser')->handle($url, 25, 2);

//        Console example:
//          php bin/console parse:link 'http://gadget-it.ru/' 12 1

        $links = $this->getDoctrine()->getRepository(Statistic::class)->findBy([], ['imgCount' => 'DESC']);

        return $this->render('default/index.html.twig', [
            'links' => $links
        ]);
    }
}
