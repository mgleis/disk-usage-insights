htmx.on('htmx:responseError', function (evt) {
    alert('Received error from server (code=' + evt.detail.xhr.status + ')');
    // 403=Forbidden
});
htmx.on('htmx:sendError', function (evt) {
    alert('Could not send data to server');
});
