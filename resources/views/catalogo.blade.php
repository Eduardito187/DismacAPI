<!DOCTYPE html>
<html>
<head>
    <title>Catálogo de Productos</title>
    <!-- Agrega los estilos CSS que necesites -->
    <style>
        .catalogo-page {
            width: 100%;
            height: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .cuadrante {
            width: 50%;
            height: 33.33%;
            float: left;
            padding: 5px;
            box-sizing: border-box;
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    @foreach ($products as $grupo)
        <div class="catalogo-page">
            @foreach ($grupo as $producto)
                <div class="cuadrante">
                    {{print_r($producto)}}
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>