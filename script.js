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
    // Използваме клас и ID, за да хванем бутона и в двете менюта
    $(document).on('click', '#logoutBtn, .logoutBtn', function (e) {
        e.preventDefault();
        localStorage.removeItem('userId');
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

    // === 5. ЗАПАЗВАНЕ НА ЧАС ===
    if ($('#userIdInput').length > 0) {
        $('#userIdInput').val(localStorage.getItem('userId'));
    }

    $('#eventForm').submit(function (e) {
        e.preventDefault();
        const userId = localStorage.getItem('userId');
        if (!userId) {
            alert("Трябва да сте влезли, за да запазите час.");
            window.location.href = 'login.html';
            return;
        }
        $.ajax({
            type: 'POST',
            url: 'php/SaveHour.php',
            data: $(this).serialize() + "&userId=" + userId,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'Success') {
                    alert("Часът е запазен успешно!");
                    window.location.href = 'account.html';
                } else {
                    alert("Грешка: " + response.message);
                }
            }
        });
    });
});