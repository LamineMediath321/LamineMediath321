<?php

namespace App\Repository;

use App\Entity\ArticleLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleLike[]    findAll()
 * @method ArticleLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleLike::class);
    }


    /**
     * Permet de retouner le nombre de vues total par user
     */
    public function findByLikesTotalByUser($user)
    {
        $query = $this->createQueryBuilder('a');
            $query->select("COUNT(a) as LIKES");
            $query->where('a.user = :id');
            $query->setParameter('id',$user);
            $query->orderBy('a.createdAt', 'ASC');
        
        return $query->getQuery()->getResult();
    }
    
    
    /**
     * Permet de retouner le nombre de likes par mois du user
     */
    public function findByLikesByArticle($user)
    {
        $query = $this->createQueryBuilder('al');
            $query->select("COUNT(al) as LIKES, MONTHNAME(al.createdAt) AS MOIS ");
            $query->leftJoin('al.article', 'a');
            $query->where('a.user = :user');
            $query->setParameter('user',$user);
            $query->groupBy('MOIS');
            $query->orderBy('al.createdAt', 'ASC');
        
        return $query->getQuery()->getResult();
    }
}
