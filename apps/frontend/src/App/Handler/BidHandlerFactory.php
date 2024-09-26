<?php

declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class BidHandlerFactory
{
    public function __invoke(ContainerInterface $container) : BidHandler
    {
        return new BidHandler($container->get(TemplateRendererInterface::class));
    }
}
