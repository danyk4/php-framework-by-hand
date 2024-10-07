<?php

namespace App\Services;

use App\Entites\Post;
use danyk\Framework\Dbal\EntityService;
use danyk\Framework\Http\Exceptions\NotFoundException;
use Doctrine\DBAL\Connection;

class PostService
{
    public function __construct(
        private EntityService $service
    ) {
    }

    public function save(Post $post)
    {
        $queryBuilder = $this->service->getConnection()->createQueryBuilder();

        $queryBuilder
            ->insert('posts')
            ->values([
                'title'      => ':title',
                'body'       => ':body',
                'created_at' => ':created_at',
            ])
            ->setParameters([
                'title'      => $post->getTitle(),
                'body'       => $post->getBody(),
                'created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
            ->executeQuery();

        $id = $this->service->save($post);

        //$post->setId($id);

        return $post;
    }

    public function findOrFail(int $id): Post
    {
        $post = $this->find($id);

        if (is_null($post)) {
            throw new NotFoundException("Post $id not found");
        }

        return $post;
    }

    public function find(int $id): ?Post
    {
        $queryBuilder = $this->service->getConnection()->createQueryBuilder();

        $result = $queryBuilder
            ->select('*')
            ->from('posts')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();


        $post = $result->fetchAssociative();

        if ( ! $post) {
            return null;
        }


        return Post::create(
            $post['title'],
            $post['body'],
            $post['id'],
            new \DateTimeImmutable($post['created_at'])
        );
    }

}
