define(['jquery'], function ($) {
    'use strict';

    var TARGETS = [
        '.bottom-inner > .links',
        '.bottom-container .bottom-inner .links',
        '.footer .bottom-inner .links',
        'footer .footer.content ul',
        '.page-footer .footer.content ul',
        'footer .footer-links',
        '#footer .links',
        'footer nav ul',
        'footer ul'
    ];

    var placedGlobally = false;

    function findTarget() {
        var i, $el;
        for (i = 0; i < TARGETS.length; i++) {
            $el = $(TARGETS[i]).first();
            if ($el.length) {
                return $el;
            }
        }
        return $();
    }

    /**
     * Match sibling list markup (Hyvä: li.mt-4 + a.text-base; Luma: li.nav.item).
     */
    function placeIntoList($target, $link) {
        var tag = ($target.prop('tagName') || '').toLowerCase();
        var $sampleLi;
        var $sampleA;
        var $li;

        if (tag === 'ul' || tag === 'ol') {
            if ($target.find('a.eu-guarantee-footer-link__a').length) {
                return true;
            }
            // First li often has no spacing class; prefer a spaced sibling (Hyvä mt-4)
            $sampleLi = $target.children('li.mt-4').first();
            if (!$sampleLi.length) {
                $sampleLi = $target.children('li.nav.item').first();
            }
            if (!$sampleLi.length) {
                $sampleLi = $target.children('li').last();
            }
            $sampleA = $sampleLi.find('a').first();
            $li = $('<li/>');
            if ($sampleLi.length && $sampleLi.attr('class')) {
                $li.attr('class', $sampleLi.attr('class'));
            } else {
                $li.addClass('mt-4');
            }
            $li.addClass('eu-guarantee-footer-link__item');
            if ($sampleA.length && $sampleA.attr('class')) {
                $link.attr(
                    'class',
                    $.trim($sampleA.attr('class') + ' eu-guarantee-footer-link__a')
                );
            } else {
                $link.addClass('text-base leading-6');
            }
            $li.append($link);
            $target.append($li);
            return true;
        }

        if ($target.find('a.eu-guarantee-footer-link__a').length) {
            return true;
        }
        $target.append($link);
        return true;
    }

    return function (config, element) {
        var $wrap = $(element);
        var $link = $wrap.find('a.eu-guarantee-footer-link__a').first();
        var $target;

        if (!$link.length || $wrap.data('euGuaranteePlaced') || placedGlobally) {
            $wrap.remove();
            return;
        }

        $target = findTarget();
        if ($target.length) {
            placeIntoList($target, $link);
            placedGlobally = true;
            $wrap.data('euGuaranteePlaced', true).remove();
            return;
        }

        $wrap.addClass('eu-guarantee-footer-link--fallback');
        placedGlobally = true;
        $wrap.data('euGuaranteePlaced', true);
    };
});
