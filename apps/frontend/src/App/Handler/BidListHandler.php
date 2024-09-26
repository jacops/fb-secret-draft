<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;
use Mezzio\Template\TemplateRendererInterface;

class BidListHandler implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     * @var AdapterInterface
     */
    private $adapter;

    public function __construct(AdapterInterface $adapter, TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->adapter  = $adapter;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $sql    = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('bids');
        $select->columns(['date']);
        $select->group(['date']);

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        return new HtmlResponse($this->renderer->render(
            'app::bid-list',
            [
                'dates' => $results,
            ]
        ));
    }
}
