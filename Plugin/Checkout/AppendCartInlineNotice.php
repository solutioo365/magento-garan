<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Plugin\Checkout;

use Magento\Checkout\Block\Cart as CartBlock;

class AppendCartInlineNotice
{
    private const BLOCK_NAME = 'checkout.cart.methods.bottom';
    private const CHILD_ALIAS = 'eu_guarantee_inline';
    private const MARKER_CLASS = 'eu-guarantee-inline';

    public function afterToHtml(CartBlock $subject, string $result): string
    {
        if ($subject->getNameInLayout() !== self::BLOCK_NAME) {
            return $result;
        }

        if ($result === '' || str_contains($result, self::MARKER_CLASS)) {
            return $result;
        }

        $child = $subject->getChildBlock(self::CHILD_ALIAS);
        if (!$child) {
            return $result;
        }

        $childHtml = $child->toHtml();
        if ($childHtml === '') {
            return $result;
        }

        $inserted = preg_replace(
            '/(<ul\b[^>]*\bcheckout-methods-items\b[^>]*>.*?<\/ul>)/is',
            '$1' . $childHtml,
            $result,
            1,
            $count
        );

        if (is_string($inserted) && $count > 0) {
            return $inserted;
        }

        return $result . $childHtml;
    }
}
