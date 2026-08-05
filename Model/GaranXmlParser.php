<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model;

use Magento\Framework\Exception\InputException;
use Solutioo\EuGuaranteeLabel\Api\Data\GaranDataInterface;
use Solutioo\EuGuaranteeLabel\Model\Api\Data\GaranDataFactory;

class GaranXmlParser
{
    private const MAX_XML_BYTES = 1048576;

    public function __construct(
        private readonly GaranDataFactory $garanDataFactory
    ) {
    }

    /**
     * @return GaranDataInterface[]
     * @throws InputException
     */
    public function parse(string $xml): array
    {
        $xml = trim($xml);
        if ($xml === '') {
            throw new InputException(__('xml empty'));
        }
        if (strlen($xml) > self::MAX_XML_BYTES) {
            throw new InputException(__('xml too large'));
        }
        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $xml)) {
            throw new InputException(__('xml entities not allowed'));
        }

        $previous = libxml_use_internal_errors(true);
        try {
            $document = simplexml_load_string(
                $xml,
                \SimpleXMLElement::class,
                LIBXML_NONET | LIBXML_NOCDATA
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if ($document === false) {
            throw new InputException(__('xml invalid'));
        }

        if ($document->getName() !== 'garanProducts') {
            throw new InputException(__('root must be garanProducts'));
        }

        $items = [];
        foreach ($document->product as $productNode) {
            $sku = trim((string) ($productNode['sku'] ?? ''));
            if ($sku === '') {
                throw new InputException(__('product needs sku'));
            }

            $storeId = isset($productNode['storeId']) ? (int) $productNode['storeId'] : 0;
            $enabledRaw = trim((string) ($productNode->enabled ?? '0'));
            $enabled = in_array(strtolower($enabledRaw), ['1', 'true', 'yes'], true);

            /** @var GaranDataInterface $dto */
            $dto = $this->garanDataFactory->create();
            $dto->setSku($sku)
                ->setStoreId($storeId)
                ->setEnabled($enabled)
                ->setYears((int) trim((string) ($productNode->years ?? '0')))
                ->setBrand(trim((string) ($productNode->brand ?? '')))
                ->setModel(trim((string) ($productNode->model ?? '')))
                ->setDeclaration(trim((string) ($productNode->declaration ?? '')))
                ->setIsValid(false);

            $items[] = $dto;
        }

        if ($items === []) {
            throw new InputException(__('no products in xml'));
        }

        return $items;
    }
}
