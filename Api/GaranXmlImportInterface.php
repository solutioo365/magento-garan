<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Api;

/**
 * @api
 */
interface GaranXmlImportInterface
{
    /**
     * @param string $xml
     * @return \Solutioo\EuGuaranteeLabel\Api\Data\GaranImportResultInterface
     */
    public function importXml(string $xml);
}
