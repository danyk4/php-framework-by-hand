<?php

namespace App\Controllers;

use App\Forms\User\RegisterForm;
use App\Services\UserService;
use danyk\Framework\Authentication\SessionAuthInterface;
use danyk\Framework\Controller\AbstractController;
use danyk\Framework\Http\RedirectResponse;
use danyk\Framework\Http\Response;

class RegisterController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private SessionAuthInterface $auth
    ) {
    }

    public function form(): Response
    {
        return $this->render('register.html.twig');
    }

    public function register()
    {
        // 1. Make form model for:
        $form = new RegisterForm($this->userService);

        $form->setFields(
            $this->request->input('email'),
            $this->request->input('password'),
            $this->request->input('password_confirmation'),
            $this->request->input('name')

        );

        // 2. Validation
        // If errors, add session and redirect to form
        if ($form->hasValidationErrors()) {
            foreach ($form->getValidationErrors() as $error) {
                $this->request->getSession()->setFlash('error', $error);
            }

            return new RedirectResponse('/register');
        }

        // 3. Register User with $form->save()
        $user = $form->save();

        // 4. Add message about successful registration
        $this->request->getSession()->setFlash('success', "User {$user->getEmail()} successfully registered");


        // 5. Login as user
        $this->auth->login($user);

        // 6. Redirect to page
        return new RedirectResponse('/dashboard');
    }
}
