<?php

namespace App\Repository;

use App\Entity\Admins;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Admins|null find($id, $lockMode = null, $lockVersion = null)
 * @method Admins|null findOneBy(array $criteria, array $orderBy = null)
 * @method Admins[]    findAll()
 * @method Admins[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admins::class);
    }

    public function authenticate($request, $username, $password)
    {
        $q = $this->createQueryBuilder('a')
            ->andWhere('a.username = :username')
            ->andWhere('a.password = :password')
            ->setParameter('username', $username)
            ->setParameter('password', $password)
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();

        if (!count($q)) return false;

        $request->getSession()->set('logged_in', true);

        return true;
    }
}
