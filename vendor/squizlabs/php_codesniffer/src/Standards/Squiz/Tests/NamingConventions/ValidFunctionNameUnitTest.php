<?php

/**
 * Unit test class for the ValidFunctionName sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace EbayVendor\PHP_CodeSniffer\Standards\Squiz\Tests\NamingConventions;

use EbayVendor\PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;
class ValidFunctionNameUnitTest extends AbstractSniffUnitTest
{
    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getErrorList()
    {
        return [4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1, 11 => 1, 12 => 1, 13 => 1, 14 => 2];
    }
    //end getErrorList()
    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return [];
    }
    //end getWarningList()
}
//end class
