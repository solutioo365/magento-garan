<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model;

use Magento\Framework\View\Asset\Repository as AssetRepository;

class Assets
{
    public function __construct(
        private readonly Config $config,
        private readonly AssetRepository $assetRepository
    ) {
    }

    public function getNoticeSvgUrl(?int $storeId = null): string
    {
        foreach (array_unique([$this->config->getNoticeLocale($storeId), 'de', 'en']) as $code) {
            try {
                return $this->assetRepository->getUrl(
                    'Solutioo_EuGuaranteeLabel::images/legal-guarantee/' . $code . '.svg'
                );
            } catch (\Throwable) {
                // nächste Locale
            }
        }
        return $this->assetRepository->getUrl('Solutioo_EuGuaranteeLabel::images/legal-guarantee/de.svg');
    }
}
