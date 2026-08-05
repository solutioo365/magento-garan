<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Controller\Garan;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Solutioo\EuGuaranteeLabel\Model\Config;
use Solutioo\EuGuaranteeLabel\Model\GaranProductData;
use Solutioo\EuGuaranteeLabel\Model\GaranSvg;

class Image extends Action implements HttpGetActionInterface
{
    public function __construct(
        Context $context,
        private readonly RawFactory $rawFactory,
        private readonly Config $config,
        private readonly GaranProductData $garanProductData,
        private readonly GaranSvg $garanSvg
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->rawFactory->create();
        $result->setHeader('Content-Type', 'image/svg+xml; charset=UTF-8', true);
        $result->setHeader('Cache-Control', 'public, max-age=86400', true);

        if (!$this->config->isGaranEnabled()) {
            $result->setHttpResponseCode(404);
            $result->setContents('');
            return $result;
        }

        $productId = (int) $this->getRequest()->getParam('id');
        $type = $this->getRequest()->getParam('type') === 'colour' ? 'colour' : 'nested';

        try {
            $data = $productId > 0 ? $this->garanProductData->getValidDataById($productId) : null;
        } catch (\Throwable) {
            $data = null;
        }

        if (!$data) {
            $result->setHttpResponseCode(404);
            $result->setContents('');
            return $result;
        }

        try {
            $result->setContents($this->garanSvg->render($type, $data));
            return $result;
        } catch (\Throwable) {
            $result->setHttpResponseCode(500);
            $result->setContents('');
            return $result;
        }
    }
}
