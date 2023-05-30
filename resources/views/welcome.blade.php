<?php

use App\Models\Sales;
use App\Classes\Picture\PictureApi;

$PictureApi = new PictureApi();
$idOrden = 1;
$fechaCompromiso = "2023-05-31 00:00:00";
$Orden = Sales::find($idOrden);
$Address = $Orden->ShippingAddress->Address;
$Customer = $Orden->ShippingAddress->Customer;
$SalesDetails = $Orden->SalesDetails;
?>
<!DOCTYPE html>
<html>
<title>Código de verificación</title>

<head>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Muli&display=swap');
        @import url('https://fonts.googleapis.com/css?family=Quicksand&display=swap');
    </style>
    <link rel="stylesheet" href="{{URL::asset('storage/css/mail.css')}}">
</head>

<body>
    <div>
        <div class="sectionImage">
            <img src="https://dismacapi.grazcompany.com/storage/dismac_clasic.png" class="imageDismac" />
        </div>
        <div class="contentSection">
            <div class="centerContent">
                <div class="sizeMax">
                    <h4 class="margin5">Número de orden: <?= $Orden->nro_proforma; ?></h4>
                    <hr class="margin0"/>
                    <h6 class="margin5">Fecha de realización <?= $Orden->created_at; ?></h6>
                    <h6 class="margin5">Fecha de entrega <?= $fechaCompromiso; ?></h6>
                </div>
                <div class="sizeMax margin5">
                    <p class="pTextMail"><small class="size13"><b>Nro de factura:</b> <?= $Orden->nro_factura; ?></small></p>
                    <p class="pTextMail"><small class="size13"><b>Nro de control:</b> <?= $Orden->nro_control; ?></small></p>
                </div>
                <div class="contentMaxFlex">
                    <div class="contentColumnFlex">
                        <h5 class="margin5">Datos de envío</h5>
                        <div class="margin5">
                            <p class="pTextMail"><small class="size13"><?= $Customer->nombre." ".$Customer->apellido_paterno." ".$Customer->apellido_materno; ?></small></p>
                            <p class="pTextMail"><small class="size13"><?= $Address->AddressExtra->extra; ?></small></p>
                            <p class="pTextMail"><small class="size13"><?= $Address->AddressExtra->address; ?></small></p>
                            <p class="pTextMail"><small class="size13"><?= $Address->Municipio->name; ?></small></p>
                            <p class="pTextMail"><small class="size13"><?= $Address->Ciudad->name; ?></small></p>
                            <p class="pTextMail"><small class="size13"><?= $Address->Pais->name; ?></small></p>
                        </div>
                    </div>
                    <div class="contentColumnFlex">
                        <h5 class="margin5">Datos de facturación</h5>
                        <div class="margin5">
                            <p class="pTextMail"><small class="size13"><?= $Customer->nombre." ".$Customer->apellido_paterno." ".$Customer->apellido_materno; ?></small></strong>
                            <p class="pTextMail"><small class="size13"><?= $Customer->TipoDocumento->type; ?>: <?= $Customer->num_documento; ?></small></p>
                            <p class="pTextMail"><small class="size13">Telf: <?= $Customer->num_telefono; ?></small></p>
                        </div>
                    </div>
                </div>
                <div class="contentTable">
                    <table class="sizeMax">
                        <thead>
                            <tr class="sizeMax">
                                <th colspan="2" scope="rwo"><p class="pTextMail"><small class="size13"><b>Producto</b></small></p></th>
                                <th scope="row" class="textRight"><p class="pTextMail"><small class="size13"><b>Cantidad</b></small></p></th>
                                <th scope="row"><p class="pTextMail"><small class="size13"><b>Precio</b></small></p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($SalesDetails as $key => $Detail) {
                                echo '<tr>
                                    <td colspan="2">
                                        <div class="contentMaxFlex">
                                            <div class="contentListImage">
                                                <img src="'.$PictureApi->productFirstPicture($Detail->Product->id).'" class="imageProduct" />
                                            </div>
                                            <div class="contentListProduct">
                                                <h5 class="margin5">'.$Detail->Product->name.'</h5>
                                                <h6 class="margin5">'.$Detail->Product->sku.'</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="textCenter"><p class="pTextMail"><small class="size13 marginRight"><b>'.$Detail->qty.'</b></small></p></td>
                                    <td class="textCenter"><p class="pTextMail"><small class="size13"><b>'.$Detail->subtotal.'</b></small></p></td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2"></th>
                                <th class="textRight"><p class="pTextMail"><small class="size13"><b>Subtotal</b></small></p></th>
                                <th class="textCenter"><p class="pTextMail"><small class="size13"><b><?= $Orden->subtotal; ?> Bs</b></b></small></p></th>
                            </tr>
                            <tr>
                                <th colspan="2"></th>
                                <th class="textRight"><p class="pTextMail"><small class="size13"><b>Descuentos</b></small></p></th>
                                <th class="textCenter"><p class="pTextMail"><small class="size13"><b><?= $Orden->discount; ?> Bs</b></small></p></th>
                            </tr>
                            
                            <tr>
                                <th colspan="2"></th>
                                <th class="textRight"><p class="pTextMail"><small class="size13"><b>Total</b></small></p></th>
                                <th class="textCenter"><p class="pTextMail"><small class="size13"><b><?= $Orden->total; ?> Bs</b></b></small></p></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="contentLinks">
            <a href="https://www.facebook.com/DismacBolivia/" class="inlineBlock">
                <img src="https://dismacapi.grazcompany.com/storage/fbnew.png" class="contentImage"/>
            </a>
            <a href="https://www.instagram.com/dismacbolivia/" class="inlineBlock">
                <img src="https://dismacapi.grazcompany.com/storage/instanew.png" class="contentImage"/>
            </a>
            <a href="https://www.youtube.com/channel/UCa_XVvWTrq7C0IIq_Oiswqw/videos" class="inlineBlock">
                <img src="https://dismacapi.grazcompany.com/storage/ytnew.png" class="contentImage"/>
            </a>
        </div>
    </div>
</body>
</html>