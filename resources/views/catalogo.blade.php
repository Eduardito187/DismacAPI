<!DOCTYPE html>
<html>
<head>
    <title>Catálogo de Productos</title>
    <!-- Agrega los estilos CSS que necesites -->
    <style>
        .catalogo-page {
            width: 8.5in;
            height: 11in;
            margin: 0 auto;
            padding: 20px;
        }

        .cuadrante {
            width: 33.33%;
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
                    <h2>{{ $producto->name }}</h2>
                    <p>Precio: ${{ $producto->price }}</p>
                    <!-- Más información del producto -->
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>