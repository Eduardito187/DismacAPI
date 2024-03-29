<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error 404 - Página no encontrada</title>
    </head>
    <body>
        <div class="container">
            <div class="error-message">
                <h1>Error 500 - Error interno del servidor</h1>
                <p>Lo sentimos, ha ocurrido un error interno del servidor.</p>
                <p>Puede que estemos experimentando problemas técnicos. Por favor, inténtalo nuevamente más tarde.</p>
            </div>
            <div class="animated-bg">
            </div>
        </div>
    </body>
</html>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #ec1c24;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    .error-message {
        text-align: center;
        color: #fff;
    }

    .error-message h1 {
        font-size: 4rem;
        margin-bottom: 0.5rem;
    }

    .error-message p {
        font-size: 1.5rem;
        margin: 0;
    }

    .animated-bg {
        width: 200px;
        height: 200px;
        background-color: #ec1c24;
        background-image: url('https://dismacapi.grazcompany.com/storage/dismac_red.png');
        background-size: cover;
        background-position: center;
    }
</style>