$('#newSerial').on('keypress', function (e) {
    var keyCode = e.keyCode || e.which;
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
    if(typeof serial === "string" && $('#newSerial').hasClass('allowMpPaste')) {
        serial = serial.replace(/•/g, '');
        var serials = serial.split(' ').filter(function (el) {
            return el != null && el !== "";
        });
        serials.forEach( function (value) {
           addChip(value);
        });
        return;
    }

    if(serial.length === 0) return;
    addChip(serial);
}

function addChip(serial) {
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