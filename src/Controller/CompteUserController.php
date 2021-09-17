<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserEditFormType;

class CompteUserController extends AbstractController
{
    /**
     * @Route("/compte/user", name="app_admin_user")
     */
    public function index(): Response
    {

    	$user=$this->getUser();

        return $this->render('compte_user/admin_user.html.twig', [
            'user' => $user,
        ]);
    }


    /**
    *@Route("/compte_user/edit_user", name="app_user_edit",methods={"GET","POST","PUT"})
    */
    public function edit_user(Request $request,EntityManagerInterface $em):Response
    {
        $user=$this->getUser();
        $form=$this->createForm(UserEditFormType::class,$user,[
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success','Mise a jour effectue avec succes !');


            return $this->redirectToRoute('app_admin_user');
        }

          return $this->render('compte_user/edit_user.html.twig',[
                                'user' => $user,
                                'monForm' => $form->createView()
                            ]);
    }


}
