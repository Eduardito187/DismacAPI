<?php

use App\Models\Sales;
use App\Classes\Picture\PictureApi;

$PictureApi = new PictureApi();
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
        body {
            font-family: 'Muli', sans-serif;
            color: rgba(0, 0, 0, 0.8);
            font-weight: 400;
            line-height: 1.58;
            letter-spacing: -.003em;
            font-size: 20px;
            padding: 40px;
        }
    </style>
</head>

<body>
    <div>
        <div style="width: 100%;background-color: white;">
            <img src="https://dismacapi.grazcompany.com/storage/dismac_clasic.png" style="width: 100px;" />
        </div>
        <div style="width: 100%;background-color: #f5f5f5;margin-bottom: 10px;">
            <div style="margin-left: 10%;width: 80%;background-color: white;">
                <div style="width: 100%;">
                    <h4 style="margin: 5px;">Número de orden: <?= $Orden->nro_proforma; ?></h4>
                    <hr style="margin: 0px;"/>
                    <h6 style="margin: 5px;">Fecha de realización <?= $Orden->created_at; ?></h6>
                    <h6 style="margin: 5px;">Fecha de entrega <?= $fechaCompromiso; ?></h6>
                </div>
                <div style="width: 100%;margin: 5px;">
                    <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>Nro de factura:</b> <?= $Orden->nro_factura; ?></small></p>
                    <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>Nro de control:</b> <?= $Orden->nro_control; ?></small></p>
                </div>
                <div style="width: 100%;display: flex;">
                    <div style="width: 50%;display: inline-block;">
                        <h5 style="margin: 5px;">Datos de envío</h5>
                        <div style="margin: 5px;">
                            <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><?= $Customer->nombre." ".$Customer->apellido_paterno." ".$Customer->apellido_materno; ?></small></p>
                            <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><?= $Address->AddressExtra->extra; ?></small></p>
                            <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><?= $Address->AddressExtra->address; ?></small></p>
                            <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><?= $Address->Municipio->name; ?></small></p>
                            <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><?= $Address->Ciudad->name; ?></small></p>
                            <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><?= $Address->Pais->name; ?></small></p>
                        </div>
                    </div>
                    <div style="width: 50%;display: inline-block;">
                        <h5 style="margin: 5px;">Datos de facturación</h5>
                        <div style="margin: 5px;">
                            <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><?= $Customer->nombre." ".$Customer->apellido_paterno." ".$Customer->apellido_materno; ?></small></strong>
                            <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><?= $Customer->TipoDocumento->type; ?>: <?= $Customer->num_documento; ?></small></p>
                            <p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;">Telf: <?= $Customer->num_telefono; ?></small></p>
                        </div>
                    </div>
                </div>
                <div style="width: 100%;display: flex;margin-top: 10px;margin: 5px;">
                    <table style="width: 100%;">
                        <thead>
                            <tr style="width: 100%;">
                                <th colspan="2" scope="rwo"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>Producto</b></small></p></th>
                                <th scope="row" style="text-align: right;"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>Cantidad</b></small></p></th>
                                <th scope="row"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>Precio</b></small></p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($SalesDetails as $key => $Detail) {
                                echo '<tr>
                                    <td colspan="2">
                                        <div style="width: 100%;display: flex;">
                                            <div style="width: 60px;display: inline-block;">
                                                <img src="'.$PictureApi->productFirstPicture($Detail->Product->id).'" style="width: 60px;height: 60px;" />
                                            </div>
                                            <div style="width: calc(100% - 60px);display: inline-block;">
                                                <h5 style="margin: 5px;">'.$Detail->Product->name.'</h5>
                                                <h6 style="margin: 5px;">'.$Detail->Product->sku.'</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>'.$Detail->qty.'</b></small></p></td>
                                    <td style="text-align: center;"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>'.$Detail->subtotal.' Bs</b></small></p></td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2"></th>
                                <th style="text-align: right;"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>Subtotal</b></small></p></th>
                                <th style="text-align: center;"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b><?= $Orden->subtotal; ?> Bs</b></b></small></p></th>
                            </tr>
                            <tr>
                                <th colspan="2"></th>
                                <th style="text-align: right;"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>Descuentos</b></small></p></th>
                                <th style="text-align: center;"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b><?= $Orden->discount; ?> Bs</b></small></p></th>
                            </tr>
                            
                            <tr>
                                <th colspan="2"></th>
                                <th style="text-align: right;"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b>Total</b></small></p></th>
                                <th style="text-align: center;"><p style="margin: 0px;line-height: 15px;"><small style="font-size: 12px;"><b><?= $Orden->total; ?> Bs</b></b></small></p></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div style="width: 100%;background-color: white;text-align: center;justify-content: center;">
            <a href="https://www.facebook.com/DismacBolivia/" style="display: inline-block;">
                <img src="https://dismacapi.grazcompany.com/storage/fbnew.png" style="width: 40px;height: 40px;border-radius: 20px;"/>
            </a>
            <a href="https://www.instagram.com/dismacbolivia/" style="display: inline-block;">
                <img src="https://dismacapi.grazcompany.com/storage/instanew.png" style="width: 40px;height: 40px;border-radius: 20px;"/>
            </a>
            <a href="https://www.youtube.com/channel/UCa_XVvWTrq7C0IIq_Oiswqw/videos" style="display: inline-block;">
                <img src="https://dismacapi.grazcompany.com/storage/ytnew.png" style="width: 40px;height: 40px;border-radius: 20px;"/>
            </a>
        </div>
    </div>
</body>
</html>