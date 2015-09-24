<?php

// src/BGG/BetBundle/Controller/HomeController.php

namespace BGG\BetBundle\Controller;

use BGG\BetBundle\Entity\Home;
use BGG\BetBundle\Entity\Image;
use BGG\BetBundle\Entity\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeController extends Controller
{
    public function indexAction($page)
    {
        // On a donc accès au conteneur :
        $mailer = $this->container->get('mailer');

        // Notre liste d'annonce en dur
        $listAdverts = array(
            array(
                'title'   => 'Recherche développpeur Symfony2',
                'id'      => 1,
                'author'  => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Mission de webmaster',
                'id'      => 2,
                'author'  => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Offre de stage webdesigner',
                'id'      => 3,
                'author'  => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date'    => new \Datetime())
        );

        // Et modifiez le 2nd argument pour injecter notre liste
        return $this->render('BGGBetBundle:Home:index.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }
    public function menuAction($limit)
    {
        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche développeur Symfony2'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage webdesigner')
        );

        return $this->render('BGGBetBundle:Home:menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listAdverts' => $listAdverts
        ));
    }
    public function viewAction($id)
    {
//        $advert = array(
//            'title'   => 'Recherche développpeur Symfony2',
//            'id'      => $id,
//            'author'  => 'Alexandre',
//            'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
//            'date'    => new \Datetime()
//        );
//
//        return $this->render('BGGBetBundle:Home:view.html.twig', array(
//            'advert' => $advert
//        ));
        // On récupère le repository
//        $repository = $this->getDoctrine()
//            ->getManager()
//            ->getRepository('BGGBetBundle:Home')
//        ;
//
//        // On récupère l'entité correspondante à l'id $id
//        $advert = $repository->find($id);
//
//        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
//        // ou null si l'id $id  n'existe pas, d'où ce if :
//        if (null === $advert) {
//            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
//        }
//
//        // Le render ne change pas, on passait avant un tableau, maintenant un objet
//        return $this->render('BGGBetBundle:Home:view.html.twig', array(
//            'advert' => $advert
//        ));
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em
            ->getRepository('BGGBetBundle:Home')
            ->find($id)
        ;

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listApplications = $em
            ->getRepository('BGGBetBundle:Application')
            ->findBy(array('advert' => $advert))
        ;

        return $this->render('BGGBetBundle:Home:view.html.twig', array(
            'advert'           => $advert,
            'listApplications' => $listApplications
        ));
    }

    public function addAction(Request $request)
    {
//        // On récupère le service
//        $antispam = $this->container->get('bgg_bet.antispam');
//
//        // Je pars du principe que $text contient le texte d'un message quelconque
//        $text = '...';
//        if ($antispam->isSpam($text)) {
//            throw new \Exception('Votre message a été détecté comme spam !');
//        }
//
//        // Ici le message n'est pas un spam
//            if ($request->isMethod('POST')) {
//                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
//                return $this->redirect($this->generateUrl('bgg_home_view', array('id' => 5)));
//            }
//            return $this->render('BGGBetBundle:Home:add.html.twig');

        // Création de l'entité
        $advert = new Home();
        $advert->setTitle('Recherche développeur Symfony2.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…");
        // On peut ne pas définir ni la date ni la publication,
        // car ces attributs sont définis automatiquement dans le constructeur

        // Création de l'entité Image
        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        // On lie l'image à l'annonce
        $advert->setImage($image);

        // Création d'une première candidature
        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent("J'ai toutes les qualités requises.");


        // Création d'une deuxième candidature par exemple
        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent("Je suis très motivé.");


        // On lie les candidatures à l'annonce
        $application1->setAdvert($advert);
        $application2->setAdvert($advert);

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // Étape 1 : On « persiste » l'entité
        $em->persist($advert);

        // Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
        // on devrait persister à la main l'entité $image
        // $em->persist($image);

        // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
        $em->persist($application1);
        $em->persist($application2);

        // Étape 2 : On « flush » tout ce qui a été persisté avant
        $em->flush();

        // Reste de la méthode qu'on avait déjà écrit
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            return $this->redirect($this->generateUrl('bgg_bet_view', array('id' => $advert->getId())));
        }

        return $this->render('BGGBetBundle:Home:add.html.twig');
    }

    public function editAction($id, Request $request)
    {
//        $advert = array(
//            'title'   => 'Recherche développpeur Symfony2',
//            'id'      => $id,
//            'author'  => 'Alexandre',
//            'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
//            'date'    => new \Datetime()
//        );

        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('BGGBetBundle:Home')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // La méthode findAll retourne toutes les catégories de la base de données
        $listCategories = $em->getRepository('BGGBetBundle:Category')->findAll();

        // On boucle sur les catégories pour les lier à l'annonce
        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Home est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // Étape 2 : On déclenche l'enregistrement
        $em->flush();

        return $this->render('BGGBetBundle:Home:edit.html.twig', array(
            'advert' => $advert
        ));
    }

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('BGGBetBundle:Home')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Home est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // On déclenche la modification
        $em->flush();

        return $this->render('BGGBetBundle:Home:delete.html.twig');
    }

    public function editImageAction($advertId)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce
        $advert = $em->getRepository('BGGBetBundle:Home')->find($advertId);

        // On modifie l'URL de l'image par exemple
        $advert->getImage()->setUrl('test.png');

        // On n'a pas besoin de persister l'annonce ni l'image.
        // Rappelez-vous, ces entités sont automatiquement persistées car
        // on les a récupérées depuis Doctrine lui-même

        // On déclenche la modification
        $em->flush();

        return new Response('OK');
    }
}