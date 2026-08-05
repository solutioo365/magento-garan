<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LocaleMode implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'auto', 'label' => __('Auto (Store-Locale)')],
            ['value' => 'fixed', 'label' => __('Fest (Feld unten)')],
        ];
    }
}
