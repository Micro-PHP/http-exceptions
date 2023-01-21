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

namespace Micro\Plugin\Http;

use Micro\Component\DependencyInjection\Container;
use Micro\Framework\Kernel\Plugin\ConfigurableInterface;
use Micro\Framework\Kernel\Plugin\DependencyProviderInterface;
use Micro\Framework\Kernel\Plugin\PluginConfigurationTrait;
use Micro\Framework\Kernel\Plugin\PluginDependedInterface;
use Micro\Plugin\Http\Business\Executor\HttpExceptionExecutorDecoratorFactory;
use Micro\Plugin\Http\Business\Executor\RouteExecutorFactoryInterface;
use Micro\Plugin\Http\Configuration\HttpExceptionResponsePluginConfigurationInterface;
use Micro\Plugin\Http\Decorator\ExceptionResponseBuilderDecorator;
use Micro\Plugin\Http\Facade\HttpFacadeInterface;

/**
 * @author Stanislau Komar <head.trackingsoft@gmail.com>
 *
 * @method HttpExceptionResponsePluginConfigurationInterface configuration()
 */
class HttpExceptionResponsePlugin implements DependencyProviderInterface, PluginDependedInterface, ConfigurableInterface
{
    use PluginConfigurationTrait;

    private HttpFacadeInterface $httpFacade;

    public function provideDependencies(Container $container): void
    {
        $container->decorate(HttpFacadeInterface::class, function (
            HttpFacadeInterface $httpFacade
        ): HttpFacadeInterface {
            $this->httpFacade = $httpFacade;

            return $this->createDecorator();
        }, $this->configuration()->getDecoratedLevel());
    }

    protected function createDecorator(): HttpFacadeInterface
    {
        return new ExceptionResponseBuilderDecorator(
            $this->httpFacade,
            $this->createHttpExceptionExecutorDecoratorFactory()
        );
    }

    protected function createHttpExceptionExecutorDecoratorFactory(): RouteExecutorFactoryInterface
    {
        return new HttpExceptionExecutorDecoratorFactory($this->httpFacade);
    }

    public function getDependedPlugins(): iterable
    {
        return [
            HttpCorePlugin::class,
        ];
    }
}
