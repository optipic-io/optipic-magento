<?php

namespace Optipic\Optipic\Observer;

use Optipic\Optipic\Helper\Data;
use Optipic\Optipic\Model\Optipic;

class ChangeResponseObserver implements \Magento\Framework\Event\ObserverInterface
{
    private $optipic;

    public function __construct(Optipic $optipic)
    {
        $this->optipic  = $optipic;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $response = $observer->getData('response');
        $content = $response->getContent();
        $content = $this->optipic->changeContent($content);
        $response->setContent($content);
    }
}