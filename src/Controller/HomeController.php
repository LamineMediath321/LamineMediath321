<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Carousel;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CarouselType;
use App\Repository\CarouselRepository;


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

            //$carousel->setUser($this->getUser());


            $em->persist($carousel);

            $em->flush();
            $this->addFlash('success','Carousel Successfully created !');

            return $this->redirectToRoute('app_carousel_show',['id' => $carousel->getId()]);
        }

        return $this->render('home/create_carousel.html.twig',[
                                'monForm' => $form->createView()
                            ]);
    }

     /**
    *@Route("/home/{id<[0-9]+>}",name="app_carousel_show")
    */
    public function show(CarouselRepository $repo,int $id): Response
    {
        /*id est un parametre de route*/

        $carousel = $repo->find($id);

        if (!$carousel) {
            throw $this->createNotFoundException('Carousel # '.$id.' not found');
            
        }

        return $this->render('home/show.html.twig',compact('carousel'));
    }


    /**
    *@Route("/home/{id<[0-9]+>}/edit", name="app_carousel_edit",methods={"GET","POST","PUT"})
    */
    public function edit(Request $request,Carousel $carousel,EntityManagerInterface $em):Response
    {
        $form=$this->createForm(CarouselType::class,$carousel,[
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success','carousel Successfully updated !');


            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/edit_carousel.html.twig',[
                                'carousel' => $carousel,  
                                'monForm' => $form->createView()
                            ]);
    }


     /**
    *@Route("/home/{id<[0-9]+>}/delete", name="app_carousel_delete",methods={"DELETE","GET","POST"})
    */
    public function delete(Request $request,Carousel $carousel,EntityManagerInterface $em):Response
    {
        if ($this->isCsrfTokenValid('carousel_delete_'.$carousel->getId(),$request->request->get('csrf_token'))) 
        {
            $em->remove($carousel);
            $em->flush();
            $this->addFlash('info','Carousel Successfully deleted !');

        }
        return $this->redirectToRoute('app_home');

    }
}


