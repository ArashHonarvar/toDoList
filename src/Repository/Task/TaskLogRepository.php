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
}
