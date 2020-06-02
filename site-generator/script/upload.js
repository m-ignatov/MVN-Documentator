const onFormSubmitted = event => {
    event.preventDefault();

    const generateButton = document.getElementById('generateButton');
    generateButton.disabled = true;

    const fileInputName = 'dataFile';
    const file = document.getElementById(fileInputName).files[0];
    const resultLabel = document.getElementById('result');

    if (!file) {
        resultLabel.innerText = 'No file uploaded';
        return;
    }

    const formData = new FormData();
    formData.append(fileInputName, file);

    resultLabel.innerText = 'Generating...';

    fetch('./endpoints/upload.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                window.open('maven/target/site/index.html');
            }
            resultLabel.innerText = response.message;
            generateButton.disabled = false;
        });
};

document.getElementById('generateForm').addEventListener('submit', onFormSubmitted);