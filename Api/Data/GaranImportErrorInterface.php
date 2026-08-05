<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Api\Data;

/**
 * @api
 */
interface GaranImportErrorInterface
{
    public const KEY_SKU = 'sku';
    public const KEY_MESSAGE = 'message';

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
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self;
}
