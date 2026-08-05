<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model\Api\Data;

use Magento\Framework\DataObject;
use Solutioo\EuGuaranteeLabel\Api\Data\GaranDataInterface;

class GaranData extends DataObject implements GaranDataInterface
{
    public function getSku(): string
    {
        return (string) $this->getData(self::KEY_SKU);
    }

    public function setSku(string $sku): GaranDataInterface
    {
        return $this->setData(self::KEY_SKU, $sku);
    }

    public function getStoreId(): int
    {
        return (int) $this->getData(self::KEY_STORE_ID);
    }

    public function setStoreId(int $storeId): GaranDataInterface
    {
        return $this->setData(self::KEY_STORE_ID, $storeId);
    }

    public function getEnabled(): bool
    {
        return (bool) $this->getData(self::KEY_ENABLED);
    }

    public function setEnabled(bool $enabled): GaranDataInterface
    {
        return $this->setData(self::KEY_ENABLED, $enabled);
    }

    public function getYears(): int
    {
        return (int) $this->getData(self::KEY_YEARS);
    }

    public function setYears(int $years): GaranDataInterface
    {
        return $this->setData(self::KEY_YEARS, $years);
    }

    public function getBrand(): string
    {
        return (string) $this->getData(self::KEY_BRAND);
    }

    public function setBrand(string $brand): GaranDataInterface
    {
        return $this->setData(self::KEY_BRAND, $brand);
    }

    public function getModel(): string
    {
        return (string) $this->getData(self::KEY_MODEL);
    }

    public function setModel(string $model): GaranDataInterface
    {
        return $this->setData(self::KEY_MODEL, $model);
    }

    public function getDeclaration(): string
    {
        return (string) $this->getData(self::KEY_DECLARATION);
    }

    public function setDeclaration(string $declaration): GaranDataInterface
    {
        return $this->setData(self::KEY_DECLARATION, $declaration);
    }

    public function getIsValid(): bool
    {
        return (bool) $this->getData(self::KEY_IS_VALID);
    }

    public function setIsValid(bool $isValid): GaranDataInterface
    {
        return $this->setData(self::KEY_IS_VALID, $isValid);
    }
}
