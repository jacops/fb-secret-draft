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
use App\Form\BidForm;

class BidPostHandler implements RequestHandlerInterface
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
        $bidForm = new BidForm;
        $bidForm->setData($request->getParsedBody());

        if ($bidForm->isValid()) {
            $data = $bidForm->getData();
            $dailyDeadline = "21:00:00";

            $now = new \DateTime();
            $now->setTimezone(new \DateTimeZone('Europe/Warsaw'));

            $bidDate = new \DateTime($data['date'], new \DateTimeZone('Europe/Warsaw'));
            $nowDate = new \DateTime($now->format("Y-m-d"), new \DateTimeZone('Europe/Warsaw'));
            $deadlineDate = new \DateTime(sprintf("%s %s", $now->format("Y-m-d"), $dailyDeadline), new \DateTimeZone('Europe/Warsaw'));

            #Check if bid is not in the past
            if ($bidDate < $nowDate) {
                return new HtmlResponse($this->renderer->render(
                    'app::bid-message',
                    [
                        'message' => "Nie możesz bidować za dni wcześniejsze.",
                        'messageType' => 'danger'
                    ]
                ));
            }

            #check if bid not after the deadline
            if ($bidDate == $nowDate && $now > $deadlineDate) {
                return new HtmlResponse($this->renderer->render(
                    'app::bid-message',
                    [
                        'message' => "Minął deadline do bidowania.",
                        'messageType' => 'danger'
                    ]
                ));
            }

            $salt = md5(time().'unique');

            $bids = $data['bids'] . "\n\nsalt: $salt";

            $sql    = new Sql($this->adapter);

            $insert = $sql->insert();
            $insert->into('bids');
            $insert->values([
                'name' => $data['name'],
                'date' => $data['date'],
                'bids' => $bids,
            ]);

            try {
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
            } catch (\Exception $e) {
                return new HtmlResponse($this->renderer->render(
                    'app::bid-message',
                    [
                        'message' => "Wystąpił. Błąd. Prawdopodobnie wysłałeś już swoje bidy.",
                        'messageType' => 'danger'
                    ]
                ));
            }

            return new HtmlResponse($this->renderer->render(
                'app::bid-message',
                [
                    'message' => "Bidy zostały wysłane",
                    'messageType' => 'success'
                ]
            ));
        }

        return new HtmlResponse($this->renderer->render(
            'app::bid',
            [
                'form' => $bidForm
            ]
        ));
    }
}
