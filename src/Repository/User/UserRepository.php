<?php

namespace App\Repository\User;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * //  * @return User
     * //
     */
    public function findUserByCredentials($credentials)
    {
        return $this->createQueryBuilder('user')
            ->select('user')
            ->join('user.tokens', 'tokens')
            ->where('tokens.accessToken = :accessToken')
            ->orWhere('tokens.refreshToken = :refreshToken')
            ->setParameter('accessToken', $credentials['access-token'])
            ->setParameter('refreshToken', $credentials['refresh-token'])->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * //  * @return User
     * //
     */
    public function findUserByUsernameOrEmail($data)
    {
        return $this->createQueryBuilder('user')
            ->select('user')
            ->where('user.username = :username')
            ->orWhere('user.email = :email')
            ->setParameter('username', $data['username'])
            ->setParameter('email', $data['email'])->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
