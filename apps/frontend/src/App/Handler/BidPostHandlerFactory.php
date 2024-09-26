<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;

class BidPostHandlerFactory
{
    public function __invoke(ContainerInterface $container) : BidPostHandler
    {
        return new BidPostHandler(
            $container->get(AdapterInterface::class),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
