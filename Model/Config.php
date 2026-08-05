<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Solutioo\Base\Model\ConfigProviderAbstract;

class Config extends ConfigProviderAbstract
{
    protected $pathPrefix = 'solutioo_eu_guarantee/';

    private const NOTICE_INFO_URLS = [
        'bg' => 'https://europa.eu/youreurope/гаранции',
        'hr' => 'https://europa.eu/youreurope/jamstva_hr',
        'cs' => 'https://europa.eu/youreurope/záruky_cs',
        'da' => 'https://europa.eu/youreurope/garantier',
        'nl' => 'https://europa.eu/youreurope/garantie',
        'de' => 'https://europa.eu/youreurope/garantien',
        'el' => 'https://europa.eu/youreurope/εγγυήσεις',
        'en' => 'https://europa.eu/youreurope/guarantees',
        'et' => 'https://europa.eu/youreurope/garantiid',
        'fi' => 'https://europa.eu/youreurope/virhevastuu',
        'fr' => 'https://europa.eu/youreurope/garanties',
        'hu' => 'https://europa.eu/youreurope/jótállás',
        'ga' => 'https://europa.eu/youreurope/ráthaíochtaí',
        'it' => 'https://europa.eu/youreurope/garanzie',
        'lt' => 'https://europa.eu/youreurope/garantijos',
        'lv' => 'https://europa.eu/youreurope/garantijas',
        'mt' => 'https://europa.eu/youreurope/garanziji',
        'pl' => 'https://europa.eu/youreurope/gwarancje',
        'pt' => 'https://europa.eu/youreurope/garantias',
        'ro' => 'https://europa.eu/youreurope/garanții',
        'sk' => 'https://europa.eu/youreurope/záruky_sk',
        'sl' => 'https://europa.eu/youreurope/jamstva_sl',
        'es' => 'https://europa.eu/youreurope/garantías',
        'sv' => 'https://europa.eu/youreurope/reklamationsrätt',
    ];

    private const GARAN_INFO_URL = 'https://europa.eu/youreurope/commercial-guarantee-durability/index.htm';

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager
    ) {
        parent::__construct($scopeConfig);
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->isSetFlag('general/enabled', $storeId);
    }

    public function isVisibleOnStorefront(?int $storeId = null): bool
    {
        return $this->isEnabled($storeId);
    }

    public function getNoticeLocale(?int $storeId = null): string
    {
        if ((string) $this->getValue('general/locale_mode', $storeId) === 'fixed') {
            $fixed = strtolower(trim((string) $this->getValue('general/locale_fixed', $storeId)));
            return $fixed !== '' ? $fixed : 'de';
        }
        try {
            $store = $storeId !== null
                ? $this->storeManager->getStore($storeId)
                : $this->storeManager->getStore();
            $locale = (string) $this->scopeConfig->getValue(
                'general/locale/code',
                ScopeInterface::SCOPE_STORE,
                $store->getId()
            );
            $lang = strtolower(substr($locale, 0, 2));
            return $lang !== '' ? $lang : 'de';
        } catch (\Throwable) {
            return 'de';
        }
    }

    /** Link wie QR auf dem Notice; Config überschreibt bei Bedarf. */
    public function getMoreInfoUrl(?int $storeId = null): string
    {
        $configured = trim((string) $this->getValue('general/more_info_url', $storeId));
        if ($configured !== '') {
            return $configured;
        }
        $locale = $this->getNoticeLocale($storeId);
        return self::NOTICE_INFO_URLS[$locale] ?? self::NOTICE_INFO_URLS['en'];
    }

    public function getGaranInfoUrl(): string
    {
        return self::GARAN_INFO_URL;
    }

    public function showFooter(?int $storeId = null): bool
    {
        return $this->isVisibleOnStorefront($storeId) && $this->isSetFlag('display/show_footer', $storeId);
    }

    /** @return 'link'|'strip' */
    public function getFooterStyle(?int $storeId = null): string
    {
        $style = (string) $this->getValue('display/footer_style', $storeId);
        return $style === Config\Source\FooterStyle::STRIP
            ? Config\Source\FooterStyle::STRIP
            : Config\Source\FooterStyle::LINK;
    }

    public function isFooterLinkStyle(?int $storeId = null): bool
    {
        return $this->getFooterStyle($storeId) === Config\Source\FooterStyle::LINK;
    }

    public function showCart(?int $storeId = null): bool
    {
        return $this->isVisibleOnStorefront($storeId) && $this->isSetFlag('display/show_cart', $storeId);
    }

    public function showCheckout(?int $storeId = null): bool
    {
        return $this->isVisibleOnStorefront($storeId) && $this->isSetFlag('display/show_checkout', $storeId);
    }

    public function showNoticePage(?int $storeId = null): bool
    {
        return $this->isVisibleOnStorefront($storeId) && $this->isSetFlag('display/show_notice_page', $storeId);
    }

    public function getFooterHeading(?int $storeId = null): string
    {
        return (string) $this->getValue('display/footer_heading', $storeId);
    }

    public function getCartText(?int $storeId = null): string
    {
        return (string) $this->getValue('display/cart_text', $storeId);
    }

    public function isGaranEnabled(?int $storeId = null): bool
    {
        return $this->isVisibleOnStorefront($storeId) && $this->isSetFlag('garan/enabled', $storeId);
    }

    public function showGaranOnPdp(?int $storeId = null): bool
    {
        return $this->isGaranEnabled($storeId) && $this->isSetFlag('garan/show_on_pdp', $storeId);
    }

    public function includeGaranInOrderMail(?int $storeId = null): bool
    {
        return $this->isGaranEnabled($storeId) && $this->isSetFlag('garan/mail_link', $storeId);
    }
}
