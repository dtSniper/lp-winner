// non-global functions
$.extend({
    redirectPost(location, args) {
        var $form = $('<form>')
        $form.attr('method', 'post')
        $form.attr('action', location)

        $.each(args, function (key, value) {
            var $field = $('<input>')

            $field.attr('type', 'hidden')
            $field.attr('name', key)
            $field.attr('value', value)

            $form.append($field)
        })

        $form.appendTo('body')
            .submit()
    },
});