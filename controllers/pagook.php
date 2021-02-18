<?php
               session_start();
               include_once("../models/downmusic_model.php");
               include_once("../db/db.php");

               $TotalPrecio=0;
               if (isset($_SESSION['cesta']) && count($_SESSION['cesta'])>0 )
                   $TotalPrecio=mostrarCesta($_SESSION['cesta']);

                $correcto=true;
                $ahora = date('Y-m-d H:i:s');
                $invoiceId=busquedaDato("max(InvoiceId)", "SELECT max(InvoiceId) FROM Invoice");
                $invoiceId++;
                //Se obtiene los datos del usuario
                $busqueda="SELECT * FROM Customer WHERE Email=N'".$_SESSION['usuario']."'";
                $datosCliente=busquedaDatosLinea($busqueda);
                
                $NewInvoice="INSERT INTO Invoice (InvoiceId,CustomerId,InvoiceDate,BillingAddress,BillingCity,BillingCountry,BillingPostalCode,Total) VALUES ($invoiceId, ".$datosCliente["CustomerId"]." , '$ahora', N'".$datosCliente["Address"]."', N'".$datosCliente["City"]."', N'".$datosCliente["Country"]."', N'".$datosCliente["PostalCode"]."', $TotalPrecio)";
                $correcto=ejecutarCadena($NewInvoice);

                for ($i=0; $i < count($_SESSION['cesta']) ; $i++) {
                    $cesta=$_SESSION['cesta'][$i];
                    foreach ($cesta as $cancion => $UnitPrice) {
                        $InvoiceLineId=busquedaDato("max(InvoiceLineId)", "SELECT max(InvoiceLineId) FROM InvoiceLine");
                        $InvoiceLineId++;
                        $TrackId=busquedaDato("TrackId", "SELECT * FROM Track WHERE Name=N'$cancion'");
                        $NewInvoiceLine="INSERT INTO InvoiceLine (InvoiceLineId,InvoiceId,TrackId,UnitPrice,Quantity) VALUES ($InvoiceLineId , $invoiceId ,$TrackId , $UnitPrice, 1)";
                        $correcto=ejecutarCadena($NewInvoiceLine);
                    }
                }
                
                if ($correcto) {
                    $_SESSION['cesta']= [];
                    echo "<br>La compra se ha realizado correctamente";
                    echo "<br><a href='../downmusic.php'>Recargar Pagina</a>";
                } else {
                    echo "<br>Hubo un error en el preceso";
                    echo "<br><a href='../downmusic.php'>Recargar Pagina</a>";
                }
