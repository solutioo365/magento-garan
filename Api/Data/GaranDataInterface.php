<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Api\Data;

/**
 * @api
 */
interface GaranDataInterface
{
    public const KEY_SKU = 'sku';
    public const KEY_STORE_ID = 'store_id';
    public const KEY_ENABLED = 'enabled';
    public const KEY_YEARS = 'years';
    public const KEY_BRAND = 'brand';
    public const KEY_MODEL = 'model';
    public const KEY_DECLARATION = 'declaration';
    public const KEY_IS_VALID = 'is_valid';

    /**
     * @return string
     */
    public function getSku(): string;

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku): self;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId(int $storeId): self;

    /**
     * @return bool
     */
    public function getEnabled(): bool;

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self;

    /**
     * @return int
     */
    public function getYears(): int;

    /**
     * @param int $years
     * @return $this
     */
    public function setYears(int $years): self;

    /**
     * @return string
     */
    public function getBrand(): string;

    /**
     * @param string $brand
     * @return $this
     */
    public function setBrand(string $brand): self;

    /**
     * @return string
     */
    public function getModel(): string;

    /**
     * @param string $model
     * @return $this
     */
    public function setModel(string $model): self;

    /**
     * @return string
     */
    public function getDeclaration(): string;

    /**
     * @param string $declaration
     * @return $this
     */
    public function setDeclaration(string $declaration): self;

    /**
     * @return bool
     */
    public function getIsValid(): bool;

    /**
     * @param bool $isValid
     * @return $this
     */
    public function setIsValid(bool $isValid): self;
}
