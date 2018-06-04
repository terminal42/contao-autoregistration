<?php

namespace Terminal42\ContaoAutoRegistrationBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Terminal42\ContaoAutoRegistrationBundle\Terminal42ContaoAutoRegistrationBundle;

/**
 * Contao Manager plugin.
 */
class Plugin implements BundlePluginInterface
{

    /**
     * Gets a list of autoload configurations for this bundle.
     *
     * @param ParserInterface $parser
     *
     * @return ConfigInterface[]
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(Terminal42ContaoAutoRegistrationBundle::class)
                ->setLoadAfter(
                    [
                        ContaoCoreBundle::class
                    ]
                )
        ];
    }
}
