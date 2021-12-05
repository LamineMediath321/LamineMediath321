<?php

namespace App\Repository;

use App\Entity\Store;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Store|null find($id, $lockMode = null, $lockVersion = null)
 * @method Store|null findOneBy(array $criteria, array $orderBy = null)
 * @method Store[]    findAll()
 * @method Store[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Store::class);
    }

    public function findStoreByCategorie(int $id)
    {
        $query = $this->createQueryBuilder('s');
            $query->leftJoin('s.domaine', 'sc');
            $query->leftJoin('sc.categorie','c');
            $query->andWhere('c.id = :id');
            $query->setParameter('id', $id);


          return $query->getQuery()->getResult();

    }

    
    
    /**
     * Permet de retouner le nombre de visites du store par mois par user d 
     */
    public function findByVueStoreByUser($user)
    {
        $query = $this->createQueryBuilder('s');
            $query->select("s.visites as VISITES, MONTHNAME(s.createdAt) AS MOIS ");
            $query->where('s.user = :id');
            $query->setParameter('id',$user);
            $query->groupBy('MOIS');
            $query->orderBy('s.createdAt', 'ASC');
        
        return $query->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Store
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
