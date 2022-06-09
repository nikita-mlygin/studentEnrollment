<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
        async function get() {
            request = await fetch('/student/add', 
            {
                method: 'POST',
                headers: 
                {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(
                    { 
                        'students': 
                        [
                            {
                                'firstName': document.getElementById('studentFirstName').value,
                                'lastName': document.getElementById('studentLastName').value
                            },
                            {
                                'firstName': document.getElementById('studentFirstName').value,
                                'lastName': document.getElementById('studentLastName').value
                            }
                        ]
                    }
                ),
            });


            console.log(await request.json());
        }

    </script>
</head>
<body>
    
    <input type="text" id='studentFirstName'>
    <input type="text" id='studentLastName'>

    <button onclick="get()">Get </button>
    <? print_r(phpinfo()) ?>

    
</body>
</html>