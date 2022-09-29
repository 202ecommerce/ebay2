<?php

namespace EbayVendor\PhpParser\ErrorHandler;

use EbayVendor\PhpParser\Error;
class ThrowingTest extends \EbayVendor\PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \PhpParser\Error
     * @expectedExceptionMessage Test
     */
    public function testHandleError()
    {
        $errorHandler = new Throwing();
        $errorHandler->handleError(new Error('Test'));
    }
}
