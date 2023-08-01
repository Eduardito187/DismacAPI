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

        .producto {
            width: 33.33%;
            float: left;
            padding: 10px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    @foreach ($productos->chunk(12) as $pagina)
        <div class="catalogo-page">
            @foreach ($pagina->chunk(3) as $fila)
                <div style="width: 100%; display: flex; justify-content: space-between;">
                    @foreach ($fila as $producto)
                        <div class="producto">
                            <h2>{{ $producto->name }}</h2>
                            <p>Precio: ${{ $producto->price }}</p>
                            <!-- Más información del producto -->
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>
