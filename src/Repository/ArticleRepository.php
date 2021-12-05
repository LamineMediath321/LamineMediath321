<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\SousCategorie;


/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

   
    public function findBySimilaire(SousCategorie $sousCategorie,int $id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.sousCategorie = :sousCategorie')
            ->setParameter('sousCategorie', $sousCategorie)
            ->andWhere('a.id != :id')
            ->setParameter('id', $id)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult()
        ;
    }

    //Faire la recherche par nomArticle ou par description
    public function chercherArticle($mots)
    {
        $query=$this->createQueryBuilder('a');
            $query->where('a.estPaye = true');
            if ($mots != null) {
                $query->andWhere('MATCH_AGAINST(a.nomArticle,a.description) AGAINST(:mots boolean)>0')
                    ->setParameter('mots',$mots);
            }
           
        return $query->getQuery()->getResult();
    }

    /*public function findByCategorie(int $id)
    {
        $em=$this->getEntityManager();
        $query=$em->getConnection()->prepare(
            'SELECT *
            FROM articles a, sous_categorie s, categories c
            WHERE sous_categorie_id = s.id
            AND categorie_id = c.id
            AND c.id ='.$id
        );
        $query->execute();
        return $query->fetchAll();
    }*/

    public function findByCategorie(int $id) 
    {
        $query = $this->createQueryBuilder('a');
                $query->where('a.estPaye=true');
                    $query->leftJoin('a.sousCategorie', 's');
                    $query->leftJoin('s.categorie', 'c');
                    $query->andWhere('c.id = :id');
                    $query->setParameter('id', $id);
 
        return $query->getQuery()->getResult();

    }
    /**
     * Permet de retouner le nombre d'article creer par mois par user
     */
    public function findByArticlesByUser($user)
    {
        $query = $this->createQueryBuilder('a');
            $query->select("COUNT(a) as COUNT, MONTHNAME(a.createdAt) AS MOIS");
            $query->where('a.user = :id');
            $query->setParameter('id',$user);
            $query->groupBy('MOIS');
            $query->orderBy('a.createdAt', 'ASC');
        
        return $query->getQuery()->getResult();
    }

    /**
     * Permet de retouner le nombre de vues par mois par user
     */
    public function findByVuesByUser($user)
    {
        $query = $this->createQueryBuilder('a');
            $query->select("SUM(a.nbVues) as VUES, MONTHNAME(a.createdAt) AS MOIS ");
            $query->where('a.user = :id');
            $query->setParameter('id',$user);
            $query->groupBy('MOIS');
            $query->orderBy('a.createdAt', 'ASC');
        
        return $query->getQuery()->getResult();
    }
    /*
    Permet de retourner le nolbre de vues des annonces existant
     */

    public function findByVuesTotalByUser($user)
    {
        $query = $this->createQueryBuilder('a');
            $query->select("SUM(a.nbVues) as VUES");
            $query->where('a.user = :id');
            $query->setParameter('id',$user);
            $query->orderBy('a.createdAt', 'ASC');
        
        return $query->getQuery()->getResult();
    }

    


}
