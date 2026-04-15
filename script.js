$(document).ready(function () {

    // === 1. ФУНКЦИЯ ЗА УПРАВЛЕНИЕ НА ВИДИМОСТТА ===
    function updateNavigation() {
        let userId = localStorage.getItem('userId');
        const isLoggedIn = userId && userId !== "null" && userId !== "undefined" && userId !== "";

        // Селектираме елементите едновременно по ID и по КЛАС
        const profileLinks = $('#acc, .nav-acc');
        const saveHourLinks = $('#save, .nav-save');
        const logoutBtns = $('#logoutBtn, .logoutBtn');

        // Намираме всички линкове за Регистрация (<a> сочи към login.html)
        const registrationLinks = $('a[href="login.html"]').parent();

        if (isLoggedIn) {
            // АКО Е ВЛЯЗЪЛ:
            profileLinks.removeClass('d-none').show();
            saveHourLinks.removeClass('d-none').show();
            logoutBtns.removeClass('d-none').show();
            registrationLinks.addClass('d-none').hide();
        } else {
            // АКО НЕ Е ВЛЯЗЪЛ:
            profileLinks.addClass('d-none').hide();
            saveHourLinks.addClass('d-none').hide();
            logoutBtns.addClass('d-none').hide();
            registrationLinks.removeClass('d-none').show();
        }
    }

    updateNavigation();

    // === 2. ЛОГИКА ЗА ВХОД ===
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

    // === 3. ЛОГИКА ЗА ИЗХОД ===
    $(document).on('click', '#logoutBtn, .logoutBtn', function (e) {
        e.preventDefault();
        localStorage.clear(); // Чисти всичко (userId, userName)
        updateNavigation();
        window.location.href = 'index.html';
    });

    // === 4. ЛОГИКА ЗА РЕГИСТРАЦИЯ ===
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

    // === 5. ЗАПАЗВАНЕ НА ЧАС (SaveHour.html) ===
    $('#eventForm').submit(function (e) {
        e.preventDefault();
        const userId = localStorage.getItem('userId');

        if (!userId) {
            alert("Трябва да сте влезли в профила си.");
            window.location.href = 'login.html';
            return;
        }

        // Поставяме ID-то в скрития input
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
                    window.location.href = 'account.html'; // Пренасочване към профила с календара
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