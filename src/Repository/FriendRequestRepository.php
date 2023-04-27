<?php

namespace App\Repository;

use App\Document\User;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class FriendRequestRepository extends DocumentRepository
{
    public function isFriendRequestExisting($from, $to): bool
    {
        $queryBuilder = $this->createQueryBuilder();

        $result = $queryBuilder
            ->addOr(
                $queryBuilder->expr()
                    ->addAnd($queryBuilder->expr()->field('from')->references($from))
                    ->addAnd($queryBuilder->expr()->field('to')->references($to))
            )
            ->addOr(
                $queryBuilder->expr()
                    ->addAnd($queryBuilder->expr()->field('from')->references($to))
                    ->addAnd($queryBuilder->expr()->field('to')->references($from))
            )
            ->addAnd(
                $queryBuilder->expr()
                    ->addOr($queryBuilder->expr()->field('status')->equals('pending'))
                    ->addOr($queryBuilder->expr()->field('status')->equals('accepted'))
            )
            ->getQuery()
            ->getSingleResult();

        return $result !== null;
    }
}
