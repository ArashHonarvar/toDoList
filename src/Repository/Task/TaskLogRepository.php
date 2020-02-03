<?php

namespace App\Repository\Task;

use App\Entity\Task\TaskLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TaskLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskLog[]    findAll()
 * @method TaskLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskLog::class);
    }

    public function findLogsByTaskId($taskId, $isQuery = false)
    {
        $query = $this->createQueryBuilder('task_log')
            ->select('task_log')
            ->join('task_log.task', 'task')
            ->where('task.id = :taskId')
            ->andWhere('task.isDeleted = FALSE')
            ->setParameter('taskId', $taskId)
            ->getQuery();
        if ($isQuery == true) {
            return $query;
        } else {
            return $query->getResult();
        }
    }


    // /**
    //  * @return TaskLog[] Returns an array of TaskLog objects
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
    public function findOneBySomeField($value): ?TaskLog
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
