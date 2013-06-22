$(function() {
    $('#loginform').submit(function(e) {
        e.preventDefault();
        var self = $(this);
        var data = self.serialize();
        $.post(this.action, data, function(res) {
            if(res.success) {
                setCookie('_phone', res.phone, {expires: 86400*7});
                setCookie('_pass', res.pass, {expires: 86400*7});
                location.reload();
            } else {
                self.find('.alert').show();
            }
        }, 'json');
    });
    $('.alert').alert().children('.close').click(function (e) {
        $(this).parent().hide();
    });
    $("#logout").click(function(e) {
        deleteCookie('_phone');
        deleteCookie('_pass');
        location.reload();
    });
    $("#goodadd").submit(function(e) {
        $.post(this.action, $(this).serialize(), function(res) {
            location.reload();
        });
        return false;
    });
    $('.actions').add('table th').tooltip({container: 'body'});
    $('.remove-good').click(function (e) {
        e.preventDefault();
        $.post('/delgood/' + $(this).data('id'), function(res) {
            location.reload();
        });
    });
    $('input.is-going').change(function(e) {
        var going = ~~!!this.checked;
        $.post('/wannago/' + going, {phone: $(this).data('phone')}, function(res) {
            console.log(res);
        });
    });
    $('input.how-paid').keyup(function(e) {
        var self = $(this),
            sum = self.val(),
            phone = self.data('phone');
        $.post('/pay/' + sum, {phone: phone}, function(res) {
            console.log(res);
        });
    });
    $('#passgen').click(function(e) {
        $.get('/passinit', function(res) {
            console.log(res);
        });
    });
    $('#remind').click(function(e) {
       $.get('/payremind', function(res) {
            console.log(res);
        });
    });
});