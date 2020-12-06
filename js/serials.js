$('#newSerial').on('keypress', function (e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        e.preventDefault();
        addNewSerial();
        return false;
    }
});

$('#addSerialButton').on('click', function (e) {
    e.preventDefault();
    addNewSerial();
    return false;
});

function addNewSerial() {
    var serial = $('#newSerial').val();
    if(serial.length === 0) return;
    var html = $('#chipHtml').html();
    html = html.replace(/\[\[number\]\]/g, serial);
    $('#serials').append(html);
    $('#newSerial').val('');
}
