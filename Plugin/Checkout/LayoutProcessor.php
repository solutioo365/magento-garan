<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor as Subject;
use Magento\Framework\UrlInterface;
use Solutioo\EuGuaranteeLabel\Model\Config;

/**
 * Inject EU guarantee notice into Luma checkout order summary (Knockout sidebar).
 */
class LayoutProcessor
{
    public function __construct(
        private readonly Config $config,
        private readonly UrlInterface $url
    ) {
    }

    /**
     * @param array<string, mixed> $jsLayout
     * @return array<string, mixed>
     */
    public function afterProcess(Subject $subject, array $jsLayout): array
    {
        if (!$this->config->showCheckout()) {
            return $jsLayout;
        }

        $summaryChildren = &$jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children'];
        if (!isset($summaryChildren) || !is_array($summaryChildren)) {
            return $jsLayout;
        }

        $label = trim($this->config->getCartText());
        if ($label === '') {
            $label = (string) __('Ihre gesetzlichen Gewährleistungsrechte');
        }

        // Prefer itemsAfter merge point; fall back to summary children.
        if (isset($summaryChildren['itemsAfter']['children']) && is_array($summaryChildren['itemsAfter']['children'])) {
            $summaryChildren['itemsAfter']['children']['eu-guarantee-notice'] = $this->buildComponent($label);
        } else {
            $summaryChildren['eu-guarantee-notice'] = $this->buildComponent($label);
        }

        return $jsLayout;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildComponent(string $label): array
    {
        return [
            'component' => 'Solutioo_EuGuaranteeLabel/js/view/checkout-notice',
            'config' => [
                'noticeUrl' => $this->url->getUrl('eu-guarantee/notice'),
                'linkText' => $label,
            ],
            'sortOrder' => 50,
        ];
    }
}
