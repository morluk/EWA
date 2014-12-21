<?php
// UTF-8 marker äöüÄÖÜß€
class StatusBlock {
	
	protected $_database = null;
	private $typ = 'xxx';
	
	public function __construct($database, $typ = 'status') {
		$this->_database = $database;
		$this->typ = $typ;
	}
	
	protected function getViewData($adresse) {
		$sql = "SELECT BestelltePizza.PizzaID, BestelltePizza.fPizzaName, BestelltePizza.Status FROM BestelltePizza, Bestellung
    			WHERE Bestellung.BestellungID = BestelltePizza.fBestellungID";
		if ($this->typ === 'status') {
			$sql = $sql . " AND Bestellung.Adresse = '" . $adresse . "'"." AND Bestellung.BestellungID = ".$_SESSION["BestellungId"];
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
	
	public function generateView($adresse = 'xxx') {
		$bestelltePizza = $this->getViewData ($adresse);
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
}
