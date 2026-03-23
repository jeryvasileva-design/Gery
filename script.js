/*function Rgs (){
    $('#registation').submit(function (e) {
        e.preventDefault(); //kakvoto i da stava ne it davam default

        $.ajax({
            type: 'POST',
            url: 'php/Registration.php',
            data: $('registation').serialize(),
            success: function (response){
                $('message').html('<div class = "alert alert-seccess">' + response + '<div/>');
                $('registation')[0].reset();
                alert("Successful Registration");
            },
            error: function(xhr, status, error){
                $('#message').html('<div class = "alert alert-danger">Error:' + xhr.responseText+ '</div>');
            }
        });
    });
}*/

$(document).ready(function () {
    $('#registation').submit(function (e) {
        e.preventDefault();


        var form = $(this);

        $.ajax({
            type: 'POST',
            url: 'php/Registration.php',
            data: form.serialize(),
            success: function (response) {
                console.log("Server response: " + response);

                if (response.trim() === 'Success') {

                    form[0].reset();

                    alert("Успешна регистрация!");

                    window.location.href = 'index.html';
                } else {
                    alert("Грешка от сървъра: " + response);
                }
            },
            error: function (xhr) {
                alert("Грешка при връзката!");
            }
        });
    });
});

$(document).ready(function () {
    let a = document.getElementById('acc');
    $('#login').submit(function (e) {
        e.preventDefault();
        
        var login = $(this);

        $.ajax({
            type: 'POST',
            url: 'php/Login.php',
            data: login.serialize(),
            success: function (response) {
                console.log("Server response: ", response);



                if (response.status === 'Success') {
                    localStorage.setItem('userId', response.id);
                    a.classList.remove('d-none');

                    alert("Успешно влизане в акаунт");
                    window.location.href = 'account.html';
                } else {
                    alert("Грешка: " + response.message);
                }
            }
        });
    });
});

