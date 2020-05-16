<?php

/*
 * autoregistration extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2020, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    MIT
 * @link       http://github.com/terminal42/contao-autoregistration
 */

namespace Terminal42\AutoRegistrationBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Terminal42\AutoRegistrationBundle\Terminal42AutoRegistrationBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(Terminal42AutoRegistrationBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['autoregistration']),
        ];
    }
}
