function getZodiac(e) {

    let date = new Date(e.target.value);

    let day = date.getDate();
    let month = date.getMonth();

    let astro_sign;

    if (month == 0) {
        if (day < 20)
            astro_sign = "Козирог";
        else
            astro_sign = "Водолей";
    }

    else if (month == 1) {
        if (day < 19)
            astro_sign = "Водолей";
        else
            astro_sign = "Риби";
    }

    else if (month == 2) {
        if (day < 21)
            astro_sign = "Риби";
        else
            astro_sign = "Овен";
    }
    else if (month == 3) {
        if (day < 20)
            astro_sign = "Овен";
        else
            astro_sign = "Телец";
    }

    else if (month == 4) {
        if (day < 21)
            astro_sign = "Телец";
        else
            astro_sign = "Близнаци";
    }

    else if (month == 5) {
        if (day < 21)
            astro_sign = "Близнаци";
        else
            astro_sign = "Рак";
    }

    else if (month == 6) {
        if (day < 23)
            astro_sign = "Рак";
        else
            astro_sign = "Лъв";
    }

    else if (month == 7) {
        if (day < 23)
            astro_sign = "Лъв";
        else
            astro_sign = "Дева";
    }

    else if (month == 8) {
        if (day < 23)
            astro_sign = "Дева";
        else
            astro_sign = "Везни";
    }

    else if (month == 9) {
        if (day < 23)
            astro_sign = "Везни";
        else
            astro_sign = "Скорпион";
    }

    else if (month == 10) {
        if (day < 22)
            astro_sign = "Скорпион";
        else
            astro_sign = "Стрелец";
    }

    else if (month == 11) {
        if (day < 22)
            astro_sign = "Стрелец";
        else
            astro_sign = "Козирог";
    }

    document.getElementById("zodiacLabel").innerText = astro_sign;
}