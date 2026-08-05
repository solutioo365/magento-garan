<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Solutioo\EuGuaranteeLabel\Model\GaranProductData;

class AddGaranProductAttributes implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory
    ) {
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributes = [
            GaranProductData::ATTR_ENABLED => [
                'type' => 'int',
                'label' => 'EU GARAN Label aktiv',
                'input' => 'boolean',
                'source' => Boolean::class,
                'required' => false,
                'default' => '0',
                'sort_order' => 210,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'group' => 'EU GARAN',
                'note' => 'Nur aktivieren, wenn Hersteller eine Haltbarkeitsgarantie >2 Jahre für die gesamte Ware kostenlos gewährt.',
            ],
            GaranProductData::ATTR_YEARS => [
                'type' => 'int',
                'label' => 'EU GARAN Dauer (Jahre)',
                'input' => 'text',
                'required' => false,
                'sort_order' => 220,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'group' => 'EU GARAN',
                'note' => 'Muss größer als 2 sein.',
            ],
            GaranProductData::ATTR_BRAND => [
                'type' => 'varchar',
                'label' => 'EU GARAN Marke / Hersteller',
                'input' => 'text',
                'required' => false,
                'sort_order' => 230,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'group' => 'EU GARAN',
            ],
            GaranProductData::ATTR_MODEL => [
                'type' => 'varchar',
                'label' => 'EU GARAN Modellnummer',
                'input' => 'text',
                'required' => false,
                'sort_order' => 240,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'group' => 'EU GARAN',
            ],
            GaranProductData::ATTR_DECLARATION => [
                'type' => 'varchar',
                'label' => 'EU GARAN Garantieerklärung (CMS-ID, Media-Pfad oder URL)',
                'input' => 'text',
                'required' => false,
                'sort_order' => 250,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'user_defined' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'group' => 'EU GARAN',
            ],
        ];

        foreach ($attributes as $code => $attr) {
            if ($eavSetup->getAttributeId(Product::ENTITY, $code)) {
                continue;
            }
            $eavSetup->addAttribute(Product::ENTITY, $code, $attr);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
