define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * GARAN compact row: expand/collapse only on click (no hover open, no layout shift on hover).
     */
    return function (config, element) {
        var $root = $(element);
        var $btn = $root.find('.eu-garan-toggle');
        var $panel = $root.find('.eu-garan-panel');
        var $hintOpen = $root.find('[data-role="garan-hint-open"]');
        var $hintClose = $root.find('[data-role="garan-hint-close"]');

        function setOpen(open) {
            $btn.attr('aria-expanded', open ? 'true' : 'false');
            $root.toggleClass('eu-garan--open', open);
            if (open) {
                $panel.removeAttr('hidden');
            } else {
                $panel.attr('hidden', 'hidden');
            }
            if ($hintOpen.length && $hintClose.length) {
                if (open) {
                    $hintOpen.attr('hidden', 'hidden');
                    $hintClose.removeAttr('hidden');
                } else {
                    $hintOpen.removeAttr('hidden');
                    $hintClose.attr('hidden', 'hidden');
                }
            }
        }

        $btn.on('click', function () {
            setOpen($btn.attr('aria-expanded') !== 'true');
        });

        $(document).on('keydown.euGaran', function (e) {
            if (e.key !== 'Escape') {
                return;
            }
            if ($btn.attr('aria-expanded') === 'true') {
                setOpen(false);
                $btn.trigger('focus');
            }
        });
    };
});
