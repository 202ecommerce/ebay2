<?php

namespace EbayVendor\PrestaShop\CodingStandards\CsFixer;

use EbayVendor\PhpCsFixer\Config as BaseConfig;
class Config extends BaseConfig
{
    public function __construct($name = 'default')
    {
        parent::__construct('PrestaShop coding standard');
        $this->setRiskyAllowed(\true);
    }
    public function getRules()
    {
        $rules = ['@Symfony' => \true, 'concat_space' => ['spacing' => 'one'], 'cast_spaces' => ['space' => 'single'], 'error_suppression' => ['mute_deprecation_error' => \false, 'noise_remaining_usages' => \false, 'noise_remaining_usages_exclude' => []], 'function_to_constant' => \false, 'no_alias_functions' => \false, 'phpdoc_summary' => \false, 'phpdoc_align' => ['align' => 'left'], 'protected_to_private' => \false, 'psr4' => \false, 'self_accessor' => \false, 'yoda_style' => null, 'non_printable_character' => \true, 'no_superfluous_phpdoc_tags' => \false];
        return $rules;
    }
}
