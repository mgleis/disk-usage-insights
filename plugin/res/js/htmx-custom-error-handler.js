htmx.on('htmx:responseError', function (evt) {
    // 403=Forbidden
    console.log(evt.detail.xhr.response);
    console.log(evt.detail.xhr.responseText);
    alert('Received error from server (code=' + evt.detail.xhr.status + ') - see console');
});
htmx.on('htmx:sendError', function (evt) {
    alert('Could not send data to server');
});
