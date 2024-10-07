<?php

namespace App\Controllers;

use App\Entites\Post;
use App\Services\PostService;
use danyk\Framework\Controller\AbstractController;
use danyk\Framework\Http\RedirectResponse;
use danyk\Framework\Http\Request;
use danyk\Framework\Http\Response;
use danyk\Framework\Session\SessionInterface;

class PostController extends AbstractController
{
    public function __construct(
        private PostService $postService,
    ) {
    }

    public function show(int $id): Response
    {
        $post = $this->postService->findOrFail($id);

        return $this->render('posts.html.twig', [
            'post' => $post,
        ]);
    }

    public function store()
    {
        $post = Post::create(
            $this->request->input('title'),
            $this->request->input('body'),
        );

        // PostService->save()
        $post = $this->postService->save($post);

        // PostService->find($id)

        $this->request->getSession()->setFlash('success', 'Post was created');

        return new RedirectResponse("/posts/{$post->getId()}");
    }

    public function create(): Response
    {
        return $this->render('create_post.html.twig');
    }

}
