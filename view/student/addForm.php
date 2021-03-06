<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@700&family=Open+Sans:wght@800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@700&family=PT+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@700&family=Lora&family=PT+Mono&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/styles/style.css">
    <script src="/scripts/main.js"></script>
</head>

<body>
    <div class="window">
        <header class="header">
            <h2 class="subTitle">
                Добавить­ <br>
                студента
            </h2>
            <nav class="formNavbar">
                <a class="formNavbar__elm formNavbar__elm_active" href="#">
                    <div class="formNavbar__text">ФИО</div>
                    <div class="formNavbar__line__container">
                        <hr class="formNavbar__line_active formNavbar__line_active_1">
                        <hr class="formNavbar__line">
                    </div>
                </a>
                <a class="formNavbar__elm" href="#">
                    <div class="formNavbar__text formNavbar__elm_active">Оценки</div>
                    <div class="formNavbar__line__container">
                        <hr class="formNavbar__line_active formNavbar__line_active_1">
                        <hr class="formNavbar__line">
                    </div>
                </a>
                <a class="formNavbar__elm" href="#">
                    <div class="formNavbar__text">Контакты</div>
                    <div class="formNavbar__line__container">
                        <hr class="formNavbar__line_active formNavbar__line_active_1">
                        <hr class="formNavbar__line">
                    </div>
                </a>
            </nav>
        </header>

        <div class="scores" style="display: none;">
            <div class="scores_header">
                <div class="scores_header_title">
                    Оценки:
                </div>
            </div>
            <div class="score_block_container">
                <div class="scores_block">
                    <div class="scores_disciplineNameChose">
                        <input class="scores_disciplineNameChoseInput" type="text" name="" id="">
                    </div>
                    <div class="scores_scoreChose">
                        <input class="scores_scoreChose" type="text" name="" id="">
                    </div>
                    <div class="scores_deleteScore">
                        <button class="scores_deleteScore_btn"></button>
                    </div>
                </div>
            </div>

        </div>
        <div class="names">
            <div class="group__title">
                ФИО
            </div>

            <div class="names__inputContainer">
                <div class="titledInput">
                    <input type="text" name="" id="1" class="titledInput__input">
                    <label for="1" class="titledInput__text">Имя</label>
                </div>
                <div class="titledInput">
                    <input type="text" name="" id="2" class="titledInput__input">
                    <label for="2" class="titledInput__text">Фамилия</label>
                </div>
                <div class="titledInput">
                    <input type="text" name="" id="3" class="titledInput__input">
                    <label for="3" class="titledInput__text">Отчество</label>
                </div>
            </div>
            
        </div>
        <div class="contacts">
            <div class="group__title">
                Контакты
            </div>

            <div class="contacts__elm">
                <div class="titledInput titledInputWithPrompt">
                    <input type="text" name="" id="4" class="titledInput__input">
                    <label for="4" class="titledInput__text">Тип контакта</label>
                    <ul class="titledInputWithPrompt__promptContainer">
                        <li class="titledInputWithPrompt__promptElement">Мать</li>
                        <li class="titledInputWithPrompt__promptElement">Отец</li>
                        <li class="titledInputWithPrompt__promptElement">Брат</li>
                    </ul>
                </div>
            </div>
        </div>

        <button onclick="get()">Get</button>
    </div>
</body>

</html>