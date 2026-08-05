<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class FooterStyle implements OptionSourceInterface
{
    public const LINK = 'link';
    public const STRIP = 'strip';

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::LINK,
                'label' => __('Dezent: Link in Footer-Leiste (empfohlen)'),
            ],
            [
                'value' => self::STRIP,
                'label' => __('Auffällig: Hinweis-Streifen mit Aufklapp-Label'),
            ],
        ];
    }
}
