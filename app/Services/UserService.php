<?php

namespace App\Services;

use App\Entites\User;
use danyk\Framework\Authentication\AuthUserInterface;
use danyk\Framework\Authentication\UserServiceInterface;
use danyk\Framework\Dbal\EntityService;
use Doctrine\DBAL\Connection;

class UserService implements UserServiceInterface
{
    public function __construct(
        private EntityService $service
    ) {
    }

    public function save(User $user): User
    {
        $queryBuilder = $this->service->getConnection()->createQueryBuilder();

        $queryBuilder
            ->insert('users')
            ->values([
                'name'       => ':name',
                'email'      => ':email',
                'password'   => ':password',
                'created_at' => ':created_at',
            ])
            ->setParameters([
                'name'       => $user->getName(),
                'email'      => $user->getEmail(),
                'password'   => $user->getPassword(),
                'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
            ->executeQuery();

        $id = $this->service->save($user);

        //$user->setId($id);

        return $user;
    }

    public function findByEmail(string $email): ?AuthUserInterface
    {
        $queryBuilder = $this->service->getConnection()->createQueryBuilder();

        $result = $queryBuilder
            ->select('*')
            ->from('users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->executeQuery();


        $user = $result->fetchAssociative();

        if ( ! $user) {
            return null;
        }


        return User::create(
            email: $user['email'],
            password: $user['password'],
            createdAt: new \DateTimeImmutable($user['created_at']),
            name: $user['name'],
            id: $user['id']
        );
    }
}
