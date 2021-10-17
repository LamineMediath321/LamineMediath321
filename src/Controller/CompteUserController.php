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
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Repository\ImageArticleRepository;
use App\Repository\BanqueRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\StoreType;
use App\Entity\Store;
use App\Repository\StoreRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

//Pour les acces on peut aussi utiliser des voters


/**
*@IsGranted("ROLE_USER")
*/

class CompteUserController extends AbstractController
{
    /**
     * @Route("/compte_user/", name="app_admin_user")
     */
    public function profil(BanqueRepository $bankRepo,StoreRepository $storeRepo): Response
    {

    	$user=$this->getUser();
        
         //On recupere son compte bancaire
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
        ]);

        //On recupere son store s'il a un store
        $store=$storeRepo->findOneBy([
            'user' => $user->getId()
        ]);

        return $this->render('compte_user/admin_user.html.twig', [
            'user' => $user,
            'store' => $store,
            'bank' => $bank
        ]);
    }


    /**
    *@Route("/compte_user/edit_user", name="app_user_edit",methods={"GET","POST","PUT"})
    *@Security("user.isVerified()",message="Veillez verifier votre email",)
    *@IsGranted("IS_AUTHENTICATED_FULLY")
    */
    public function edit_user(Request $request,EntityManagerInterface $em,BanqueRepository $bankRepo,StoreRepository $storeRepo):Response
    {
        $user=$this->getUser();
         //On recupere son compte bancaire
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
        ]);
        //On recupere son store s'il a un store
        $store=$storeRepo->findOneBy([
            'user' => $user->getId()
        ]);
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
                                'bank' => $bank,
                                'store' => $store,
                                'monForm' => $form->createView()
                            ]);
    }


    /**
    *@Route("/compte_user/creer_article", name="app_article",methods={"GET","POST","PUT"})
    *@Security("user.isVerified()",message="Veillez verifier votre email")
    *@IsGranted("IS_AUTHENTICATED_FULLY")
    */
    public function creer_article(Request $request,EntityManagerInterface $em,BanqueRepository $bankRepo,StoreRepository $storeRepo):Response
    {
        $user=$this->getUser();
          //On recupere son compte bancaire
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
        ]);
        //On recupere son store s'il a un store
        $store=$storeRepo->findOneBy([
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
                 'constraints' => new NotBlank(['message' => 'Veillez choisir une categorie']) 
            ])

            ->add('Sous_Categorie',EntityType::class,[
                'placeholder' => 'Choisissez une sous categorie',
                'disabled' => false,
                'class' => SousCategorie::class,
                'choice_label' => 'nomCategorie',
                'query_builder' => function(SousCategorieRepository $sousCateRepo){
                    return $sousCateRepo->createQueryBuilder('s')->orderBy('s.nomCategorie','ASC');
                },
                 'constraints' => new NotBlank(['message' => 'Veillez choisir une sous categorie']) 
            ])

            ->add('Nom_article',TextType::class,[
                'constraints' => new NotBlank(['message' => 'Le nom de votre article ne doit pas être vide']) 
            ])

           ->add('Description',TextareaType::class,[
                'constraints' => new NotBlank(['message' => 'La description ne doit pas être vide'])
            ])
            ->add('Lieu_de_Vente',TextareaType::class,[
                'constraints' => new NotBlank(['message' => 'Le Lieu de vente ne doit pas être vide'])
            ])

            ->add('Prix_article',TextType::class)

            ->add('Nombre_etoiles',IntegerType::class,[
                'label' => 'Nombre etoiles [1 & 5]',
                'constraints' => [
                    new GreaterThan([
                            'value' => 0,
                            'message' => 'Vous devez avoir un moins 1 etoile',
                        ]),
                    new LessThan([
                            'value' => 6,
                            'message' => '5 etoiles au maximum',
                        ]),
                    ]
            ])

            ->add('image_1', FileType::class,[
                'multiple' => false,
                'mapped' => false,
                'required' => true,
                'constraints' => new NotBlank(['message' => 'Veillez soummettre La photo principale'])
            ])
            ->add('image_2', FileType::class,[
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('image_3', FileType::class,[
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('image_4', FileType::class,[
                'multiple' => false,
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
            $article->setSousCategorie($form->get('Sous_Categorie')->getData());
            $article->setPrice($form->get('Prix_article')->getData());
            $article->setEtoiles($form->get('Nombre_etoiles')->getData());
            //Par default l'article n'est pas paye, il sera true lorsqu'on retire la somme de son compte 
            $article->setEstPaye(false);

            for ($i=1; $i <=4 ; $i++) { 
                // On récupère les images transmises
                $image = $form->get('image_'.$i)->getData();
                
                if ($image!=null) {
                 // On génère un nouveau nom de fichier
                    $fichier = $image->getClientOriginalName();
                // On copie le fichier dans le dossier uploads
                    $image->move(
                        $this->getParameter('images_directory'),
                            $fichier
                    );
                    // On crée l'image dans la base de données
                    $img = new ImageArticle();
                    $img->setImageName($fichier);
                    $img->setNumImage($i);
                    $img->setArticle($article);
                    $em->persist($img);
                    $article->addImageArticle($img);
                }         
            }
           
            $em->persist($article);
            $article->setUser($this->getUser());
            $em->flush();
            return $this->redirectToRoute('app_article_2',[
                'id' => $article->getId(),
            ]);
        }

        return $this->render('compte_user/creer_article.html.twig',[
            'user' => $user,
            'bank' => $bank,
            'store' => $store,
            'form' => $form->createView()
        ]);

    }


    /**
     * @Route("/compte_user/creer_article_2/{id<[0-9]+>}", name="app_article_2")
     * @Security("is_granted('ARTICLE_MANAGE',article)")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function creer_article2(Request $request,Article $article,BanqueRepository $bankRepo,EntityManagerInterface $em,StoreRepository $storeRepo): Response
    {
        //Il va falloir revoir cette methode car elle n'est pas complet
        $user=$this->getUser();
        //On recupere son compte bancaire
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
        ]);
        //On recupere son store s'il a un store
        $store=$storeRepo->findOneBy([
            'user' => $user->getId()
        ]);

        $imageArticles=$article->getImageArticles();
        $form=$this->createFormBuilder()
            ->add('standard',CheckboxType::class)
            ->add('vip',CheckboxType::class)
            ->add('vip_premium',CheckboxType::class)
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             
            //On regarde la ou il a coche 
            if ($form->get('standard')->getData()===true) {
               //On verifie si il possible d'effectuer l'operation
                if ($bank->getPieces()>=10) {
                    $article->setChoixVisbilite("standard");
                    $em->flush();
                }
                /*else{
                    //Sinon on redirige vers la page d'achat de pieces 
                    return $this->redirectToRoute('app_achat_piece');
                }*/
            }
            elseif ($form->get('vip')->getData()===true) {
                if ($bank->getPieces()>=20) {
                    $article->setChoixVisbilite("vip");
                    $em->flush();
                }
                /*else{
                    //Sinon on redirige vers la page d'achat de pieces 
                    return $this->redirectToRoute('app_achat_piece');
                }*/
            }
            else{
                 if ($bank->getPieces()>=30) {
                    $article->setChoixVisbilite("vip_premium");
                    $em->flush();
                }
                /*else{
                    //Sinon on redirige vers la page d'achat de pieces 
                    return $this->redirectToRoute('app_achat_piece');
                }*/
            }
            /*Pour eviter des choix multiples*/
            $a=$form->get('standard')->getData();
            $b=$form->get('vip')->getData();
            $c=$form->get('vip_premium')->getData();
            if (($a&&$b&&$c)||($a&&$b)||($a&&$c)||($b&&$c)) {
                $this->addFlash('danger', 'Veillez cocher une seule des offres ci-dessous');
                return $this->redirectToRoute('app_article_2',[
                    'id' => $article->getId(),
                ]);
            }
            $route=$request->attributes->get('_route');
            //On redirige vers la page d'affichage d'article
            return $this->redirectToRoute('app_article_show',[
                'id' => $article->getId(),
                'route' => $route
            ]);

        }/*Fin du if $form->isSubmitted() && $form->isValid()*/


        return $this->render('compte_user/creer_article_2.html.twig',[
            'user' => $user,
            'bank' => $bank,
            'store' => $store,
            'imageArticles' => $imageArticles,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/compte_user/show_edit/{id<[0-9]+>}", name="app_article_show")
     * @Security("is_granted('ARTICLE_MANAGE',article)")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function show_edit(Request $request,Article $article,BanqueRepository $bankRepo,EntityManagerInterface $em,StoreRepository $storeRepo): Response
    { 
        $user=$this->getUser();
        //On recupere son compte bancaire
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
        ]);
        //On recupere son store s'il a un store
        $store=$storeRepo->findOneBy([
            'user' => $user->getId()
        ]);

        $form=$this->createFormBuilder([
            'Nom_article' => $article->getNomArticle(),
            'Description' => $article->getDescription(),
            'Lieu_de_Vente' => $article->getLieuVente(),
            'Prix_article' => $article->getPrice(),
            'Nombre_etoiles' => $article->getEtoiles()
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
                'constraints' => [
                    new GreaterThan([
                            'value' => 0,
                            'message' => 'Vous devez avoir un moins 1 etoile',
                        ]),
                    new LessThan([
                            'value' => 6,
                            'message' => '5 etoiles au maximum',
                        ]),
                    ]
            ])

            ->add('image', FileType::class,[
                    'label' => 'Ajouter une image si vous avez moins de 4 images',
                    'multiple' => false,
                    'mapped' => false,
                    'required' => false
                ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Il va falloir essayer de sauvegarder les modifications du formulaire
            $article->setNomArticle($form->get('Nom_article')->getData());
            $article->setDescription($form->get('Description')->getData());
            $article->setLieuVente($form->get('Lieu_de_Vente')->getData());
            $article->setDescription($form->get('Description')->getData());
            $article->setPrice($form->get('Prix_article')->getData());
            $article->setEtoiles($form->get('Nombre_etoiles')->getData());
            //Par default l'article n'est pas paye, il sera true lorsqu'on retire la somme de son compte 
                
            // On récupère l'image transmise
            $image = $form->get('image')->getData();
                
              
            if ($image!=null) {
                 // On génère un nouveau nom de fichier
                $fichier = $image->getClientOriginalName();
                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                        $fichier
                );
                // On crée l'image dans la base de données
                $img = new ImageArticle();
                $img->setImageName($fichier);
                $img->setArticle($article);
                $em->persist($img);
                $article->addImageArticle($img);
            }
            
           
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('app_article_show',[
                'id' => $article->getId()
            ]);
        }

        $images=$article->getImageArticles();
        $img=$images[0];
       
        return $this->render('compte_user/show_edit.html.twig',[
            'user' => $user,
            'article' => $article,
            'img' => $img,
            'form' => $form->createView()
        ]);
    }


/**
 * @Route("/compte_user/delete/{id<[0-9]+>}", name="app_delete_image", methods={"DELETE"})
 * @Security("user.isVerified()",message="Veillez verifier votre email")
 *@IsGranted("IS_AUTHENTICATED_FULLY")
 */
public function delete_image(ImageArticle $image, Request $request,EntityManagerInterface $em){

    $data = json_decode($request->getContent(), true);

    // On vérifie si le token est valide
    if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
        // On récupère le nom de l'image
        $imageName = $image->getImageName();
        // On supprime le fichier
        unlink($this->getParameter('images_directory').'/'.$imageName);

        // On supprime l'entrée de la base
        $em->remove($image);
        $em->flush();

        // On répond en json
        return new JsonResponse(['success' => 1]);
    }else{
        return new JsonResponse(['error' => 'Token Invalide'], 400);
    }
}

    
    /**
    *@Route("/compte_user/terminer/{id<[0-9]+>}", name="app_terminer")
    * @Security("is_granted('ARTICLE_MANAGE',article)")
    */
    public function finaliser(Request $request,Article $article,BanqueRepository $bankRepo,EntityManagerInterface $em,StoreRepository $storeRepo):Response
    {
        $user=$this->getUser();
        //On recupere son compte bancaire pour finaliser l'operation
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
            ]);
        //On recupere son store s'il a un store
        $store=$storeRepo->findOneBy([
            'user' => $user->getId()
        ]);


        //On retire les pieces de son compte

        $choix=$article->getChoixVisbilite();
        switch ($choix) {
            case 'vip':
            {
                $bank->setPieces($bank->getPieces()-20);
                $article->setEstPaye(true);
                $em->flush();
             }
                break;
            case 'standard':
            {
                $bank->setPieces($bank->getPieces()-10);
                $article->setEstPaye(true);
                $em->flush();
             }
                break;
            case 'vip_premium':
            {
                $bank->setPieces($bank->getPieces()-30);
                $article->setEstPaye(true);
                $em->flush();
             }
                break;
            
            default:
                # code...
                break;
        }


        $this->addFlash('success','Vous avez creer une annonce !');
          return $this->redirectToRoute('app_voir_article');
    }


    /**
    *@Route("/compte_user/store_price", name="app_store_price")
    * @Security("user.isVerified()",message="Veillez verifier votre email")
    */
    public function store_price(Request $request,BanqueRepository $bankRepo,StoreRepository $storeRepo,EntityManagerInterface $em):Response
    {
        $user=$this->getUser();
        //On recupere son compte bancaire pour finaliser l'operation
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
            ]);
        //On recupere son store s'il a un store
        $store=$storeRepo->findOneBy([
            'user' => $user->getId()
        ]);

            return $this->render('compte_user/store_price.html.twig',[
                                'user' => $user,
                                'bank' => $bank,
                                'store' => $store
                            ]);

  
    }


    /**
    *@Route("/compte_user/store", name="app_store")
    * @Security("user.isVerified()",message="Veillez verifier votre email")
    */
    public function creer_vitrine(Request $request,BanqueRepository $bankRepo,EntityManagerInterface $em):Response
    {
        $user=$this->getUser();
        //On recupere son compte bancaire pour finaliser l'operation
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
            ]);

        $store= new Store();

        $form=$this->createForm(StoreType::class,$store,[
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On fera le retrait sur le compte bancaire du user mais apres
            $store->setUser($user);
            $em->persist($store);
            $em->flush();

            $this->addFlash('info','Vous avez creer un store !');
            return $this->redirectToRoute('app_store_edit');
        }

          return $this->render('compte_user/store.html.twig',[
                                'user' => $user,
                                'bank' => $bank,
                                'store' => $store,
                                'form' => $form->createView()
                            ]);
    }


    /**
    *@Route("/compte_user/store_edit/{id<[0-9]+>}", name="app_store_edit")
    * @Security("is_granted('STORE_EDIT',store)")
    */
    public function edit_vitrine(Request $request,BanqueRepository $bankRepo,EntityManagerInterface $em,Store $store):Response
    {
        $user=$this->getUser();
        //On recupere son compte bancaire pour finaliser l'operation
        $bank=$bankRepo->findOneBy([
            'user' => $user->getId()
            ]);

        $form=$this->createForm(StoreType::class,$store,[
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->addFlash('primary','Mise à jour éffectuée avec succès !');
            return $this->redirectToRoute('app_admin_user');
        }

          return $this->render('compte_user/edit_store.html.twig',[
                                'user' => $user,
                                'bank' => $bank,
                                'store' => $store,
                                'form' => $form->createView()
                            ]);
    }



    /**
    *@Route("/compte_user/voir_article", name="app_voir_article")
    * @Security("user.isVerified()",message="Veillez verifier votre email")
    */
    public function voir_article(Request $request,ArticleRepository $articleRepo,EntityManagerInterface $em,PaginatorInterface $paginator):Response
    {
        //Pour consulter les articles du user en connecte
        $user=$this->getUser();

        //Je recupere les articles du user courant 
        $donnees=$articleRepo->findBy([
            'user' => $user->getId(),
            'estPaye' => true
            ],
            ['createdAt' => 'DESC']);

        //La pagination
        $articles = $paginator->paginate(
            $donnees, //Les donnees
            $request->query->getInt('page',1), //Current page or default page 1
            9 //Le nombre d'articles / page 
        );


        return $this->render('compte_user/voir_article.html.twig',[
                                'user' => $user,
                                'articles' => $articles
                            ]);
    }



}
