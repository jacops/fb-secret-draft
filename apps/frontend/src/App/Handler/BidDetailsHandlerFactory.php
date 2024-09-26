<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;

class BidDetailsHandlerFactory
{
    public function __invoke(ContainerInterface $container) : BidDetailsHandler
    {
        return new BidDetailsHandler(
            $container->get(AdapterInterface::class),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
