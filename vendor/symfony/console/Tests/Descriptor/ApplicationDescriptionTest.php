<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EbayVendor\Symfony\Component\Console\Tests\Descriptor;

use EbayVendor\PHPUnit\Framework\TestCase;
use EbayVendor\Symfony\Component\Console\Application;
use EbayVendor\Symfony\Component\Console\Command\Command;
use EbayVendor\Symfony\Component\Console\Descriptor\ApplicationDescription;
final class ApplicationDescriptionTest extends TestCase
{
    /**
     * @dataProvider getNamespacesProvider
     */
    public function testGetNamespaces(array $expected, array $names)
    {
        $application = new TestApplication();
        foreach ($names as $name) {
            $application->add(new Command($name));
        }
        $this->assertSame($expected, \array_keys((new ApplicationDescription($application))->getNamespaces()));
    }
    public function getNamespacesProvider()
    {
        return [[['_global'], ['foobar']], [['a', 'b'], ['b:foo', 'a:foo', 'b:bar']], [['_global', 'b', 'z', 22, 33], ['z:foo', '1', '33:foo', 'b:foo', '22:foo:bar']]];
    }
}
final class TestApplication extends Application
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return [];
    }
}
