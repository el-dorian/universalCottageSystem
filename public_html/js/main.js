$(function () {
    "use strict";

    let emptyCottages = $('button.empty');
    emptyCottages.tooltip({'trigger': 'hover'});
    emptyCottages.hover(
        function () {
            $(this).text('').addClass('glyphicon glyphicon-plus');
        }, function () {
            $(this).text($(this).attr('data-index')).removeClass('glyphicon glyphicon-plus');
        });
    emptyCottages.on('click.loadAddCottageForm', function () {
        sendAjax('get', '/form/add-cottage/' + $(this).attr('data-index'), appendModalForm);
    });
});