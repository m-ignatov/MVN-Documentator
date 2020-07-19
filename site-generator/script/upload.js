const onFormSubmitted = event => {
    event.preventDefault();

    const generateButton = document.getElementById('generateButton');
    generateButton.disabled = true;

    const fileInputName = 'dataFile';
    const file = document.getElementById(fileInputName).files[0];

    const languageSelect = document.getElementById('languageSelect');
    const selectedLanguage = languageSelect.options[languageSelect.selectedIndex].value;

    const folderName = document.getElementById('foldername').value;

    if (!file) {
        window.alert("No file chosen");
        return;
    }

    if (activeOption == -1) {
        window.alert("No theme chosen");
        return;
    }

    const formData = new FormData();
    formData.append(fileInputName, file);
    formData.append("chosenTheme", activeOption); //from generateContent.js takes the activeOption var which indicates the choosen theme 
    formData.append("language", selectedLanguage);
    formData.append("folderName", folderName);
    generateButton.innerText = 'GENERATING...';

    fetch('./endpoints/upload.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                window.open('maven/target/' + folderName + '/index.html');
            } else {
                alert(response.message);
            }
            generateButton.innerText = "GENERATE";
            generateButton.disabled = false;
        });
};

document.getElementById('generateForm').addEventListener('submit', onFormSubmitted);

document.getElementById('upload').addEventListener('change', function() {
    const fileInputName = 'dataFile';
    const file = document.getElementById(fileInputName).files[0];
    document.getElementById('uploadLabel').innerHTML = file.name;
});