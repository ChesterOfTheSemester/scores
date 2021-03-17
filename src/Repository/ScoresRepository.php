<?php

namespace App\Repository;

use App\Entity\Scores;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Scores|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scores|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scores[]    findAll()
 * @method Scores[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scores::class);
    }

    public $maxResults = 100;

    public function show($offset=0, $order='score', $orderBy='DESC', $filter='', $request)
    {
        $q = $this->createQueryBuilder('a')
            ->andWhere($request->getSession()->get('logged_in') ? '1=1' : 'a.verified = TRUE')
            ->andWhere($filter ? "a.name LIKE :filter" : "1=1 OR a.name LIKE :filter")
            ->orderBy($order ? "a.{$order}" : 'a.score', $orderBy==='ASC'?'ASC':'DESC')
                ->setParameter('filter', "%{$filter}%")
            ->setFirstResult($offset)
            ->setMaxResults($this->maxResults)
            ->getQuery()
            ->getArrayResult();

        return $q;
    }

    public function submitScore($name, $difficulty, $score)
    {
        $entityManager = $this->getEntityManager();

        $newScore = new Scores();
        $newScore->setName($name);
        $newScore->setDifficulty($difficulty);
        $newScore->setScore($score);
        $newScore->setVerified(false);

        $entityManager->persist($newScore);
        $entityManager->flush();

        return true;
    }

    public function verifyScore($id, $state)
    {
        $entityManager = $this->getEntityManager();
        $score = $entityManager->find(Scores::class, $id);
        $score->setVerified($state);
        $entityManager->flush();
    }
}
