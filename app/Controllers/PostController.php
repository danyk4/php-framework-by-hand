<?php

namespace App\Controllers;

use danyk\Framework\Controller\AbstractController;
use danyk\Framework\Http\Response;

class PostController extends AbstractController
{
    public function show(int $id): Response
    {
        return $this->render('posts.html.twig', [
          'postId' => $id,
        ]);
    }

    public function create(): Response
    {
        return $this->render('create_post.html.twig');
    }
}
