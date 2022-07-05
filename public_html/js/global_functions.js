// noinspection JSValidateTypes,JSUnresolvedFunction

function dangerReload() {
    $(window).on('beforeunload.message', function () {
        return "Необходимо заполнить все поля на странице!";
    });
}

function appendModalForm(answer) {
    if (answer['status'] === 1) {
        makeModal(answer.title, answer.view, answer.delay);
    } else if (answer['status'] === 0) {
        if (answer['message']) {
            makeInformer("warning", "Неудача", answer['message'])
        } else {
            makeInformer("warning", "Неудача", "Произошла ошибка при выполнении операции. Попробуйте ещё раз.")
        }
    } else {
        makeInformer("danger", "Ошибка", "Невозможно выполнить операцию. Ошибка на стороне сервера.")
    }
}

function showAlert(alertDiv) {
    // считаю расстояние от верха страницы до места, где располагается информер
    const topShift = alertDiv[0].offsetTop;
    const elemHeight = alertDiv[0].offsetHeight;
    let shift = topShift + elemHeight;
    alertDiv.css({'top': -shift + 'px', 'opacity': '0.1'});
    // анимирую появление информера
    alertDiv.animate({
        top: 0,
        opacity: 1
    }, 500, function () {
        // запускаю таймер самоуничтожения через 5 секунд
        setTimeout(function () {
            closeAlert(alertDiv)
        }, 300000);
    });

}

function closeAlert(alertDiv) {
    const elemWidth = alertDiv[0].offsetWidth;
    alertDiv.animate({
        left: elemWidth
    }, 500, function () {
        alertDiv.animate({
            height: 0,
            opacity: 0
        }, 300, function () {
            alertDiv.remove();
        });
    });
}

