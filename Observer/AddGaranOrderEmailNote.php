<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Solutioo\EuGuaranteeLabel\Model\Config;
use Solutioo\EuGuaranteeLabel\Model\GaranProductData;

class AddGaranOrderEmailNote implements ObserverInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly GaranProductData $garanProductData,
        private readonly UrlInterface $url
    ) {
    }

    public function execute(Observer $observer): void
    {
        $transport = $observer->getEvent()->getData('transportObject');
        if (!$transport) {
            return;
        }
        $order = $transport->getOrder();
        if (!$order instanceof Order) {
            return;
        }

        $storeId = (int) $order->getStoreId();
        if (!$this->config->includeGaranInOrderMail($storeId)) {
            $transport->setData('solutioo_eu_garan_note', '');
            return;
        }

        $lines = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $productId = (int) $item->getProductId();
            if ($productId < 1) {
                continue;
            }
            $data = $this->garanProductData->getValidDataById($productId);
            if ($data === null) {
                continue;
            }
            $lines[] = sprintf(
                '%s — GARAN %d %s (%s / %s)',
                $item->getName(),
                $data['years'],
                (string) __('Jahre'),
                $data['brand'],
                $data['model']
            );
        }

        if ($lines === []) {
            $transport->setData('solutioo_eu_garan_note', '');
            return;
        }

        $noticeUrl = $this->url->getUrl('eu-guarantee/notice/index', ['_scope' => $storeId]);
        $html = '<p><strong>' . __('EU-Haltbarkeitsgarantie (GARAN)') . '</strong></p><ul>';
        foreach ($lines as $line) {
            $html .= '<li>' . htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</li>';
        }
        $html .= '</ul><p>'
            . __('Die gesetzliche Gewährleistung bleibt unberührt.')
            . ' <a href="' . htmlspecialchars($noticeUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">'
            . __('Mehr zur gesetzlichen Gewährleistung')
            . '</a></p>';

        $transport->setData('solutioo_eu_garan_note', $html);
    }
}
