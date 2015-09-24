<?php

namespace BGG\BetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BGGBetBundle:Default:index.html.twig', array('name' => $name));
    }
}
