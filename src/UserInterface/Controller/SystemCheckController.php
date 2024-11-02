<?php

namespace Tax16\SystemCheckBundle\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tax16\SystemCheckBundle\Core\Application\Service\HealthCheckHandler;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

class SystemCheckController extends AbstractController
{

    private $healthCheckHandler;

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


    public function healthJson(Request $request): Response
    {
        $traceId = $request->headers->get('X-Trace-Id', null);

        $appId = $this->getParameter('system_check.id');

        if (!empty($traceId) && $traceId === $appId) {
            $response = new JsonResponse([], Response::HTTP_OK);
            $response->headers->set('X-Trace-Id', $traceId);

            return $response;
        }

        $responseData = array_map(
            static function (HealthCheck $checkDTO) {
                return $checkDTO->toArray();
            },
            $this->healthCheckHandler->getHealthCheckResult()
        );

        return  new JsonResponse($responseData, Response::HTTP_OK);
    }

    public function healthHtml(): Response
    {
        return new Response('System check html', 200);
    }
}
