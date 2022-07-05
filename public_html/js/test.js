$(function () {

});
function drawModal(title, body) {
    $(function () {
        let modal = $('<div class="modal fade show" tabindex="-1">\n' +
            '  <div class="modal-dialog modal-xl">\n' +
            '    <div class="modal-content">\n' +
            '      <div class="modal-header">\n' +
            '        <h5 class="modal-title">' + title + '</h5>\n' +
            '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>\n' +
            '      </div>\n' +
            '      <div class="modal-body">\n' +
            '        ' + body + '\n' +
            '      </div>\n' +
            '      <div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>\n' +
            '        <button type="button" class="btn btn-primary">Сохранить</button>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div>');
        $('body').append(modal);
        modal.modal({
            keyboard: false,
            backdrop: 'static',
            show: true
        });
    });
}