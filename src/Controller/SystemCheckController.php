<?php

namespace  Tax16\SystemCheckBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemCheckController
{
    public function check(): Response
    {
        return new Response('System is healthy', 200);
    }
}
