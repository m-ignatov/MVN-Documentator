document.addEventListener("DOMContentLoaded", function(event) {
    const container = document.getElementById("themes-container");
    console.log(container);

    const images = ["brisk.png", "compote.png", "condiments.png", "coral.png", "green.png", "harbour.png", "harvest.png", "marsala.png", "pebble.png", "scholar.png", "sky.png", "uncorked.png"];
    const names = ["Brisk", "Compote", "Condiments", "Coral", "Green", "Harbour", "Harvest", "Marsala", "Pebble", "Scholar", "Sky", "Uncorked"];

    function makeRows(rows, cols) {
        container.style.setProperty('--grid-rows', rows);
        container.style.setProperty('--grid-cols', cols);

        for (i = 0; i < (rows * cols); i++) {
            let themeDiv = document.createElement('div');
            themeDiv.className = 'theme-option';
            /* if (i == 0) {
                 themeDiv.className = 'selected-option';
             }*/

            let themeImg = document.createElement('img');
            themeImg.src = "img/themes/" + images[i];
            console.log(themeImg.src);
            themeDiv.appendChild(themeImg);

            let themeName = document.createElement('p');
            let text = document.createTextNode(names[i].toUpperCase());
            themeName.appendChild(text);
            themeDiv.appendChild(themeName);
            container.appendChild(themeDiv);
        };
    };

    makeRows(3, 4);
});

var activeOption = -1;

function setActiveOption() {
    var themes = document.getElementsByClassName('theme-option');
    var themesCount = themes.length;
    for (let i = 0; i < themesCount; i++) {
        themes[i].addEventListener('click', function() {
            if (activeOption != -1) {
                themes[activeOption].classList.remove("selected-option");
            }
            activeOption = i;
            console.log(activeOption);
            themes[activeOption].classList.add("selected-option");

        });
    }
}