<?php

namespace App\Classes\Pdf;

use TCPDF;
use App\Classes\Helper\Text;

class PDFProductList extends TCPDF
{
    public function Header()
    {
        // Define el encabezado del PDF (opcional)
        // Por ejemplo, puedes agregar un logotipo o información relevante.
        // Puedes personalizar esto según tus necesidades.
    }

    public function Footer()
    {
        // Define el pie de página del PDF (opcional)
        // Puedes personalizar esto según tus necesidades.
    }

    /**
     * @param array $products
     * @return void
     */
    public function generatePDF(array $products){
        $productsPerPage = 6;
        $pageCount = ceil(count($products) / $productsPerPage);

        for ($currentPage = 1; $currentPage <= $pageCount; $currentPage++) {
            $this->AddPage();

            $start = ($currentPage - 1) * $productsPerPage;
            $end = $start + $productsPerPage;
            $productsOnPage = array_slice($products, $start, $end);

            $this->writeProducts($productsOnPage);
        }
    }

    /**
     * @param array $products
     * @return void
     */
    private function writeProducts(array $products){
        foreach ($products as $product) {
            $this->Cell(0, 10, 'Producto: ' . $product[Text::COLUMN_NAME], 0, 1);
            $this->Cell(0, 10, 'Descripción: ' . $product[Text::BRAND], 0, 1);

            // Si el precio especial es mayor a 0, tacha el precio.
            if ($product[Text::SPECIAL_PRICE] > 0) {
                $this->SetFont('', 'S'); // Establece el estilo de fuente para tachar el texto.
                $this->Cell(0, 10, 'Precio: $' . $product[Text::PRICE], 0, 1, 'T');
                $this->SetFont('', ''); // Restablece el estilo de fuente al predeterminado.
                $this->Cell(0, 10, 'Precio Especial: $' . $product[Text::SPECIAL_PRICE], 0, 1);
            } else {
                $this->Cell(0, 10, 'Precio: $' . $product[Text::PRICE], 0, 1);
            }

            $this->Ln(5);

            // Puedes ajustar las dimensiones y la disposición de las imágenes según tus necesidades.
            $imageWidth = 50;
            $imageHeight = 50;
            $this->Image($product[Text::IMAGE], $this->GetX(), $this->GetY(), $imageWidth, $imageHeight);
            $this->Ln($imageHeight + 5);
        }
    }
}
?>