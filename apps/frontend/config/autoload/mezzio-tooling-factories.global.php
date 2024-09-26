<?php

/**
 * This file generated by Mezzio\Tooling\Factory\ConfigInjector.
 *
 * Modifications should be kept at a minimum, and restricted to adding or
 * removing factory definitions; other dependency types may be overwritten
 * when regenerating this file via mezzio-tooling commands.
 */
 
declare(strict_types=1);

return [
    'dependencies' => [
        'factories' => [
            App\Handler\BidDetailsHandler::class => App\Handler\BidDetailsHandlerFactory::class,
            App\Handler\BidHandler::class => App\Handler\BidHandlerFactory::class,
            App\Handler\BidListHandler::class => App\Handler\BidListHandlerFactory::class,
            App\Handler\BidPostHandler::class => App\Handler\BidPostHandlerFactory::class,
        ],
    ],
];
