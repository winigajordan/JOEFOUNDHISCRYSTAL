<?php

namespace App\Repository;

use App\Entity\InvitationsEnvoye;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvitationsEnvoye>
 *
 * @method InvitationsEnvoye|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvitationsEnvoye|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvitationsEnvoye[]    findAll()
 * @method InvitationsEnvoye[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationsEnvoyeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvitationsEnvoye::class);
    }

    public function add(InvitationsEnvoye $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InvitationsEnvoye $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return InvitationsEnvoye[] Returns an array of InvitationsEnvoye objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InvitationsEnvoye
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
