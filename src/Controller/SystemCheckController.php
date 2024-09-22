<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemCheckController
{
    /**
     * @Route("/system-check", name="system_check")
     */
    public function check(): Response
    {
        return new Response('System is healthy', 200);
    }
}
