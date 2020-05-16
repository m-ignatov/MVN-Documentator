const onFormSubmitted = event => {
    event.preventDefault();

    const formElement = event.target;

    const formData = {
        firstName: formElement.querySelector("input[name='firstName']").value,
        lastName: formElement.querySelector("input[name='lastName']").value,
        courseYear: formElement.querySelector("input[name='courseYear']").value,
        courseName: formElement.querySelector("input[name='courseName']").value,
        facultyNumber: formElement.querySelector("input[name='facultyNumber']").value,
        groupNumber: formElement.querySelector("input[name='groupNumber']").value,
        birthday: formElement.querySelector("input[name='birthday']").value,
        zodiac: formElement.querySelector("label[id='zodiacLabel']").innerText,
        link: formElement.querySelector("input[name='link']").value,
        photo: formElement.querySelector("input[name='photo']").value,
        motivation: formElement.querySelector("textarea[name='motivation']").value,
        signature: formElement.querySelector("canvas[id='signature-pad']").toDataURL('image/png'),
    };

    fetch('./endpoints/register.php', {
        method: 'POST',
        body: JSON.stringify(formData),
    })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                window.location.replace("../success.html");
            } else {
                alert(response.message);
            }
        });
};

document.getElementById('registerForm').addEventListener('submit', onFormSubmitted);