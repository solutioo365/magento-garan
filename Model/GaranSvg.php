<?php

declare(strict_types=1);

namespace Solutioo\EuGuaranteeLabel\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;

class GaranSvg
{
    public function __construct(
        private readonly ModuleDirReader $moduleDirReader
    ) {
    }

    /**
     * @param array{years:int|float|string,brand:string,model:string} $data
     */
    public function render(string $type, array $data): string
    {
        $type = $type === 'colour' ? 'colour' : 'nested';
        $file = $this->moduleDirReader->getModuleDir(Dir::MODULE_VIEW_DIR, 'Solutioo_EuGuaranteeLabel')
            . '/frontend/web/images/garan/' . $type . '.svg';
        if (!is_readable($file)) {
            throw new LocalizedException(__('GARAN SVG fehlt.'));
        }

        $svg = (string) file_get_contents($file);
        $years = $this->formatYears($data['years']);
        $brand = htmlspecialchars((string) $data['brand'], ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $model = htmlspecialchars((string) $data['model'], ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $svg = preg_replace_callback(
            '#(<tspan[^>]*>)XX(</tspan>)#',
            static fn (array $m): string => $m[1] . $years . $m[2],
            $svg,
            -1,
            $xxCount
        ) ?? $svg;
        if ($xxCount < 1) {
            throw new LocalizedException(__('GARAN: XX-Platzhalter fehlt.'));
        }

        if ($type === 'colour') {
            $svg = preg_replace(
                '#<text class="cls-5" transform="translate\(6\.32 74\.52\)">.*?</text>#s',
                '<text class="cls-5" transform="translate(6.32 74.52)"><tspan x="0" y="0">' . $brand . '</tspan></text>',
                $svg,
                1,
                $brandCount
            ) ?? $svg;
            $svg = preg_replace(
                '#<text class="cls-5" transform="translate\(196\.75 74\.52\)">.*?</text>#s',
                '<text class="cls-5" transform="translate(196.75 74.52)"><tspan x="0" y="0">' . $model . '</tspan></text>',
                $svg,
                1,
                $modelCount
            ) ?? $svg;
            if (($brandCount ?? 0) < 1 || ($modelCount ?? 0) < 1) {
                throw new LocalizedException(__('GARAN: Brand/Model-Platzhalter fehlen.'));
            }
        }

        return $svg;
    }

    private function formatYears(int|float|string $years): string
    {
        if (is_string($years) && str_contains($years, ',')) {
            return htmlspecialchars($years, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        }
        $num = (float) $years;
        if (abs($num * 2 - round($num * 2)) < 0.001 && floor($num) != $num) {
            return number_format($num, 1, ',', '');
        }
        return (string) (int) round($num);
    }
}
