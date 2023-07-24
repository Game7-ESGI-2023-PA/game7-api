<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class GameLobbyRepository extends DocumentRepository
{
    /**
     * @throws MongoDBException
     */
    public function getAllMyLobbies(UserInterface $me) {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->addOr(
                $queryBuilder->expr()->field('master')->references($me),
                $queryBuilder->expr()->field('players')->references($me)
            )
            ->getQuery()
            ->execute();
    }
}
