<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Controller\Notice;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Solutioo\EuGuaranteeLabel\Model\Config;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly PageFactory $pageFactory,
        private readonly ForwardFactory $forwardFactory,
        private readonly Config $config
    ) {
    }

    public function execute()
    {
        if (!$this->config->showNoticePage()) {
            return $this->forwardFactory->create()->forward('noroute');
        }
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set(__('Gesetzliche Gewährleistung'));
        return $page;
    }
}
