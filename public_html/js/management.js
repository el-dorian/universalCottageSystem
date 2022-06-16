// навигация по табам
function enableTabNavigation() {
    let url = location.href.replace(/\/$/, "");
    if (location.hash) {
        const hash = url.split("#");
        $('a[href="#' + hash[1] + '"]').tab("show");
        url = location.href.replace(/\/#/, "#");
        history.replaceState(null, null, url);
    }

    $('a[data-toggle="tab"]').on("click", function () {
        let newUrl;
        const hash = $(this).attr("href");
        if (hash === "#home") {
            newUrl = url.split("#")[0];
        } else {
            newUrl = url.split("#")[0] + hash;
        }
        history.replaceState(null, null, newUrl);
    });
}

$(function () {
    "use strict";
    enableTabNavigation();

    // сохранение настроек почты
    let mailSettingsForm = $('form#mail-preferences-form');
    mailSettingsForm.on('submit', function (e) {
        e.preventDefault();
        sendAjax('post',
            '/edit-settings/mail-settings',
            simpleActionHandler,
            mailSettingsForm,
            true);
    });

    // сохранение базовых настроек
    let baseSettingsForm = $('form#base-preferences-form');
    baseSettingsForm.on('submit', function (e) {
        e.preventDefault();
        sendAjax('post',
            '/edit-settings/base-settings',
            simpleActionHandler,
            baseSettingsForm,
            true);
    });

    // сохранение настроек базы данных
    let dbSettingsForm = $('form#db-preferences-form');
    dbSettingsForm.on('submit', function (e) {
        e.preventDefault();
        sendAjax('post',
            '/edit-settings/db-settings',
            simpleActionHandler,
            dbSettingsForm,
            true);
    });

    // сохранение настроек банка
    let bankSettingsForm = $('form#bank-preferences-form');
    bankSettingsForm.on('submit', function (e) {
        e.preventDefault();
        sendAjax('post',
            '/edit-settings/bank-settings',
            simpleActionHandler,
            bankSettingsForm,
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
            $('input#restore-db-input').click()
        });

    // при выборе файлов базы данных- восстановлю бекап
    let registryInput = $('#restore-db-input');
    registryInput.on('change.send', function () {
        if ($(this).val()) {
            $(this).parents('form').trigger('submit');
        }
    });
});