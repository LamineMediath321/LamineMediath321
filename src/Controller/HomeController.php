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
use App\Repository\UserRepository;
use App\Repository\StoreRepository;
use App\Repository\ArticleRepository;
use App\Repository\SousCategorieRepository;
use App\Entity\Article;
use App\Entity\Store;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\ChercherArticleType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\LadiaMessage;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Repository\ArticleLikeRepository;
use App\Entity\ArticleLike;
use MercurySeries\FlashyBundle\FlashyNotifier;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(EntityManagerInterface $em,StoreRepository $storeRepo,ArticleRepository $articleRepo,Request $request): Response
    {

    	$repo=$em->getRepository(Carousel::class);

        $carousels=$repo->findBy([],['createdAt'=>'DESC']);
        //pour les stores
        $stores=$storeRepo->findAll();

        //por les offres vip mais pour l'instant on a findall
        $articles=$articleRepo->findBy([
            'estPaye' => true
            ],
            ['createdAt' => 'DESC']
        );
       

        return $this->render('home/index.html.twig', [
            'carousels' => $carousels,
            'stores' => $stores,
            'articles' => $articles
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
            //$this->addFlash('success','Carousel Successfully created !');

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
    public function edit(Request $request,Carousel $carousel,EntityManagerInterface $em,FlashyNotifier $flashy):Response
    {
        $form=$this->createForm(CarouselType::class,$carousel,[
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $flashy->success('carousel Successfully updated !','carousel Successfully updated !');


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


     /**
    *@Route("/home/{id<[0-9]+>}/article_details", name="app_article_details")
    */
    public function article_details(Article $article,ArticleRepository $articleRepo,EntityManagerInterface $em,Request $request,ArticleLikeRepository $likeRepo):Response
    {

        /*On gere le nombre de vues*/
        $nbVues = $article->getNbVues();
        $nbVues++;
        $article->setNbVues($nbVues);
        /*On gere le nombre d'etoiles*/
        $nbLikes = $likeRepo->count([
            'article' => $article
        ]);
        if (($nbVues>=5 && $nbVues<=10)||($nbLikes>2 && $nbLikes<=5)) {
            $article->setEtoiles(2);   
        }
        if (($nbVues>10 && $nbVues<=15)||($nbLikes>5 && $nbLikes<=10)||($nbVues>=5 && $nbLikes==3)) {
            $article->setEtoiles(3);   
        }
        if (($nbVues>15 && $nbVues<=20)||($nbLikes>10 && $nbLikes<=15)||($nbVues>5 && $nbLikes==5)) {
            $article->setEtoiles(4);   
        }
        if (($nbVues>20)||($nbLikes>15)||($nbVues>10 && $nbLikes>=10)) {
            $article->setEtoiles(5);   
        }
       

        /*Fin*/
        $em->flush();
        /*Fin pour les vues*/
        $imageArticles=$article->getImageArticles();

        //On recupere les articles similaires
        $store=$article->getUser()->getStore();
        $similaires=$articleRepo->findBySimilaire($article->getSousCategorie(),$article->getId());

        $vendeur=$article->getUser();

        //Le formulaire de LadiaMessage
        $form=$this->createFormBuilder([
            'Destinataire' => $vendeur->getFirstName().' '.$vendeur->getLastName(),
        ])
            ->add('Destinataire',TextType::class,[
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Message',TextareaType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Coordonnees',TextareaType::class,[
                'constraints' => new NotBlank(['message' => 'Veillez mettre vos coordonnées']) 
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $message = new LadiaMessage();
            $message->setDestinataire($vendeur);
            $message->setMessage($form->get('Message')->getData());
            $message->setCoordonnees($form->get('Coordonnees')->getData());
            $message->setEstLu(false);
            $message->setArticle($article);
            $vendeur->addLadiaMessage($message);
            $em->persist($message);
            $em->flush();

            $taille = 0;
            foreach ($imageArticles as $image) {
                $taille++;
            }


            $this->addFlash('info' , 'Vous avez envoyé un message à '.$vendeur->getFirstName());
            return $this->render('home/article_details.html.twig',[
            'article' => $article,
            'imageArticles' => $imageArticles,
            'vendeur' => $vendeur,
            'store' => $store,
            'taille' => $taille,
            'similaires' => $similaires,
            'form' => $form->createView()
        ]);
            
        }
        $taille = 0;
        foreach ($imageArticles as $image) {
            $taille++;
        }

        
        return $this->render('home/article_details.html.twig',[
            'article' => $article,
            'imageArticles' => $imageArticles,
            'vendeur' => $vendeur,
            'store' => $store,
            'taille' => $taille,
            'similaires' => $similaires,
            'form' => $form->createView()
        ]);

    }

/**
*@Route("/home/like/{id<[0-9]+>}",name="app_aime_article")
*/
public function article_like(Article $article,EntityManagerInterface $em,ArticleLikeRepository $likeRepo,FlashyNotifier $flashy):Response
{

    $user = $this->getUser();

    if (!$user) {
        return $this->json([
            'code' => 403, 
            'message' => 'Unauthorized'
        ], 403);
    }
    if ($article->isLikedByUser($user)) {
        $like = $likeRepo->findOneBy([
            'article' => $article,
            'user' => $user
        ]);
        $em->remove($like);
        $em->flush();

        return $this->json([
            'code' => 200, 
            'message' => 'Like bien supprimé',
            'likes' => $likeRepo->count(['article' => $article])
        ], 200);
    }

    $like = new ArticleLike();
    $like->setArticle($article);
    $like->setUser($user);

    $em->persist($like);
    $em->flush();

    return $this->json([
        'code' => 200, 
        'message' => 'like bien ajouté',
        'likes' => $likeRepo->count(['article' => $article])
        ], 200);



}



     /**
    *@Route("/home/acheter/{id<[0-9]+>}", name="app_acheter")
    */
    public function acheter(Request $request,PaginatorInterface $paginator,ArticleRepository $articleRepo,int $id):Response
    {
         //Faire des recherches Avec MATCH AGAINTS
        $form = $this->createForm(ChercherArticleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donnees=$articleRepo->chercherArticle($form->get('mots')->getData());
            //La pagination
            $articles = $paginator->paginate(
                $donnees, //Les donnees
                $request->query->getInt('page',1), //Current page or default page 1
                8 //Le nombre d'articles / page 
            );
            //On remet id a 100
            //On se qui concerne le formulaire
            return $this->render('home/acheter.html.twig',[
                'articles' => $articles,
                'form' => $form->createView()
            ]);
        }

        switch ($id) {
            case 100:
                    //Pour tous les articles
                    $donnees=$articleRepo->findBy(['estPaye' => true],['createdAt' => 'DESC']);
                break;
            case 27:
                     $donnees=$articleRepo->findByCategorie(1);
                break;
            case 28:
                    $donnees=$articleRepo->findByCategorie(2);

                break;
            case 29:
                    $donnees=$articleRepo->findByCategorie(3);
                break;
            case 30:
                    $donnees=$articleRepo->findByCategorie(4);
                break;
            case 31:
                    $donnees=$articleRepo->findByCategorie(5);                
                    break;
            case 32:
                    $donnees=$articleRepo->findByCategorie(6);
                break;
            case 33:
                    $donnees=$articleRepo->findByCategorie(7);
                break;
            case 36:
                    $donnees=$articleRepo->findByCategorie(8);
                break;
            
            default:
                //Pour les articles specifiques
                $donnees=$articleRepo->findBy([
                  'sousCategorie' => $id,
                  'estPaye' => true
                ],
                ['createdAt' => 'DESC']);
                break;
        }
        //La pagination
        $articles = $paginator->paginate(
            $donnees, //Les donnees
            $request->query->getInt('page',1), //Current page or default page 1
            8 //Le nombre d'articles / page 
        );
        
        return $this->render('home/acheter.html.twig',[
            'articles' => $articles,
            'form' => $form->createView()
        ]);
    }


    /**
    *@Route("/home/voir_all_store",name="app_voir_all_store")
    */
    public function voir_all_store(Request $request,EntityManagerInterface $em,StoreRepository $storeRepo,PaginatorInterface $paginator):Response
    {
        //Les stores en immobilier
        $immobiliers=$storeRepo->findStoreByCategorie(1);
        //Les stores en electroniques
        $electros=$storeRepo->findStoreByCategorie(2);
           //Les stores en vetements
        $vetements=$storeRepo->findStoreByCategorie(3);
           //Les stores en beaute
        $beautes=$storeRepo->findStoreByCategorie(4);
           //Les stores en sports
        $sports=$storeRepo->findStoreByCategorie(5);
          //Les stores en services
        $services=$storeRepo->findStoreByCategorie(6);
          //Les stores en alimantation
        $alimentations=$storeRepo->findStoreByCategorie(7);
          //Les stores en voitures
        $voitures=$storeRepo->findStoreByCategorie(8);



        return $this->render('home/voir_all_store.html.twig',[
            'immobiliers' => $immobiliers,
            'electros' => $electros,
            'vetements' => $vetements,
            'beautes' => $beautes,
            'sports' => $sports,
            'services' => $services,
            'alimentations' => $alimentations,
            'voitures' => $voitures
        ]);
    }

     /**
    *@Route("/home/{id<[0-9]+>}/voir_store", name="app_voir_store")
    */
    public function voir_store(Store $store,UserRepository $userRepo,ArticleRepository $articleRepo,SousCategorieRepository $sousCatRepo,PaginatorInterface $paginator,Request $request,EntityManagerInterface $em):Response
    {
        /*On gere les visites*/
        $visites = $store->getVisites();
        $visites++;
        $store->setVisites($visites);
        $em->flush();
        /*Fin*/
        //On recupere le proprietaire du store
        $storien=$userRepo->findOneBy([
            'id' => $store->getUser()
        ]);
        //Je recupere son domaine
        $domaine=$sousCatRepo->findOneBy([
            'id' => $store->getDomaine()
        ]);
        //Je recupere ses articles
        $donnees=$articleRepo->findBy([
            'user' => $storien->getId(),
            'estPaye' => true
            ],
            ['createdAt' => 'DESC']);


        //La pagination
        $articles = $paginator->paginate(
            $donnees, //Les donnees
            $request->query->getInt('page',1), //Current page or default page 1
            9 //Le nombre d'articles / page 
        );


        return $this->render('home/voir_store.html.twig',[
            'store' => $store,
            'storien' => $storien,
            'domaine' => $domaine,
            'articles' => $articles 
        ]);

    }




}


