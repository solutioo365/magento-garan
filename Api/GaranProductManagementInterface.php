<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Api;

/**
 * @api
 */
interface GaranProductManagementInterface
{
    /**
     * @param string $sku
     * @param int $storeId
     * @return \Solutioo\EuGuaranteeLabel\Api\Data\GaranDataInterface
     */
    public function get(string $sku, int $storeId = 0);

    /**
     * @param string $sku
     * @param \Solutioo\EuGuaranteeLabel\Api\Data\GaranDataInterface $data
     * @return \Solutioo\EuGuaranteeLabel\Api\Data\GaranDataInterface
     */
    public function save(string $sku, \Solutioo\EuGuaranteeLabel\Api\Data\GaranDataInterface $data);

    /**
     * @param \Solutioo\EuGuaranteeLabel\Api\Data\GaranDataInterface[] $items
     * @return \Solutioo\EuGuaranteeLabel\Api\Data\GaranImportResultInterface
     */
    public function saveList(array $items);
}
