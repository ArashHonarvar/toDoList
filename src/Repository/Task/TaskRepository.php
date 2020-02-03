<?php

namespace App\Repository\Task;

use App\Entity\Task\Task;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByUser(User $user, $isQuery = false)
    {
        $query = $this->createQueryBuilder('task')
            ->select('task')
            ->where('task.createdBy = :createdBy')
            ->andWhere('task.isDeleted = FALSE')
            ->setParameter('createdBy', $user)
            ->getQuery();
        if ($isQuery == true) {
            return $query;
        } else {
            return $query->getResult();
        }
    }

    /**
     * @param $taskId
     * @return Task
     */
    public function findNotDeleted($taskId)
    {
        $query = $this->createQueryBuilder('task')
            ->select('task')
            ->where('task.id = :taskId')
            ->andWhere('task.isDeleted = FALSE')
            ->setParameter('taskId', $taskId)
            ->getQuery();
        return $query->setMaxResults(1)->getOneOrNullResult();
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
