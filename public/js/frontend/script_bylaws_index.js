$(document).ready(function() {
    const $firstItem = $('.bylaws_item').first();
    const $firstTrigger = $firstItem.find('.accordion_trigger');
    const $firstContent = $('#' + $firstTrigger.attr('aria-controls'));

    $firstTrigger.attr('aria-expanded', 'true');
    $firstItem.addClass('on');
    $firstContent.prop('hidden', false).show();

    $('.accordion_trigger').on('click', function() {
        const $this = $(this);
        const isExpanded = $this.attr('aria-expanded') === 'true';
        const $target = $('#' + $this.attr('aria-controls'));
        const $parent = $this.closest('.bylaws_item');
        const $allItems = $('.accordion_trigger');
        const $allContents = $('.con');
        const $allParents = $('.bylaws_item');

        if (!isExpanded) {
            $allItems.attr('aria-expanded', 'false');
            $allContents.prop('hidden', true).slideUp();
            $allParents.removeClass('on');

            $this.attr('aria-expanded', 'true');
            $target.prop('hidden', false).slideDown();
            $parent.addClass('on');
        } else {
            $this.attr('aria-expanded', 'false');
            $parent.removeClass('on');
            $target.slideUp(function() {
                $(this).prop('hidden', true);
            });
        }
    });
});