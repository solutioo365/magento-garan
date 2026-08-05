<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/** Validiert eu_garan_* (ganze Ware, > 2 Jahre). */
class GaranProductData
{
    public const ATTR_ENABLED = 'eu_garan_enabled';
    public const ATTR_YEARS = 'eu_garan_years';
    public const ATTR_BRAND = 'eu_garan_brand';
    public const ATTR_MODEL = 'eu_garan_model';
    public const ATTR_DECLARATION = 'eu_garan_declaration';

    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @return array{years:int,brand:string,model:string,declaration:string}|null
     */
    public function getValidData(ProductInterface $product): ?array
    {
        if (!(int) $product->getData(self::ATTR_ENABLED)) {
            return null;
        }
        $years = (int) $product->getData(self::ATTR_YEARS);
        $brand = trim((string) $product->getData(self::ATTR_BRAND));
        $model = trim((string) $product->getData(self::ATTR_MODEL));
        if ($years <= 2 || $brand === '' || $model === '') {
            return null;
        }
        return [
            'years' => $years,
            'brand' => $brand,
            'model' => $model,
            'declaration' => trim((string) $product->getData(self::ATTR_DECLARATION)),
        ];
    }

    /**
     * @return array{years:int,brand:string,model:string,declaration:string}|null
     */
    public function getValidDataById(int $productId): ?array
    {
        if ($productId < 1) {
            return null;
        }
        try {
            $product = $this->productRepository->getById($productId);
        } catch (\Throwable) {
            // NoSuchEntity oder Plugins (z. B. CustomMetadata TypeError) → kein GARAN
            return null;
        }
        return $this->getValidData($product);
    }
}
