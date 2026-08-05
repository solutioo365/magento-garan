<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model\Api\Data;

use Magento\Framework\DataObject;
use Solutioo\EuGuaranteeLabel\Api\Data\GaranImportResultInterface;

class GaranImportResult extends DataObject implements GaranImportResultInterface
{
    public function getSuccessCount(): int
    {
        return (int) $this->getData(self::KEY_SUCCESS_COUNT);
    }

    public function setSuccessCount(int $count): GaranImportResultInterface
    {
        return $this->setData(self::KEY_SUCCESS_COUNT, $count);
    }

    public function getFailedCount(): int
    {
        return (int) $this->getData(self::KEY_FAILED_COUNT);
    }

    public function setFailedCount(int $count): GaranImportResultInterface
    {
        return $this->setData(self::KEY_FAILED_COUNT, $count);
    }

    public function getErrors(): array
    {
        $errors = $this->getData(self::KEY_ERRORS);
        return is_array($errors) ? $errors : [];
    }

    public function setErrors(array $errors): GaranImportResultInterface
    {
        return $this->setData(self::KEY_ERRORS, $errors);
    }
}
