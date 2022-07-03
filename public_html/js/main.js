$(function () {
    "use strict";
    handleAjaxFormTriggers();
    let emptyCottages = $('button.empty');
    emptyCottages.tooltip({'trigger': 'hover'});
    emptyCottages.hover(
        function () {
            $(this).html('<i class="fa fa-plus"></i>');
        }, function () {
            $(this).text($(this).attr('data-index'));
        });
    emptyCottages.on('click.loadAddCottageForm', function () {
        sendAjax('get', '/form/add-cottage/' + $(this).attr('data-index'), appendModalForm);
    });
});