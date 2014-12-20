<?php
// UTF-8 marker äöüÄÖÜß€
class StatusBlock {
	
	protected $_database = null;
	private $pizzaName = array ();
	private $adresse = 'xxx';
	private $typ = 'xxx';
	
	public function __construct($database, $typ = 'status') {
		$this->_database = $database;
		$this->typ = $typ;
	}
	
	protected function getViewData() {
		$sql = "SELECT BestelltePizza.PizzaID, BestelltePizza.fPizzaName, BestelltePizza.Status FROM BestelltePizza, Bestellung
    			WHERE Bestellung.BestellungID = BestelltePizza.fBestellungID";
		if ($this->typ === 'status') {
			$sql = $sql . " AND Bestellung.Adresse = '" . $this->adresse . "'";
		}
		
		// Cursor auf DB Abfrage records
		$recordset = $this->_database->query ( $sql );
		if (! $recordset)
			throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
			
			// read selected records into result array
		$bestelltePizza = array ();
		// Cursor auf erstes Record
		$record = $recordset->fetch_assoc ();
		while ( $record ) {
			$bestelltePizza [] = $record;
			$record = $recordset->fetch_assoc ();
		}
		// Cursor loslassen
		$recordset->free ();
		return $bestelltePizza;
	}
	
	private function insert_tr($name, $status, $id) {
		$status_1 = "";
		$status_2 = "";
		$status_3 = "";
		$status_4 = "";
		$disabled = "";
		if ($this->typ === 'status') {
			$disabled = "disabled";
		}
		switch ($status) {
			case 0 :
				$status_1 = "checked";
				break;
			case 1 :
				$status_2 = "checked";
				break;
			case 2 :
				if ($this->typ === 'baecker')
					return;
				$status_3 = "checked";
				break;
			case 3 :
				if ($this->typ === 'baecker')
					return;
				$status_4 = "checked";
				break;
			default :
		}
		echo <<<HERE
			<tr>
			  <td>$name</td>
			  <td class="hidden">$id</td>
			  <td class="status">
			  <input type="radio" name="$id" value="0" onclick="document.forms['formid'].submit();" $status_1 $disabled /></td>
			  <td class="status">
			  <input type="radio" name="$id" value="1" onclick="document.forms['formid'].submit();" $status_2 $disabled /></td>
			  <td class="status">
			  <input type="radio" name="$id" value="2" onclick="document.forms['formid'].submit();" $status_3 $disabled /></td>
HERE;
		if ($this->typ === 'status') {
			echo <<<HERE
			  <td class="status">
			  <input type="radio" name="$id" value="3" onclick="document.forms['formid'].submit();" $status_4 $disabled /></td>
HERE;
		}
		echo ("</tr>");
	}
	
	public function generateView() {
		$bestelltePizza = $this->getViewData ();
		$tableHeader = "";
		if ($this->typ === 'status') {
			$tableHeader = "<th>unterwegs</th>";
		}
		$uri = $_SERVER ['REQUEST_URI'];
		echo <<<HERE
		<form id="formid" action="http://localhost$uri" accept-charset="UTF-8" method="post">
	      <table>
			<tr>
			  <th></th>
			  <th class="hidden">PizzaID</th>
			  <th>bestellt</th>
			  <th>im Ofen</th>
			  <th>fertig</th>
			  $tableHeader
			</tr>
    	
HERE;
		
		foreach ( $bestelltePizza as $bestpizza ) {
			$this->insert_tr ( $bestpizza ["fPizzaName"], $bestpizza ["Status"], $bestpizza ["PizzaID"] );
		}
		
		echo <<<HERE
		      </table>
		      </form>
HERE;
	}
	private function processBestellung() {
		// Annahme: neue Bestellung = INSERT
		$sql = "INSERT INTO Bestellung(Adresse) VALUES ('" . $this->adresse . "')";
		$this->_database->query ( $sql );
		$bestellungID = $this->_database->insert_id;
		foreach ( $this->pizzaName as $pizza ) {
			$sqlpizza = "INSERT INTO BestelltePizza(fBestellungID,fPizzaName,Status) VALUES
    			('" . $bestellungID . "','" . $pizza . "','bestellt')";
			$this->_database->query ( $sqlpizza );
		}
	}
	
	private function processUpdate() {
		// hole alle gueltigen PizzaIDs um $_POST zu ueberpruefen
		$sql = "SELECT PizzaID from BestelltePizza";
		$recordset = $this->_database->query ( $sql );
		
		if (! $recordset)
			throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
		
		$record = $recordset->fetch_assoc ();
		$pizzaId = array ();
		while ( $record ) {
			$pizzaId [] = $record;
			$record = $recordset->fetch_assoc ();
		}
		// Cursor loslassen
		$recordset->free ();
		// laufe ueber alle gueltigen PizzaIds und mache UPDATE Status
		foreach ( $pizzaId as $id ) {
			if (isset ( $_POST [$id ["PizzaID"]] )) {
				$escapedStatus = $this->_database->real_escape_string ( $_POST [$id ["PizzaID"]] );
				$sqlUpdate = "UPDATE `BestelltePizza` SET `Status`=" . $escapedStatus . " WHERE `PizzaID`=" . $id["PizzaID"];
				if (!$this->_database->query ( $sqlUpdate )) {
					throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
				}
			}
		}
	}
	
	public function processReceivedData() {
		$get_info = "";
		if (isset ( $_POST ["Adresse"] )) {
			$this->adresse = $this->_database->real_escape_string ( $_POST ["Adresse"] );
			$get_info = "?Adresse=" . $this->adresse;
		}
		if (isset ( $_POST ["PizzaName"] )) {
			for($i = 0; $i < count ( $_POST ['PizzaName'] ); $i ++) {
				$this->pizzaName [$i] = $this->_database->real_escape_string ( $_POST ['PizzaName'] [$i] );
			}
			// $_POST Bestellung verarbeiten .. INSERT
			$this->processBestellung ();
		}
		if (count ( $_POST )) {
			// $_POST Baecker UPDATE verarbeiten
			//schlecht weil jedes mal die DB gefragt werden muss. 
			//Woher bekomme ich die Ids, oder den Namen von _POST?
			$this->processUpdate ();
			// POST Redirect GET
			// adresse mitgeben
			header ( "Location: " . $_SERVER ['REQUEST_URI'] . $get_info );
		}
		// wenn Adresse ueber $_GET kommt
		if (isset ( $_GET ["Adresse"] )) {
			$this->adresse = $this->_database->real_escape_string ( $_GET ["Adresse"] );
		}
	}
}
