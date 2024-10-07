<?php

namespace App\Controllers;

use danyk\Framework\Authentication\SessionAuthInterface;
use danyk\Framework\Controller\AbstractController;
use danyk\Framework\Http\RedirectResponse;
use danyk\Framework\Http\Response;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly SessionAuthInterface $auth,
    ) {
    }

    public function form(): Response
    {
        return $this->render('login.html.twig');
    }

    public function login(): RedirectResponse
    {
        $isAuth = $this->auth->authenticate(
            $this->request->input('email'),
            $this->request->input('password')
        );

        if ( ! $isAuth) {
            $this->request->getSession()->setFlash('error', 'Wrong data');

            return new RedirectResponse('/login');
        }

        $this->request->getSession()->setFlash('success', 'You are now logged in');

        return new RedirectResponse('/dashboard');
    }

    public function logout(): RedirectResponse
    {
        $this->auth->logout();

        return new RedirectResponse('/login');
    }
}
