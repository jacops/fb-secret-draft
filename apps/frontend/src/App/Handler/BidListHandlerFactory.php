<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;

class BidListHandlerFactory
{
    public function __invoke(ContainerInterface $container) : BidListHandler
    {
        return new BidListHandler(
            $container->get(AdapterInterface::class),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
