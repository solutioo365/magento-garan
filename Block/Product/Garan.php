<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Block\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Solutioo\EuGuaranteeLabel\Model\Config;
use Solutioo\EuGuaranteeLabel\Model\GaranProductData;

class Garan extends Template
{
    public function __construct(
        Context $context,
        private readonly Config $config,
        private readonly GaranProductData $garanProductData,
        private readonly Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function isVisible(): bool
    {
        return $this->config->showGaranOnPdp() && $this->getGaranData() !== null;
    }

    /** @return array{years:int,brand:string,model:string,declaration:string}|null */
    public function getGaranData(): ?array
    {
        $product = $this->getProduct();
        return $product ? $this->garanProductData->getValidData($product) : null;
    }

    public function getProduct(): ?ProductInterface
    {
        $product = $this->registry->registry('current_product');
        return $product instanceof ProductInterface ? $product : null;
    }

    public function getNestedImageUrl(): string
    {
        return $this->imageUrl('nested');
    }

    public function getExpandedImageUrl(): string
    {
        return $this->imageUrl('colour');
    }

    public function getNoticePageUrl(): string
    {
        return $this->getUrl('eu-guarantee/notice/index');
    }

    public function getGaranInfoUrl(): string
    {
        return $this->config->getGaranInfoUrl();
    }

    public function getDeclarationUrl(): string
    {
        $data = $this->getGaranData();
        if (!$data || $data['declaration'] === '') {
            return '';
        }
        $decl = $data['declaration'];
        if (str_starts_with($decl, 'http://') || str_starts_with($decl, 'https://')) {
            return $decl;
        }
        if (str_starts_with($decl, 'media/') || str_starts_with($decl, '/media/')) {
            return $this->getBaseUrl() . ltrim($decl, '/');
        }
        return $this->getUrl(null, ['_direct' => $decl]);
    }

    private function imageUrl(string $type): string
    {
        $product = $this->getProduct();
        if (!$product || !$product->getId()) {
            return '';
        }
        return $this->getUrl('eu-guarantee/garan/image', [
            'type' => $type,
            'id' => (int) $product->getId(),
        ]);
    }
}
