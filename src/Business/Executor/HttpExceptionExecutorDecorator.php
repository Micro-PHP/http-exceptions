<?php

declare(strict_types=1);

/*
 *  This file is part of the Micro framework package.
 *
 *  (c) Stanislau Komar <kost@micro-php.net>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Micro\Plugin\Http\Business\Executor;

use Micro\Plugin\Http\Exception\HttpException;
use Micro\Plugin\Http\Exception\HttpInternalServerException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Stanislau Komar <head.trackingsoft@gmail.com>
 */
readonly class HttpExceptionExecutorDecorator implements RouteExecutorInterface
{
    public function __construct(
        private RouteExecutorInterface $decorated
    ) {
    }

    public function execute(Request $request, bool $flush = true): Response
    {
        try {
            return $this->decorated->execute($request, $flush);
        } catch (\Throwable $throwable) {
            if (!$flush) {
                throw $throwable;
            }

            if (!($throwable instanceof HttpException)) {
                $throwable = new HttpInternalServerException('Internal Server Error.', $throwable);
            }

            $response = new Response(
                $throwable->getMessage(),
                $throwable->getCode(),
            );

            $response->send();

            return $response;
        }
    }
}