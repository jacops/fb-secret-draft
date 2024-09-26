<?php

declare(strict_types=1);

namespace App\Form;

use Laminas\Form\Form;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Textarea;
use Laminas\InputFilter\InputFilterProviderInterface;

class BidForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('bid-form');

        $this->add([
            'type'    => Text::class,
            'name'    => 'name',
            'required' => true,
            'options' => [
                'required' => true,
                'label' => 'Twitter handler',
            ],
        ]);

        $this->add([
            'type'    => Date::class,
            'name'    => 'date',
            'required' => true,
            'options' => [
                'label' => 'Data, której tyczą sie bidy (deadline 21:00)',
            ],
        ]);

        $this->add([
            'type'    => Textarea::class,
            'name'    => 'bids',
            'required' => true,
            'options' => [
                'required' => true,
                'label' => 'Bidy',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => true,
            ],
            'date' => [
                'required' => true,
            ],
            'bids' => [
                'required' => true,
            ],
        ];
    }
}
