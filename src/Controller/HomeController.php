<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Carousel;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CarouselType;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }



     /**
     * @Route("/home/create_carousel",name="app_carousel_create",methods={"GET","POST"})
     */
    public function create(Request $request,EntityManagerInterface $em)
    {

        $carousel= new Carousel;


       $form=$this->createForm(CarouselType::class,$carousel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pin->setUser($this->getUser());


            $em->persist($carousel);

            $em->flush();
            //$this->addFlash('success','Pin Successfully created !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/create_carousel.html.twig',[
                                'monForm' => $form->createView()
                            ]);
    }
}


