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

                  localStorage.setItem('userId', response.id);
                    
                    // 2. Alert and Redirect to the account page
                    alert("Успешно влизане в акаунт");
                    a.classList.remove('d-none');
                   
                } else {
                    alert("Грешка: " + response.message);
                }
            }
        });
    });
});

