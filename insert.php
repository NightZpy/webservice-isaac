<?php
  $soapParameters = [];
  $soapParameters['SecurityToken'] = "...";
  #crear un cliente del servicio indicado por la url hacia el WSDL.
  $soapClient = new SoapClient("http://url?wsdl", $soapParameters);

  $soapParameters['ClientName'] = 'ADOLFO PUENTE Y ESPINOZA';
  #al crear el cliente, se crean automáticamente los métodos definidos en el WSDL.
  $soapResponse = $soapClient->GetCurrentPositionByClientNameWithAdressAndEvent($soapParameters);

  $xmlResponse = $soapResponse->GetCurrentPositionByClientNameWithAdressAndEventResult;
  $xmlObject = new SimpleXMLElement($xmlResponse);
  $soap = (array)$xmlObject->Table;

  $tables = '(';
  $values = '';
  foreach ($soap as $key => $value) {
    $tables .= $key . ', ';
    $values .= '$client["' . $key . '"], ';
  }
  $tables .= ')';

  echo $tables;
  echo '<br>';
  echo $values;
  echo '<br><hr>';

  $create = 'CREATE TABLE clients_data (<br>id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, <br>client_id INTEGER NOT NULL, ';

  foreach ($soap as $key => $value) 
    $create .= '<br>' . $key . ' TEXT NOT NULL, ';  
  $create .= ')';

  echo $create;
