/**
 * class CopyControl
 */
class CopyControl {

    constructor($control) {
        this.control = $control;
        this.max_clone_count = $control.data('max-clone-count');
    }

    /**
     * Клонирует элементы.
     * @param $clone_button
     * @return {CopyControl}
     */
    clone($clone_button) {
        let $table = $clone_button.parents('div.copy-control:first').find('table')
            ,$cloned_item = $table.find('tr:last').clone()
            ,count_items = $table.find('tr').length;

        if (count_items >= this.max_clone_count) {
            return this;
        }

        $cloned_item.find('button')
            .removeClass('btn-clone')
            .addClass('btn-remove-clone')
            .find('span')
            .removeClass('glyphicon-plus')
            .addClass('glyphicon-minus');

        $cloned_item.find('.help-block')
            .html('');

        $table.append('<tr>' + $cloned_item.html() + '</tr>');
        return this;
    }

    removeCloned($remove_clone_button) {
        $remove_clone_button.parents('tr:first').remove();
        return this;
    }

    init() {
        let th = this;
        this.control.on('click', 'button.btn-clone', function() {
            th.clone($(this));
        });
        this.control.on('click', 'button.btn-remove-clone', function() {
            th.removeCloned($(this));
        });
    }
}

$(function () {
    $('div.copy-control').each(function() {
        new CopyControl($(this)).init();
    });
});