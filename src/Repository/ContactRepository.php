<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * @return Contact[] Returns an array of Contact objects
     */
    public function paginate(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('c')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Contact[] Returns an array of Contact objects
     */
    public function search(string $search, ?int $page = null, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('c.firstName', ':search'),
                    $qb->expr()->like('c.name', ':search'),
                ),
            )
            ->setParameter('search', '%'.$search.'%');
        
        // Ajouter la pagination si spécifiée
        if ($page !== null && $limit !== null) {
            $offset = ($page - 1) * $limit;
            $qb->setFirstResult($offset)
               ->setMaxResults($limit);
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Compte le nombre de résultats pour une recherche
     */
    public function countSearch(string $search): int
    {
        $qb = $this->createQueryBuilder('c');
        return $qb->select('COUNT(c.id)')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('c.firstName', ':search'),
                    $qb->expr()->like('c.name', ':search'),
                ),
            )
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Recherche ET filtrage par statut avec pagination
     */
    public function searchAndFilter(?string $search, string $status, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;
        $qb = $this->createQueryBuilder('c');

        // Filtrage par recherche
        if ($search) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('c.firstName', ':search'),
                    $qb->expr()->like('c.name', ':search'),
                )
            )->setParameter('search', '%'.$search.'%');
        }

        // Filtrage par statut
        if ($status !== 'all') {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $status);
        }

        return $qb->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les résultats avec recherche ET filtrage par statut
     */
    public function countSearchAndFilter(?string $search, string $status): int
    {
        $qb = $this->createQueryBuilder('c')->select('COUNT(c.id)');

        // Filtrage par recherche
        if ($search) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('c.firstName', ':search'),
                    $qb->expr()->like('c.name', ':search'),
                )
            )->setParameter('search', '%'.$search.'%');
        }

        // Filtrage par statut
        if ($status !== 'all') {
            $qb->andWhere('c.status = :status')
               ->setParameter('status', $status);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
