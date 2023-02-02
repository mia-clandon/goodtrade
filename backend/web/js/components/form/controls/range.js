/**
 * class RangeControl
 * @author Артём Широких kowapssupport@gmail.com
 */
class RangeControl {

    /**
     * @return {RangeControl}
     */
    initSlider() {

        let slider = this.getSlider()
            ,th = this
            ,$hidden_control_start = this.range_wrapper.find('input[type=hidden].range-start')
            ,$hidden_control_end = this.range_wrapper.find('input[type=hidden].range-end')
        ;

        let data = $.extend(slider.data(), {
            slide: function(event, ui) {
                // двойной диапазон.
                if (th.is_double) {
                    $hidden_control_start.val(ui.values[0]);
                    $hidden_control_end.val(ui.values[1]);
                    th.range_wrapper.find('span.value').text(ui.values[0] + ' - ' + ui.values[1]);
                }
                // один ползунок.
                else {
                    $hidden_control_start.val(ui.value);
                    $hidden_control_end.val(ui.value);
                    th.range_wrapper.find('span.value').text(ui.value);
                }
            },
        });

        if (th.is_double) {
            data.value = undefined;
            data.values = [$hidden_control_start.val(), $hidden_control_end.val()];
        }
        else {
            data.values = undefined;
            data.value = $hidden_control_start.val();
        }

        slider.slider(data);
        return this;
    }

    destroySlider() {
        this.getSlider().slider('destroy');
        return this;
    }

    getSlider() {
        return this.range_wrapper.find('.range-control');
    }

    initEvents() {
        let th = this;
        this.is_double = $('input[name=is_double]', this.range_wrapper).is(':checked');
        this.initSlider();

        $('input[name=is_double]', this.range_wrapper).click(function() {
            th.is_double = $(this).is(':checked');
            th.destroySlider();
            th.initSlider();
        });
    }

    isDouble() {
        return this.is_double;
    }

    /**
     * Инициализация.
     * @param $range_wrapper
     * @return {RangeControl}
     */
    init($range_wrapper) {
        this.range_wrapper = $range_wrapper;
        this.initEvents();
        return this;
    }
}

$(function () {
    $('.range-wrapper').each(function() {
        new RangeControl().init($(this));
    });
});