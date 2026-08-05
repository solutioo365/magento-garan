<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model\Api\Data;

use Magento\Framework\DataObject;
use Solutioo\EuGuaranteeLabel\Api\Data\GaranImportErrorInterface;

class GaranImportError extends DataObject implements GaranImportErrorInterface
{
    public function getSku(): string
    {
        return (string) $this->getData(self::KEY_SKU);
    }

    public function setSku(string $sku): GaranImportErrorInterface
    {
        return $this->setData(self::KEY_SKU, $sku);
    }

    public function getMessage(): string
    {
        return (string) $this->getData(self::KEY_MESSAGE);
    }

    public function setMessage(string $message): GaranImportErrorInterface
    {
        return $this->setData(self::KEY_MESSAGE, $message);
    }
}
