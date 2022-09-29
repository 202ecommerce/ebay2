<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EbayVendor\PhpCsFixer\Tests;

use EbayVendor\LegacyPHPUnit\TestCase as BaseTestCase;
use EbayVendor\PHPUnitGoodPractices\Polyfill\PolyfillTrait;
use EbayVendor\PHPUnitGoodPractices\Traits\ExpectationViaCodeOverAnnotationTrait;
use EbayVendor\PHPUnitGoodPractices\Traits\ExpectOverSetExceptionTrait;
use EbayVendor\PHPUnitGoodPractices\Traits\IdentityOverEqualityTrait;
use EbayVendor\PHPUnitGoodPractices\Traits\ProphecyOverMockObjectTrait;
use EbayVendor\PHPUnitGoodPractices\Traits\ProphesizeOnlyInterfaceTrait;
use EbayVendor\Prophecy\PhpUnit\ProphecyTrait;
use EbayVendor\Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
// we check single, example DEV dependency - if it's there, we have the dev dependencies, if not, we are using PHP-CS-Fixer as library and trying to use internal TestCase...
if (\trait_exists(ProphesizeOnlyInterfaceTrait::class)) {
    if (\trait_exists(ProphecyTrait::class)) {
        /**
         * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
         *
         * @internal
         */
        abstract class InterimTestCase extends BaseTestCase
        {
            use ProphecyTrait;
        }
    } else {
        /**
         * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
         *
         * @internal
         */
        abstract class InterimTestCase extends BaseTestCase
        {
        }
    }
    /**
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     *
     * @internal
     */
    abstract class TestCase extends InterimTestCase
    {
        use ExpectationViaCodeOverAnnotationTrait;
        use ExpectDeprecationTrait;
        use ExpectOverSetExceptionTrait;
        use IdentityOverEqualityTrait;
        use PolyfillTrait;
        use ProphecyOverMockObjectTrait;
        use ProphesizeOnlyInterfaceTrait;
    }
} else {
    /**
     * Version without traits for cases when this class is used as a lib.
     *
     * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     *
     * @internal
     *
     * @todo 3.0 To be removed when we clean up composer prod-autoloader from dev-packages.
     */
    abstract class TestCase extends BaseTestCase
    {
    }
}
