<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends DocumentRepository implements UserLoaderInterface
{

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->addOr($queryBuilder->expr()->field('id')->equals($identifier))
            ->addOr($queryBuilder->expr()->field('email')->equals($identifier))
            ->getQuery()
            ->getSingleResult();
    }
}
