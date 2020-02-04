<?php

namespace App\Repository\User;

use App\Entity\User\ApiToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ApiToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiToken[]    findAll()
 * @method ApiToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }

    /**
     * @param $token
     * @return ApiToken|null
     */
    public function findTokenByAccessOrRefreshToken($token)
    {
        return $this->createQueryBuilder('api_token')
            ->select('api_token')
            ->where('api_token.accessToken = :token')
            ->orWhere('api_token.refreshToken = :token')
            ->setParameter('token', $token)
            ->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    public function findTokensByUsername($username, $isQuery = false)
    {
        $query = $this->createQueryBuilder('api_token')
            ->select('api_token')
            ->join('api_token.createdBy', 'user')
            ->where('user.username = :username')
            ->setParameter('username', $username);
        if ($isQuery == true) {
            return $query->getQuery();
        } else {
            return $query->getQuery()->getResult();
        }
    }
}
