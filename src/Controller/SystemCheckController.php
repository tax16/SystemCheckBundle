<?php

namespace Tax16\SystemCheckBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tax16\SystemCheckBundle\Services\Health\HealthCheckManager;

class SystemCheckController extends AbstractController
{
    private HealthCheckManager $checkManager;

    public function __construct(HealthCheckManager $checkManager)
    {
        $this->checkManager = $checkManager;
    }

    public function ping(): Response
    {
        $this->checkManager->performChecks();
        return $this->render('@SystemCheckBundle/default/index.html.twig');
    }

    public function checkDashboard(): Response
    {
        return $this->render('@SystemCheckBundle/default/view-all.html.twig');
    }

    public function healthJson(): Response
    {
        return new JsonResponse([
            'status' => 'UP',
        ], status: 200);
    }

    public function healthHtml(): Response
    {
        return new Response('System check html', 200);
    }
}
