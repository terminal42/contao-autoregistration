<?php

declare(strict_types=1);

namespace Terminal42\AutoRegistrationBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Terminal42\AutoRegistrationBundle\Terminal42AutoRegistrationBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(Terminal42AutoRegistrationBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, 'notification_center'])
                ->setReplace(['autoregistration']),
        ];
    }
}
