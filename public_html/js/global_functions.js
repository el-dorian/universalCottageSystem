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
    // проверю, не является ли ссылка на арртибуты ссылкой на форму
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
            checkMessages();
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