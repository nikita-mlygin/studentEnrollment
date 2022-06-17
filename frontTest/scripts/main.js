window.onload = () => {
    for (let iterator of document.querySelectorAll('.formNavbar__elm')) {
        iterator.addEventListener(
            'click',
            (e) => {
                let line = e.currentTarget.querySelector('.formNavbar__line_active');

                if (line.classList.contains('formNavbar__line_active_0')) {
                    line.classList.remove('formNavbar__line_active_0');
                    line.offsetHeight;
                    line.classList.add('formNavbar__line_active_1');
                } else {
                    line.classList.remove('formNavbar__line_active_1');
                    line.offsetHeight;
                    line.classList.add('formNavbar__line_active_0');
                }
            }
        );
    }

    buildMyInput();
}

function buildMyInput(options = { 'baseClass': 'myInput', 'container': 'container', 'input': 'input' }) {
    let baseClass = '.' + options.baseClass;
    let containerClass = baseClass + '__' + options.container;
    let inputClass = baseClass + '__' + options.input;

    for (let iterator of document.querySelectorAll(baseClass)) {
        iterator.querySelector(inputClass).addEventListener(
            'focus',
            (e) => {
                e.currentTarget.nextElementSibling.classList.add('titledInput__text_active');
            }
        );

        iterator.querySelector(inputClass).addEventListener(
            'blur',
            (e) => {
                if (e.currentTarget.value == '') {
                    e.currentTarget.nextElementSibling.classList.remove('titledInput__text_active');
                }
            }
        );
    }
}