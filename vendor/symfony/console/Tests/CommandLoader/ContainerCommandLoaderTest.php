<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EbayVendor\Symfony\Component\Console\Tests\CommandLoader;

use EbayVendor\PHPUnit\Framework\TestCase;
use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use EbayVendor\Symfony\Component\DependencyInjection\ServiceLocator;
class ContainerCommandLoaderTest extends TestCase
{
    public function testHas()
    {
        $loader = new ContainerCommandLoader(new ServiceLocator(['foo-service' => function () {
            return new Command('foo');
        }, 'bar-service' => function () {
            return new Command('bar');
        }]), ['foo' => 'foo-service', 'bar' => 'bar-service']);
        $this->assertTrue($loader->has('foo'));
        $this->assertTrue($loader->has('bar'));
        $this->assertFalse($loader->has('baz'));
    }
    public function testGet()
    {
        $loader = new ContainerCommandLoader(new ServiceLocator(['foo-service' => function () {
            return new Command('foo');
        }, 'bar-service' => function () {
            return new Command('bar');
        }]), ['foo' => 'foo-service', 'bar' => 'bar-service']);
        $this->assertInstanceOf(Command::class, $loader->get('foo'));
        $this->assertInstanceOf(Command::class, $loader->get('bar'));
    }
    public function testGetUnknownCommandThrows()
    {
        $this->expectException('EbayVendor\\Symfony\\Component\\Console\\Exception\\CommandNotFoundException');
        (new ContainerCommandLoader(new ServiceLocator([]), []))->get('unknown');
    }
    public function testGetCommandNames()
    {
        $loader = new ContainerCommandLoader(new ServiceLocator(['foo-service' => function () {
            return new Command('foo');
        }, 'bar-service' => function () {
            return new Command('bar');
        }]), ['foo' => 'foo-service', 'bar' => 'bar-service']);
        $this->assertSame(['foo', 'bar'], $loader->getNames());
    }
}
