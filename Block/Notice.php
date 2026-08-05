<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Solutioo\EuGuaranteeLabel\Model\Assets;
use Solutioo\EuGuaranteeLabel\Model\Config;

class Notice extends Template
{
    public function __construct(
        Context $context,
        private readonly Config $config,
        private readonly Assets $assets,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isVisible(): bool
    {
        return match ((string) $this->getData('placement')) {
            'footer' => $this->config->showFooter(),
            'cart' => $this->config->showCart(),
            'checkout' => $this->config->showCheckout(),
            'page' => $this->config->showNoticePage(),
            default => $this->config->isVisibleOnStorefront(),
        };
    }

    public function getNoticeImageUrl(): string
    {
        return $this->assets->getNoticeSvgUrl();
    }

    public function getNoticePageUrl(): string
    {
        return $this->getUrl('eu-guarantee/notice/index');
    }

    public function getFooterHeading(): string
    {
        return $this->config->getFooterHeading();
    }

    public function getFooterStyle(): string
    {
        return $this->config->getFooterStyle();
    }

    public function isFooterLinkStyle(): bool
    {
        return $this->config->isFooterLinkStyle();
    }

    public function getFooterLinkText(): string
    {
        $heading = trim($this->getFooterHeading());
        if ($heading !== '') {
            return $heading;
        }
        $cart = trim($this->getCartText());
        return $cart !== '' ? $cart : (string) __('Gesetzliche Gewährleistung');
    }

    public function getCartText(): string
    {
        return $this->config->getCartText();
    }

    public function getMoreInfoUrl(): string
    {
        return $this->config->getMoreInfoUrl();
    }

    public function getCacheTags(): array
    {
        return array_merge(parent::getCacheTags(), [
            'SOLUTIOO_EU_GUARANTEE',
            'STORE_' . $this->_storeManager->getStore()->getId(),
        ]);
    }
}
