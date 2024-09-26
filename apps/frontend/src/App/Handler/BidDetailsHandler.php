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

class BidDetailsHandler implements RequestHandlerInterface
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
        $date       = $request->getAttribute('date');
        $canSeeBids = $this->canSeeBids($date);

        $sql    = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('bids');
        $select->where(['date' => $date]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $rowset = $statement->execute();
        $list = [];

        foreach ($rowset as $row) {
            $list[] = [
                'name' => $row['name'],
                'bids' => $canSeeBids ? $row['bids'] : '***',
                'checksum' => hash('md5', str_replace("\r\n", "\n", $row['bids']))
            ];
        }

        return new HtmlResponse($this->renderer->render(
            'app::bid-details',
            [
                'rowset' => $list,
                'date' => $date
            ]
        ));
    }

    private function canSeeBids($date) : bool {
        $dailyDeadline = "21:00:00";

        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('Europe/Warsaw'));

        $bidDate = new \DateTime($date, new \DateTimeZone('Europe/Warsaw'));
        $nowDate = new \DateTime($now->format("Y-m-d"), new \DateTimeZone('Europe/Warsaw'));
        $deadlineDate = new \DateTime(sprintf("%s %s", $now->format("Y-m-d"), $dailyDeadline), new \DateTimeZone('Europe/Warsaw'));

        #if previous dates
        if ($bidDate < $nowDate) {
            return true;
        }

        #If the same day and after deadline
        if ($bidDate == $nowDate && $now >= $deadlineDate) {
            return true;
        }

        return false;
    }
}
