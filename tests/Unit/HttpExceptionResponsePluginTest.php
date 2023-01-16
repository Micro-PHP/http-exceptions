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

namespace Micro\Plugin\Http\Test\Unit;

use Micro\Component\DependencyInjection\Container;
use Micro\Framework\Kernel\Configuration\ApplicationConfigurationInterface;
use Micro\Plugin\Http\Decorator\ExceptionResponseBuilderDecorator;
use Micro\Plugin\Http\Facade\HttpFacadeInterface;
use Micro\Plugin\Http\HttpCorePlugin;
use Micro\Plugin\Http\HttpExceptionResponsePlugin;
use Micro\Plugin\Http\HttpExceptionResponsePluginConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @author ChatGPT Jan 9 Version
 */
class HttpExceptionResponsePluginTest extends TestCase
{
    public function testProvideDependencies()
    {
        $containerMock = new Container();
        $containerMock->register(HttpFacadeInterface::class, fn () => $this->createMock(HttpFacadeInterface::class));

        $configMock = new HttpExceptionResponsePluginConfiguration(
            $this->createMock(ApplicationConfigurationInterface::class)
        );

        $plugin = new HttpExceptionResponsePlugin();
        $plugin->setConfiguration($configMock);
        $plugin->provideDependencies($containerMock);

        $this->assertInstanceOf(ExceptionResponseBuilderDecorator::class, $containerMock->get(HttpFacadeInterface::class));
    }

    public function testGetDependedPlugins()
    {
        $plugin = new HttpExceptionResponsePlugin();
        $dependedPlugins = $plugin->getDependedPlugins();

        $this->assertIsArray($dependedPlugins);
        $this->assertContains(HttpCorePlugin::class, $dependedPlugins);
    }
}
