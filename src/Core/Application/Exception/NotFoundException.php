<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Application\Exception;

class NotFoundException extends \LogicException
{
    public function __construct(
        string $message,
        int $code = 404,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}
