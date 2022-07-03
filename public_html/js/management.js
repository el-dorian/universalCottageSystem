$(function () {
    "use strict";
    enableTabNavigation();
    handleAjaxFormTriggers();

    // сохранение настроек почты
    let settingsForms = $('form.preferences-form');
    settingsForms.on('submit.sendAjax', function (e) {
        e.preventDefault();
        sendAjax('post',
            $(this).attr('action'),
            simpleActionHandler,
            this,
            true);
    });

    let backupDbBtn = $('button#backup-db');
    backupDbBtn.on('click.getUpdate',
        function () {
            sendAjax('get', '/misc/make-db-backup', function () {
                // сохраню файл
                let newWindow = window.open('/misc/download-db-backup');
                newWindow.focus();
            });
        });
    let restoreDbBtn = $('button#restore-db');
    restoreDbBtn.on('click.restore',
        function () {
            $('input#restore-db-input').trigger('click')
        });

    // при выборе файлов базы данных- восстановлю бекап
    let registryInput = $('#restore-db-input');
    registryInput.on('change.send', function () {
        if ($(this).val()) {
            $(this).parents('form').trigger('submit');
        }
    });

    let payTargetsTrigger = $('input#basepreferenceseditor-paytarget');
    payTargetsTrigger.on('change.toggle', function () {
        if($(this).prop('checked')){
            $('div#setTargetPaysTriggerContainer').removeClass('d-none');
        }
        else{
            $('div#setTargetPaysTriggerContainer').addClass('d-none');

        }
    })
});