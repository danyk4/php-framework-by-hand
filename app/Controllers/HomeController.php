<?php

namespace App\Controllers;

use App\Services\YouTubeService;
use danyk\Framework\Controller\AbstractController;
use danyk\Framework\Http\Response;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly YouTubeService $youTube,
    ) {}

    public function index(): Response
    {
        //        $db = new PDO('mysql:host=mysql_db;port=3306;dbname=db', 'root', 'root');
        //        dd($db);
        //        $databaseUrl      = 'pdo-mysql://root:root@mysql_db:3306/db?charset=utf8mb4';
        //        $dsnParser        = new DsnParser();
        //        $connectionParams = $dsnParser->parse($databaseUrl);
        //        $conn             = DriverManager::getConnection($connectionParams);
        //
        //        $sql  = "SELECT * FROM posts";
        //        $stmt = $conn->executeQuery($sql);
        //        $row  = $stmt->fetchAssociative();
        //        dd($row);

        return $this->render('home.html.twig', [
            'youTubeChannel' => $this->youTube->getChannelUrl(),
        ]);
    }
}
