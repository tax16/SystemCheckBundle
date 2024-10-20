<?php

namespace Tax16\SystemCheckBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tax16\SystemCheckBundle\Services\Health\HealthCheckHandler;

class SystemCheckController extends AbstractController
{
    private HealthCheckHandler $healthCheckHandler;

    public function __construct(HealthCheckHandler $healthCheckHandler)
    {
        $this->healthCheckHandler = $healthCheckHandler;
    }

    public function index(): Response
    {
        $categorizedResults = $this->healthCheckHandler->getHealthCheckDashboard();
        $networkData = $this->healthCheckHandler->getNodeSystem();

        return $this->render('@SystemCheckBundle/default/index.html.twig', [
            'successChecks' => $categorizedResults->getSuccessChecks(),
            'failedChecks' => $categorizedResults->getFailedChecks(),
            'warningChecks' => $categorizedResults->getWarningChecks(),
            'totalChecks' => $categorizedResults->getServiceCount(),
            'networkData' => json_encode($networkData->toArray()),
        ]);
    }

    public function details(): Response
    {
        $resultCheck = $this->healthCheckHandler->getHealthCheckResult();

        return $this->render('@SystemCheckBundle/default/view-all.html.twig', [
            'resultCheck' => $resultCheck,
            'totalChecks' => count($resultCheck),
        ]);
    }

    public function network(): Response
    {
        $networkData = $this->healthCheckHandler->getNodeSystem();

        return $this->render('@SystemCheckBundle/default/network.html.twig', [
            'networkData' => json_encode($networkData->toArray()),
            'totalChecks' => count($networkData->getEdges()),
        ]);
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
