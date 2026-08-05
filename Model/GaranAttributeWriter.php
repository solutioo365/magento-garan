<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Solutioo\EuGuaranteeLabel\Api\Data\GaranDataInterface;
use Solutioo\EuGuaranteeLabel\Model\Api\Data\GaranDataFactory;

/** schreibt / liest eu_garan_* */
class GaranAttributeWriter
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductAction $productAction,
        private readonly StoreManagerInterface $storeManager,
        private readonly GaranProductData $garanProductData,
        private readonly GaranDataFactory $garanDataFactory,
        private readonly TypeListInterface $cacheTypeList
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getBySku(string $sku, int $storeId = 0): GaranDataInterface
    {
        $storeIdForLoad = $this->resolveStoreIdForEntityLoad($storeId);
        $product = $this->productRepository->get($sku, false, $storeIdForLoad, true);

        $enabled = (bool) (int) $product->getData(GaranProductData::ATTR_ENABLED);
        $years = (int) $product->getData(GaranProductData::ATTR_YEARS);
        $brand = trim((string) $product->getData(GaranProductData::ATTR_BRAND));
        $model = trim((string) $product->getData(GaranProductData::ATTR_MODEL));
        $declaration = trim((string) $product->getData(GaranProductData::ATTR_DECLARATION));
        $isValid = $this->garanProductData->getValidData($product) !== null;

        /** @var GaranDataInterface $dto */
        $dto = $this->garanDataFactory->create();
        $dto->setSku($sku)
            ->setStoreId($storeId)
            ->setEnabled($enabled)
            ->setYears($years)
            ->setBrand($brand)
            ->setModel($model)
            ->setDeclaration($declaration)
            ->setIsValid($isValid);

        return $dto;
    }

    /**
     * @throws NoSuchEntityException
     * @throws InputException
     */
    public function save(GaranDataInterface $data, bool $flushCaches = true): GaranDataInterface
    {
        $sku = trim($data->getSku());
        if ($sku === '') {
            throw new InputException(__('SKU is required.'));
        }

        $this->assertWritable($data);

        $storeId = $data->getStoreId();
        $storeIdForLoad = $this->resolveStoreIdForEntityLoad($storeId);

        try {
            $product = $this->productRepository->get($sku, false, $storeIdForLoad, true);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('The product that was requested doesn\'t exist. Verify and try again.'));
        }

        $this->productAction->updateAttributes(
            [(int) $product->getId()],
            [
                GaranProductData::ATTR_ENABLED => $data->getEnabled() ? 1 : 0,
                GaranProductData::ATTR_YEARS => (int) $data->getYears(),
                GaranProductData::ATTR_BRAND => trim($data->getBrand()),
                GaranProductData::ATTR_MODEL => trim($data->getModel()),
                GaranProductData::ATTR_DECLARATION => trim($data->getDeclaration()),
            ],
            $storeIdForLoad
        );

        if ($flushCaches) {
            $this->flushCaches();
        }

        // Fresh load (bypass repository cache)
        $this->productRepository->get($sku, false, $storeIdForLoad, true);
        return $this->getBySku($sku, $storeId);
    }

    /**
     * Soft save for bulk/XML: returns null on success, error message on failure.
     */
    public function trySave(GaranDataInterface $data, bool $flushCaches = false): ?string
    {
        try {
            if (trim($data->getSku()) === '') {
                return (string) __('SKU is required.');
            }
            $this->save($data, $flushCaches);
            return null;
        } catch (NoSuchEntityException $e) {
            return (string) $e->getMessage();
        } catch (InputException $e) {
            return (string) $e->getMessage();
        } catch (\Throwable $e) {
            return (string) __('Unexpected error: %1', $e->getMessage());
        }
    }

    public function flushAfterBulk(): void
    {
        $this->flushCaches();
    }

    /**
     * @throws InputException
     */
    public function assertWritable(GaranDataInterface $data): void
    {
        if (!$data->getEnabled()) {
            return;
        }

        $years = (int) $data->getYears();
        $brand = trim($data->getBrand());
        $model = trim($data->getModel());

        if ($years <= 2) {
            throw new InputException(__('years must be > 2 if enabled'));
        }
        if ($brand === '') {
            throw new InputException(__('brand required if enabled'));
        }
        if ($model === '') {
            throw new InputException(__('model required if enabled'));
        }
    }

    private function resolveStoreIdForEntityLoad(int $storeId): int
    {
        if ($storeId === 0) {
            $default = $this->storeManager->getDefaultStoreView();
            return $default ? (int) $default->getId() : 0;
        }

        return $storeId;
    }

    private function flushCaches(): void
    {
        $this->cacheTypeList->cleanType('full_page');
        $this->cacheTypeList->cleanType('block_html');
        $this->cacheTypeList->cleanType('config');
    }
}
