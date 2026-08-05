<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model\Api;

use Solutioo\EuGuaranteeLabel\Api\Data\GaranImportResultInterface;
use Solutioo\EuGuaranteeLabel\Api\GaranProductManagementInterface;
use Solutioo\EuGuaranteeLabel\Api\GaranXmlImportInterface;
use Solutioo\EuGuaranteeLabel\Model\GaranXmlParser;

class GaranXmlImport implements GaranXmlImportInterface
{
    public function __construct(
        private readonly GaranXmlParser $parser,
        private readonly GaranProductManagementInterface $productManagement
    ) {
    }

    public function importXml(string $xml): GaranImportResultInterface
    {
        $items = $this->parser->parse($xml);
        return $this->productManagement->saveList($items);
    }
}
