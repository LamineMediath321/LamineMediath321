<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserEditFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;
use App\Entity\ImageArticle;
use App\Entity\SousCategorie;
use App\Entity\Article;
use App\Repository\SousCategorieRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Repository\ImageArticleRepository;
use App\Repository\BanqueRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class CompteUserController extends AbstractController
{
    /**
     * @Route("/compte/user", name="app_admin_user")
     */
    public function index(BanqueRepository $bankRepo): Response
    {

    	$user=$this->getUser();
        
         //On recupere son compte bancaire
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
        ]);

        return $this->render('compte_user/admin_user.html.twig', [
            'user' => $user,
            'bank' => $bank
        ]);
    }


    /**
    *@Route("/compte_user/edit_user", name="app_user_edit",methods={"GET","POST","PUT"})
    */
    public function edit_user(Request $request,EntityManagerInterface $em,BanqueRepository $bankRepo):Response
    {
        $user=$this->getUser();
         //On recupere son compte bancaire
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
        ]);
        $form=$this->createForm(UserEditFormType::class,$user,[
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            //$this->addFlash('success','Mise a jour effectue avec succes !');


            return $this->redirectToRoute('app_admin_user');
        }

          return $this->render('compte_user/edit_user.html.twig',[
                                'user' => $user,
                                'bank' => $bank,
                                'monForm' => $form->createView()
                            ]);
    }


    /**
    *@Route("/compte_user/creer_article", name="app_article",methods={"GET","POST","PUT"})
    */
    public function creer_article(Request $request,EntityManagerInterface $em,BanqueRepository $bankRepo):Response
    {
        $user=$this->getUser();
          //On recupere son compte bancaire
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
        ]);

        $form=$this->createFormBuilder([
            'Nombre_etoiles' => 1
        ])
            ->add('Categorie',EntityType::class,[
                'placeholder' => 'Choisissez une categorie',
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
                'query_builder' => function(CategorieRepository $cateRepo){
                    return $cateRepo->createQueryBuilder('c')->orderBy('c.nomCategorie','ASC');
                },
                 'constraints' => new NotBlank(['message' => 'Please choisissez une categorie']) 
            ])

            ->add('Sous_Categorie',EntityType::class,[
                'placeholder' => 'Choisissez une sous categorie',
                'disabled' => false,
                'class' => SousCategorie::class,
                'choice_label' => 'nomCategorie',
                'query_builder' => function(SousCategorieRepository $sousCateRepo){
                    return $sousCateRepo->createQueryBuilder('s')->orderBy('s.nomCategorie','ASC');
                },
                 'constraints' => new NotBlank(['message' => 'Please choisissez une sous categorie']) 
            ])

            ->add('Nom_article',TextType::class,[
                'constraints' => new NotBlank(['message' => 'Le nom ne de votre article ne doit pas être vide']) 
            ])

           ->add('Description',TextareaType::class,[
                'constraints' => new NotBlank(['message' => 'La description ne doit pas être vide'])
            ])
            ->add('Lieu_de_Vente',TextareaType::class,[
                'constraints' => new NotBlank(['message' => 'Le Lieu de vente ne doit pas être vide'])
            ])

            ->add('Prix_article',TextType::class)

            ->add('Nombre_etoiles',IntegerType::class,[
                'label' => 'Nombre etoiles entre 1 & 5'
            ])

            ->add('image_1', FileType::class,[
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('image_2', FileType::class,[
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('image_3', FileType::class,[
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('image_4', FileType::class,[
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])

             ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /*          ARTICLE                */
            $article = new Article();

            $article->setNomArticle($form->get('Nom_article')->getData());
            $article->setDescription($form->get('Description')->getData());
            $article->setLieuVente($form->get('Lieu_de_Vente')->getData());
            $article->setDescription($form->get('Description')->getData());
            $article->setSousCategorie($form->get('Sous_Categorie')->getData());
            $article->setPrice($form->get('Prix_article')->getData());
            $article->setEtoiles($form->get('Nombre_etoiles')->getData());

            for ($i=1; $i <=4 ; $i++) { 
                // On récupère les images transmises
                $images = $form->get('image_'.$i)->getData();
                
              // On boucle sur les images
                foreach($images as $image)
                {
                    // On génère un nouveau nom de fichier
                    $fichier = md5(uniqid()).'.'.$image->guessExtension();
                        
                    // On copie le fichier dans le dossier uploads
                    $image->move(
                    $this->getParameter('images_directory'),
                        $fichier
                    );
                        
                    // On crée l'image dans la base de données
                    $img = new ImageArticle();
                    $img->setImageName($fichier);
                    $img->setNumImage($i);
                    $em->persist($img);
                    $article->addImageArticle($img);
                }
            }
           
            $em->persist($article);
            $article->setUser($this->getUser());
            $em->flush();
            return $this->redirectToRoute('app_article_2',[
                'id_article' => $article->getId() 
            ]);
        }


        return $this->render('compte_user/creer_article.html.twig',[
            'user' => $user,
            'bank' => $bank,
            'form' => $form->createView()
        ]);

    }


    /**
     * @Route("/compte_user/creer_article_2/{id_article<[0-9]+>}", name="app_article_2")
     */
    public function creer_article2(Request $request,ImageArticleRepository $imgRepo,int $id_article,BanqueRepository $bankRepo,EntityManagerInterface $em): Response
    {
        $user=$this->getUser();
        //On recupere son compte bancaire
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
        ]);
        $imageArticle=$imgRepo->findOneBy([
            'article' => $id_article,
            'numImage' => 1
        ]);
        $form=$this->createFormBuilder()
            ->add('offre_de_base',CheckboxType::class)
            ->add('ofrre_vip',CheckboxType::class)
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             
            //On regarde la ou il a coche 
            if ($form->get('offre_de_base')->getData()===true) {
               //On verifie si il possible d'effectuer l'operation
                if ($bank->getPieces()>=10) {
                    $bank->setPieces($bank->getPieces()-10);
                    $em->flush();
                }
                /*else{
                    //Sinon on redirige vers la page d'achat de pieces 
                    return $this->redirectToRoute('app_achat_piece');
                }*/
            }
            elseif ($form->get('offre_vip')->getData()===true) {
                if ($bank->getPieces()>=20) {
                    $bank->setPieces($bank->getPieces()-20);
                    $em->flush();
                }
                /*else{
                    //Sinon on redirige vers la page d'achat de pieces 
                    return $this->redirectToRoute('app_achat_piece');
                }*/
            }
            //On redirige vers la page d'affichage d'article
            return $this->redirectToRoute('app_article_show',[
                'id_article' => $id_article 
            ]);

        }/*Fin du if $form->isSubmitted() && $form->isValid()*/


        return $this->render('compte_user/creer_article_2.html.twig',[
            'user' => $user,
            'bank' => $bank,
            'imageArticle' => $imageArticle,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/compte_user/show_edit/{id_article<[0-9]+>}", name="app_article_show")
     */
    public function show_edit(Request $request,ImageArticleRepository $imgRepo,ArticleRepository $articleRepo,int $id_article,BanqueRepository $bankRepo,EntityManagerInterface $em): Response
    { 
        $user=$this->getUser();
        //On recupere l'article
        $article=$articleRepo->find($id_article);
        $imageArticles=$article->getImageArticles();
        $form=$this->createFormBuilder([
            'Nom_article' => $article->getNomArticle(),
            'Description' => $article->getDescription(),
            'Lieu_de_Vente' => $article->getLieuVente(),
            'Prix_article' => $article->getPrice()
        ])
            ->add('Nom_article',TextType::class,[
                'constraints' => new NotBlank(['message' => 'Le nom ne de votre article ne doit pas être vide']) 
            ])

           ->add('Description',TextareaType::class,[  
                'constraints' => new NotBlank(['message' => 'La description ne doit pas être vide'])
            ])
            ->add('Lieu_de_Vente',TextareaType::class,[
                'constraints' => new NotBlank(['message' => 'Le Lieu de vente ne doit pas être vide'])
            ])
            ->add('Prix_article',TextType::class)

            ->add('Nombre_etoiles',IntegerType::class)

            ->add('image', FileType::class,[
                    'label' => false,
                    'multiple' => true,
                    'mapped' => false,
                    'required' => false
                ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }
        return $this->render('compte_user/show_edit.html.twig',[
            'user' => $user,
            'imageArticles' => $imageArticles,
            'article' => $article,
            'form' => $form->createView()
        ]);
    }


    /**
    *@Route("/compte_user/store", name="app_store")
    */
    public function creer_vitrine(Request $request,EntityManagerInterface $em):Response
    {
        $user=$this->getUser();
        $form=$this->createForm(UserEditFormType::class,$user,[
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //$em->flush();

            //$this->addFlash('success','Mise a jour effectue avec succes !');


            return $this->redirectToRoute('app_admin_user');
        }

          return $this->render('compte_user/store.html.twig',[
                                'user' => $user,
                                'form' => $form->createView()
                            ]);
    }

}