function makeInformer(type, header, body) {
    if (!body)
        body = '';
    const container = $('div#notifications_div');
    const informer = $('' +
        '<div class="alert-wrapper">' +
        '<div class="alert alert-' + type + ' alert-dismissable my-alert">' +
        '<div class="card card-' + type + '">' +
        '<div class="card-body">' +
        '<h5 class="card-title">' +
        header +
        '</h5><button type="button" class="close">&times;</button>' +
        '<p class="card-text">' +
        body +
        '</p>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '');
    informer.find('button.close').on('click.hide', function (e) {
        e.preventDefault();
        closeAlert(informer)
    });
    container.append(informer);
    showAlert(informer)
}


// Функция вызова пустого модального окна
function makeModal(header, text, delayed) {
    text = text.replaceAll("glyphicon", "fa");
    text = text.replaceAll("fa-duplicate", "fa-clone");
    if (delayed) {
        // открытие модали поверх другой модали
        let modal = $("div.modal");
        modal.off('hidden.bs.modal');
        if (modal.length === 1) {
            modal.modal('hide');
            let newModal = $('<div id="myModal" class="modal fade"><div class="modal-dialog modal-xl"><div class="modal-content"><div class="modal-header">' + header + '</div><div class="modal-body">' + text + '</div><div class="modal-footer"><button class="btn btn-danger"  data-dismiss="modal" type="button" id="cancelActionButton">Отмена</button></div></div></div>');
            modal.on('hidden.bs.modal', function () {
                modal.remove();
                if (!text)
                    text = '';
                $('body').append(newModal);
                dangerReload();
                newModal.modal({
                    keyboard: true,
                    show: true
                });
                newModal.on('shown.bs.modal', function () {
                    $('body').css({'overflow': 'hidden'});
                    $('div.wrap div.container, div.wrap nav').addClass('blured');
                });
                newModal.on('hidden.bs.modal', function () {
                    normalReload();
                    newModal.remove();
                    $('body').css({'overflow': 'auto'});
                    $('div.wrap div.container, div.wrap nav').removeClass('blured');
                });
                $('div.wrap div.container, div.wrap nav').addClass('blured');
            });
            handleAjaxFormTriggers();
            return newModal;
        }
    }
    if (!text)
        text = '';
    let modal = $('<div id="myModal" class="modal fade"><div class="modal-dialog modal-xl"><div class="modal-content"><div class="modal-header">' + header + '</div><div class="modal-body">' + text + '</div><div class="modal-footer"><button class="btn btn-danger"  data-dismiss="modal" type="button" id="cancelActionButton">Отмена</button></div></div></div>');
    $('body').append(modal);
    dangerReload();
    modal.modal({
        keyboard: true,
        show: true
    });
    modal.on('hidden.bs.modal', function () {
        normalReload();
        modal.remove();
        $('div.wrap div.container, div.wrap nav').removeClass('blured');
    });
    $('div.wrap div.container, div.wrap nav').addClass('blured');
    modal.find('form').on('submit', function () {
        normalReload();
    })
    handleAjaxFormTriggers();
    return modal;
}

function showWaiter() {
    let shader = $('<div class="shader"></div>');
    $('body').append(shader).css({'overflow': 'hidden'});

    $('div.wrap, div.flyingSum, div.modal').addClass('blured');
    shader.showLoading();
}

function deleteWaiter() {
    $('div.wrap, div.flyingSum, div.modal').removeClass('blured');
    $('body').css({'overflow': ''});
    let shader = $('div.shader');
    if (shader.length > 0)
        shader.hideLoading().remove();
}


function normalReload() {
    $(window).off('beforeunload');
}

function ajaxDangerReload() {
    $(window).on('beforeunload.ajax', function () {
        return "Необходимо заполнить все поля на странице!";
    });
}

function ajaxNormalReload() {
    $(window).off('beforeunload.ajax');
}

function serialize(obj) {
    const str = [];
    for (let p in obj)
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
    return str.join("&");
}

function sendAjax(method, url, callback, attributes, isForm) {
    showWaiter();
    ajaxDangerReload();
    // проверю, не является ли ссылка на аттрибуты ссылкой на форму
    if (attributes && attributes instanceof jQuery && attributes.is('form')) {
        attributes = attributes.serialize();
    } else if (isForm) {
        attributes = $(attributes).serialize();
    } else {
        attributes = serialize(attributes);
    }
    if (method === 'get') {
        $.ajax({
            method: method,
            data: attributes,
            url: url
        }).done(function (e) {
            deleteWaiter();
            ajaxNormalReload();
            callback(e);
        }).fail(function (e) {// noinspection JSUnresolvedVariable
            ajaxNormalReload();
            deleteWaiter();
            if (e.responseJSON) {// noinspection JSUnresolvedVariable
                makeInformer('danger', 'Системная ошибка', e.responseJSON['message']);
            } else {
                makeInformer('info', 'Ответ системы', e.responseText);
                console.log(e);
            }
            //callback(false)
        });
    } else if (method === 'post') {
        $.ajax({
            data: attributes,
            method: method,
            url: url
        }).done(function (e) {
            deleteWaiter();
            normalReload();
            callback(e);
        }).fail(function (e) {// noinspection JSUnresolvedVariable
            deleteWaiter();
            normalReload();
            if (e['responseJSON']) {// noinspection JSUnresolvedVariable
                makeInformer('danger', 'Системная ошибка', e.responseJSON.message);
            } else {
                makeInformer('info', 'Ответ системы', e.responseText);
            }
            //callback(false)
        });
    }
}

function simpleActionHandler(answer) {
    if (answer['status'] === 1) {
        makeInformer("success", "Успех", "Операция завершена успешно.")
    } else if (answer['status'] === 0) {
        if (answer['message']) {
            makeInformer("warning", "Неудача", answer['message'])
        } else {
            makeInformer("warning", "Неудача", "Произошла ошибка при выполнении операции. Попробуйте ещё раз.")
        }
    } else {
        makeInformer("danger", "Ошибка", "Невозможно выполнить операцию. Ошибка на стороне сервера.")
    }
}

function modalActionHandler(answer) {
    if (answer['status'] === 1) {
        if (answer.reload) {
            makeInformerModal('Успех', answer.message)
        } else {
            makeInformerModal('Успех', answer.message, function () {
            })
        }
    } else if (answer['status'] === 0) {
        if (answer['message']) {
            makeInformer("warning", "Неудача", answer['message'])
        } else {
            makeInformer("warning", "Неудача", "Произошла ошибка при выполнении операции. Попробуйте ещё раз.")
        }
    } else {
        makeInformer("danger", "Ошибка", "Невозможно выполнить операцию. Ошибка на стороне сервера.")
    }
}

function handleAjaxFormTriggers() {
    let triggers = $('.ajax-form-trigger');
    triggers.off('click.request-form');
    triggers.on('click.request-form', function (event) {
        event.preventDefault();
        event.stopPropagation()
        sendAjax(
            'get',
            $(this).attr('data-action'),
            appendModalForm
        )
    });
}

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

function makeInformerModal(header, text, acceptAction, declineAction) {
    if (!text)
        text = '';
    let modal = $('<div class="modal fade mode-choose"><div class="modal-dialog text-center"><div class="modal-content"><div class="modal-header"><h3>' + header + '</h3></div><div class="modal-body">' + text + '</div><div class="modal-footer"><button class="btn btn-success" type="button" id="acceptActionBtn">Ок</button></div></div></div>');
    $('body').append(modal);

    let acceptButton = modal.find('button#acceptActionBtn');
    if (declineAction) {
        let declineBtn = $('<button class="btn btn-warning" role="button">Отмена</button>');
        declineBtn.insertAfter(acceptButton);
        declineBtn.on('click.custom', function () {
            normalReload();
            modal.modal('hide');
            declineAction();
        });
    }
    dangerReload();
    modal.modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    modal.on('hidden.bs.modal', function () {
        normalReload();
        modal.remove();
        $('div.wrap div.container, div.wrap nav').removeClass('blured');
    });
    modal.on('shown.bs.modal', function () {
        acceptButton.focus();
    });
    $('div.wrap div.container, div.wrap nav').addClass('blured');

    acceptButton.on('click', function () {
        normalReload();
        modal.modal('hide');
        if (acceptAction) {
            acceptAction();
        } else {
            location.reload();
        }
    });

    return modal;
}

function handlePromiseTriggers() {
    let triggers = $('.ajax-promise-trigger');
    triggers.on('click.showPromise', function () {
        let trigger = $(this);
        makeInformerModal("Подтвердите действие", $(this).attr('data-promise'), function () {
            sendAjax('post', trigger.attr('data-action'), modalActionHandler)
        }, function () {
        })
    });
}

function handleTooltips(){
    $('.tooltip-enabled').tooltip({'trigger': 'hover'});
}