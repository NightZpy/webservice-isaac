<?php
  require "Database.class.php";
  $db = new Database('data/clients.db');

  $db->query('DELETE FROM clients_data');

  $strQuery = "SELECT id, name FROM clients WHERE enabled";
  $result = $db->query($strQuery);
  if ($result) {
    $clients = [];
    while($row = $result->fetchArray(SQLITE3_ASSOC) )
      $clients[$row['id']] = $row['name'];

    $soapParameters = [];
    $soapParameters['SecurityToken'] = "...";
    #crear un cliente del servicio indicado por la url hacia el WSDL.
    $soapClient = new SoapClient("http://url?wsdl", $soapParameters);        

    $soapClients = [];
    foreach ($clients as $id => $client) {
      $soapParameters['ClientName'] = $client;
      $soapResponse = $soapClient->GetCurrentPositionByClientNameWithAdressAndEvent($soapParameters);

      $xmlResponse = $soapResponse->GetCurrentPositionByClientNameWithAdressAndEventResult;
      $xmlObject = new SimpleXMLElement($xmlResponse); 
      $clientData = [];
      foreach ($xmlObject as $obj)  {
        $arrayObj = (array)$obj;
        $arrayObj['SMSTarget']   = empty((array)$arrayObj['SMSTarget'])   ? 0 : $arrayObj['SMSTarget'];
        $arrayObj['SMSPassword'] = empty((array)$arrayObj['SMSPassword']) ? 0 : $arrayObj['SMSPassword'];
        $arrayObj['SMSUsername'] = empty((array)$arrayObj['SMSUsername']) ? 0 : $arrayObj['SMSUsername'];
        $arrayObj['IPAddress']   = empty((array)$arrayObj['IPAddress'])   ? 0 : $arrayObj['IPAddress'];
        $arrayObj['PortNumber']  = empty((array)$arrayObj['PortNumber'])  ? 0 : $arrayObj['PortNumber'];
        $arrayObj['modelDevice'] = empty((array)$arrayObj['modelDevice']) ? 0 : $arrayObj['modelDevice'];
        $arrayObj['Address2']    = empty((array)$arrayObj['Address2'])    ? 0 : $arrayObj['Address2'];
        $arrayObj['ZipCode']     = empty((array)$arrayObj['ZipCode'])     ? 0 : $arrayObj['ZipCode'];
        $clientData[] = $arrayObj;      
      }
      $soapClients[$id] = $clientData;
    }    

    $strQuery = 'INSERT INTO clients_data (
      client_id, ItemGUID,      ItemID, ClientID, HardwareID, EntityType, EntityID,   ItemName,     IMEI, ItemImage, 
      SMSTarget, TapestryFlag,  Status, Created,  Modified,   CreatedBy,  ModifiedBy, SMSPassword,    SMSUsername, TraceLineColor,  IPAddress,  DefaultItemImage, IsOutOfService, PNDType,    EnableDataFeed,    ItemStatus, GEXEnabled,      PortNumber, modelDevice,      PositionGUID,   PositionID, MessageID,         SessionID, EventValue,      ClientID1,  ItemID1,          Lat,            Lon,        Alt,               Speed, ActualDate,      IsPoll,     Created1,         Address1,       Address2,   City,              State, ZipCode,         Country,    ActualDateUTC,    IgnitionStatus, Direction, DistanceInSession,  IsValid, BreakDiscrepancy, Odometer,  Idle) VALUES (%d, %d, %d, "%s", %d, "%s", %d, "%s", %d, "%s", "%s", %d, "%s", "%s", "%s", %d, %d, "%s", "%s", "%s", "%s", "%s", %d, %d, %d, %d, %d, %d, "%s", "%s", %d, %d, %d, %d, %d, %d, %f, %f, %f, %f, "%s", %d, "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", %d, %f, %f, %d, %d, %f, %d)';    

    $query = '';
    foreach ($soapClients as $id => $clientData) {
      echo '<h1>' . $clients[$id] . '</h1>';
      foreach ($clientData as $data) {
        echo '<hr>';
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        $query .= sprintf($strQuery, $id, $data["ItemGUID"], $data["ItemID"], $data["ClientID"], $data["HardwareID"], $data["EntityType"], $data["EntityID"], $data["ItemName"], $data["IMEI"], $data["ItemImage"], $data["SMSTarget"], $data["TapestryFlag"], $data["Status"], $data["Created"], $data["Modified"], $data["CreatedBy"], $data["ModifiedBy"], $data["SMSPassword"], $data["SMSUsername"], $data["TraceLineColor"], $data["IPAddress"], $data["DefaultItemImage"], $data["IsOutOfService"], $data["PNDType"], $data["EnableDataFeed"], $data["ItemStatus"], $data["GEXEnabled"], $data["PortNumber"], $data["modelDevice"], $data["PositionGUID"], $data["PositionID"], $data["MessageID"], $data["SessionID"], $data["EventValue"], $data["ClientID1"], $data["ItemID1"], $data["Lat"], $data["Lon"], $data["Alt"], $data["Speed"], $data["ActualDate"], $data["IsPoll"], $data["Created1"], $data["Address1"], $data["Address2"], $data["City"], $data["State"], $data["ZipCode"], $data["Country"], $data["ActualDateUTC"], $data["IgnitionStatus"], $data["Direction"], $data["DistanceInSession"], $data["IsValid"], $data["BreakDiscrepancy"], $data["Odometer"], $data["Idle"]);
        $query .= ';';
      }
    }
    $result = $db->exec($query);
  }
  $db->close();