const deleteTexto = (event, id) => {
    event.preventDefault();
    if (confirm(msg_delete)) {
        $.ajax({
            url: url_ajax,
            data: {
                action : 'deleteTexto',
                id
            },
            success: function (response) {
                if (typeof response.error !== 'undefined') {
                    showError(response.error)
                }
            }
        });
    }
}

const showError = (message) => {
    $.growl.error({
        title: growl.error,
        message: message
    })
}

const showSuccess = (message) => {
    $.growl.notice({
        title: growl.notice,
        message: message
    })
}