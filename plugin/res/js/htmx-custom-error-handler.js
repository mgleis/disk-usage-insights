function dui_escapeHTML(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}
function dui_handleHtmxError(title, msg) {
    alert('Oops, an unexpected error occurred, see error area for details.');
    document.getElementById('dui_errors').style.display = 'block';
    document.getElementById('dui_errors_content').innerHTML += dui_escapeHTML(title).replace(/\n/g, '<br>') + "<br>\n";
    document.getElementById('dui_errors_content').innerHTML += dui_escapeHTML(msg).replace(/\n/g, '<br>') + "<br>\n";
}

// https://htmx.org/events/#htmx:responseError
htmx.on('htmx:responseError', function (evt) {
    // 403=Forbidden
    dui_handleHtmxError('Response error from server (code=' + evt.detail.xhr.status + ')', evt.detail.xhr.responseText);
});

// https://htmx.org/events/#htmx:sendError
htmx.on('htmx:sendError', function (evt) {
    dui_handleHtmxError('Network send error: could not send data to server', '');
});

htmx.on('htmx:onLoadError', function (evt) {
    dui_handleHtmxError('onLoadError', evt);
});
htmx.on('htmx:oobErrorNoTarget', function (evt) {
    dui_handleHtmxError('oobErrorNoTarget', evt);
});
htmx.on('htmx:sseError', function (evt) {
    dui_handleHtmxError('sseError', evt);
});
htmx.on('htmx:swapError', function (evt) {
    dui_handleHtmxError('swapError', evt);
});
htmx.on('htmx:targetError', function (evt) {
    dui_handleHtmxError('targetError', evt);
});
htmx.on('htmx:historyCacheError', function (evt) {
    dui_handleHtmxError('historyCacheError', evt);
});
htmx.on('htmx:historyCacheMissLoadError', function (evt) {
    dui_handleHtmxError('historyCacheMissLoadError', evt);
});
