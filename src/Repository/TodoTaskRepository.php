<?php

namespace App\Repository;

use App\Entity\TodoTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TodoTask>
 */
class TodoTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoTask::class);
    }

       /**
        * @return TodoTask[] Returns an array of TodoTask objects
        */
       public function findByOwner(int $id): array
       {
           return $this->createQueryBuilder('task')
               ->andWhere('task.user = :val')
               ->setParameter('val', $id)
               ->orderBy('task.finished')
               ->getQuery()
               ->getResult()
           ;
       }

    //    public function findOneBySomeField($value): ?TodoTask
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
