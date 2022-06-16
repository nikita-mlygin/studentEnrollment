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

    for (let iterator of document.querySelectorAll('.titledInput')) {
        iterator.querySelector('.titledInput__input').addEventListener(
            'focus',
            (e) => {
                e.currentTarget.nextElementSibling.classList.add('titledInput__text_active');
            }
        );

        iterator.querySelector('.titledInput__input').addEventListener(
            'blur',
            (e) => {
                if (e.currentTarget.value == '') {
                    e.currentTarget.nextElementSibling.classList.remove('titledInput__text_active');
                }
            }
        );
    }
}