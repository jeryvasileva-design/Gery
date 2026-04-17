$(document).ready(function () {
//nav bar
    function updateNavigation() {

        let userId = localStorage.getItem('userId');
        const isLoggedIn = userId && userId !== "null" && userId !== "undefined" && userId !== "";

        const profileLinks = $('#acc, .nav-acc');
        const saveHourLinks = $('#save, .nav-save');
        const logoutBtns = $('#logoutBtn, .logoutBtn');

        const registrationLinks = $('a[href="login.html"]').parent();

        if (isLoggedIn) {
        
            profileLinks.removeClass('d-none').show();
            saveHourLinks.removeClass('d-none').show();
            logoutBtns.removeClass('d-none').show();
            registrationLinks.addClass('d-none').hide();
        } else {
          
            profileLinks.addClass('d-none').hide();
            saveHourLinks.addClass('d-none').hide();
            logoutBtns.addClass('d-none').hide();
            registrationLinks.removeClass('d-none').show();
        }
    }

    updateNavigation();

//login
    $('#login').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'php/Login.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'Success') {
                    localStorage.setItem('userId', response.id);
                    localStorage.setItem('userName', response.userName);
                    let userName = localStorage.getItem('userName');


                    window.location.href = 'account.html';

                } else {
                    alert("Грешка: " + response.message);
                }
            },
            error: function () {
                alert("Възникна грешка при свързване със сървъра.");
            }
        });
    });

//logout
    $(document).on('click', '#logoutBtn, .logoutBtn', function (e) {
        e.preventDefault();
        localStorage.clear(); 
        updateNavigation();
        window.location.href = 'index.html';
    });

//registration
    $('#registation').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'php/Registration.php',
            data: $(this).serialize(),
            success: function (response) {
                if (response.trim() === 'Success') {
                    alert("Успешна регистрация!");
                    if (typeof toggleFlip === "function") toggleFlip();
                } else {
                    alert("Грешка: " + response);
                }
            }
        });
    });

//saveHour
    $('#eventForm').submit(function (e) {
        e.preventDefault();
        const userId = localStorage.getItem('userId');

        if (!userId) {
            alert("Трябва да сте влезли в профила си.");
            window.location.href = 'login.html';
            return;
        }

        $('#userIdInput').val(userId);

        let formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'php/SaveHour.php',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'Success') {
                    alert("Часът е запазен успешно!");
                    window.location.href = 'account.html';
                } else {
                    alert("Грешка: " + response.message);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert("Техническа грешка при записването.");
            }
        });
    });
});