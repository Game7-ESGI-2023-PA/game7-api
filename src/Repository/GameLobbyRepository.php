<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class GameLobbyRepository extends DocumentRepository
{
    /**
     * @throws MongoDBException
     */
    public function getAllMyLobbies($email) {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->addOr(
                $queryBuilder->expr()->field('master')->references($email)
            )
            ->addOr(
                $queryBuilder->expr()->field('players')->all([
                    ['\$email' => $email->getUserIdentifier()]
                ])
            )
            ->getQuery()
            ->execute();
    }
}
