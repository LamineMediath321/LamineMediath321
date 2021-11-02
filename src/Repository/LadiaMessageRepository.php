<?php

namespace App\Repository;

use App\Entity\LadiaMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LadiaMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method LadiaMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method LadiaMessage[]    findAll()
 * @method LadiaMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LadiaMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LadiaMessage::class);
    }

 
    public function countAllMessage()
    {
        $query = $this->createQueryBuilder('l');
        $query->select('COUNT(l.id) as value');
        $query->where('l.estLu = false');
        return $query->getQuery()->getOneOrNullResult();
    }
    

    /*
    public function findOneBySomeField($value): ?LadiaMessage
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
