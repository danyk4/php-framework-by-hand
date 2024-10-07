<?php

namespace App\Controllers;

use danyk\Framework\Controller\AbstractController;
use danyk\Framework\Http\Response;

class DashboardController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('dashboard.html.twig');
    }
}
