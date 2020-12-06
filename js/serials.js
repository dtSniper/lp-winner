$('#newSerial').on('keypress', function (e) {
    var keyCode = e.keyCode || e.which;
    console.log(keyCode);
    if (keyCode === 13 || keyCode === 32 || keyCode === 44) {
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
    $('#newSerial').parent().removeClass('has-error');
}

$(document)
    .ready(function () {
        $("form#serialAdd")
            .submit(function (event) {
                if ($('form input[name="serials[]"]').length === 0) {
                    event.preventDefault();
                    $('#newSerial').parent().addClass('has-error');
                    return false;
                }
            });
    });