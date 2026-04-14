$(document).ready(function () {

    // === 1. ГЛОБАЛНА ФУНКЦИЯ ЗА НАВИГАЦИЯ И ДОСТЪП ===
    function updateNavigation() {
        let userId = localStorage.getItem('userId');
        const isLoggedIn = userId && userId !== "null" && userId !== "undefined" && userId !== "";

        const profileLink = $('#acc');      
        const saveHourLink = $('#save');    
        const logoutBtn = $('#logoutBtn');  
        
        // ВАЖНО: Селектираме само линка в навигацията
        const navLoginLink = $('nav a[href="login.html"]').parent();

        if (isLoggedIn) {
            profileLink.removeClass('d-none').show();
            saveHourLink.removeClass('d-none').show();
            logoutBtn.removeClass('d-none').show();
            navLoginLink.hide(); // Скрива само "Регистрация" в менюто
        } else {
            profileLink.addClass('d-none').hide();
            saveHourLink.addClass('d-none').hide();
            logoutBtn.addClass('d-none').hide();
            navLoginLink.show(); // Показва го, ако не е логнат
        }
    }
    // Извикваме навигацията веднага при зареждане
    updateNavigation();

    // === 2. ЛОГИКА ЗА ВХОД (LOGIN) ===
    $('#login').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'php/Login.php',
            data: $(this).serialize(),
            dataType: 'json', // Очакваме JSON отговор от сървъра
            success: function (response) {
                if (response.status === 'Success') {
                    localStorage.setItem('userId', response.id); // Записваме ID-то
                    window.location.href = 'account.html'; // Препращаме към профила
                } else {
                    alert("Грешка: " + response.message);
                }
            },
            error: function() {
                alert("Грешка при връзка със сървъра.");
            }
        });
    });

    // === 3. ЛОГИКА ЗА ИЗХОД (LOGOUT) ===
    $(document).on('click', '#logoutBtn', function (e) {
        e.preventDefault();
        localStorage.removeItem('userId'); // Изтриваме ID-то
        window.location.href = 'index.html'; // Връщаме в началото
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
                    alert("Успешна регистрация! Вече можете да влезете.");
                    if (typeof toggleFlip === "function") toggleFlip(); // Превключваме към формата за вход
                } else {
                    alert("Грешка: " + response);
                }
            }
        });
    });

    // === 5. ЛОГИКА ЗА ЗАПАЗВАНЕ НА ЧАС ===
    // Попълваме скритото ID в полето, ако съществува
    if ($('#userIdInput').length > 0) {
        $('#userIdInput').val(localStorage.getItem('userId'));
    }

    $('#eventForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'php/SaveHour.php',
            data: $(this).serialize(),
            success: function (response) {
                if (response.status === 'Success') {
                    alert("Часът е запазен успешно!");
                    window.location.href = 'account.html';
                } else {
                    alert("Грешка при запис: " + response.message);
                }
            }
        });
    });
});