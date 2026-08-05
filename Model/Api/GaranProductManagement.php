<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model\Api;

use Solutioo\EuGuaranteeLabel\Api\Data\GaranDataInterface;
use Solutioo\EuGuaranteeLabel\Api\Data\GaranImportResultInterface;
use Solutioo\EuGuaranteeLabel\Api\GaranProductManagementInterface;
use Solutioo\EuGuaranteeLabel\Model\Api\Data\GaranImportErrorFactory;
use Solutioo\EuGuaranteeLabel\Model\Api\Data\GaranImportResultFactory;
use Solutioo\EuGuaranteeLabel\Model\GaranAttributeWriter;

class GaranProductManagement implements GaranProductManagementInterface
{
    public function __construct(
        private readonly GaranAttributeWriter $writer,
        private readonly GaranImportResultFactory $resultFactory,
        private readonly GaranImportErrorFactory $errorFactory
    ) {
    }

    public function get(string $sku, int $storeId = 0): GaranDataInterface
    {
        return $this->writer->getBySku($sku, $storeId);
    }

    public function save(string $sku, GaranDataInterface $data): GaranDataInterface
    {
        $data->setSku($sku);
        return $this->writer->save($data);
    }

    public function saveList(array $items): GaranImportResultInterface
    {
        $success = 0;
        $errors = [];

        foreach ($items as $item) {
            if (!$item instanceof GaranDataInterface) {
                $error = $this->errorFactory->create();
                $error->setSku('')
                    ->setMessage((string) __('Invalid item payload.'));
                $errors[] = $error;
                continue;
            }

            $message = $this->writer->trySave($item, false);
            if ($message === null) {
                $success++;
                continue;
            }

            $error = $this->errorFactory->create();
            $error->setSku($item->getSku())
                ->setMessage($message);
            $errors[] = $error;
        }

        if ($success > 0) {
            $this->writer->flushAfterBulk();
        }

        /** @var GaranImportResultInterface $result */
        $result = $this->resultFactory->create();
        $result->setSuccessCount($success)
            ->setFailedCount(count($errors))
            ->setErrors($errors);

        return $result;
    }
}
