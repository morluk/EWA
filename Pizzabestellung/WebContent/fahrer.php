<?php 
// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';
class Fahrer extends Page {
	// to do: declare reference variables for members
	// representing substructures/blocks
	private $bestellungen = array ();
	private $pizzaNamen = array ();
	private $pizzaStati = array ();
	protected function __construct() {
		parent::__construct ();
		// to do: instantiate members representing substructures/blocks
	}
	protected function __destruct() {
		parent::__destruct ();
	}
	protected function getViewData() {
		// hole BestellungId, Adresse, Gesamtpreis pro Bestellung
		$sql = "SELECT Bestellung.BestellungId, Bestellung.Adresse, SUM(Angebot.Preis) AS Gesamtpreis
        		FROM BestelltePizza, Bestellung, Angebot
    			WHERE Bestellung.BestellungID = BestelltePizza.fBestellungID AND
        		Angebot.PizzaName = BestelltePizza.fPizzaName GROUP BY Bestellung.BestellungId HAVING min(BestelltePizza.Status) > 1";
		
		// Cursor auf DB Abfrage records
		$recordset = $this->_database->query ( $sql );
		if (! $recordset)
			throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
			
			// read selected records into result array
			// Cursor auf erstes Record
		$record = $recordset->fetch_assoc ();
		while ( $record ) {
			$this->bestellungen [] = $record;
			$record = $recordset->fetch_assoc ();
		}
		// Cursor loslassen
		$recordset->free ();
		
		// hole zu jeder BestellungId die PizzaNamen
		// und schreibe in $bestellungen an Stelle [$BestellungId]
		foreach ( $this->bestellungen as $bestellung ) {
			$sqlNamen = "SELECT fPizzaName FROM BestelltePizza WHERE fBestellungId = " . $bestellung ["BestellungId"];
			$recordsetNamen = $this->_database->query ( $sqlNamen );
			if (! $recordsetNamen)
				throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
			
			$pizzaNamen = "";
			$recordName = $recordsetNamen->fetch_assoc ();
			while ( $recordName ) {
				// vermeidet Komma am Anfang von String
				if ($pizzaNamen === "")
					$pizzaNamen = $recordName ["fPizzaName"];
				else
					$pizzaNamen = $pizzaNamen . ", " . $recordName ["fPizzaName"];
				$recordName = $recordsetNamen->fetch_assoc ();
			}
			$recordsetNamen->free ();
			$this->pizzaNamen [$bestellung ["BestellungId"]] = $pizzaNamen;
		}
		
		// hole zu jeder BestellungId die Pizza Stati und setze entsprechend
		foreach ( $this->bestellungen as $bestellung ) {
			$sql = "SELECT Status FROM BestelltePizza WHERE fBestellungId = " . $bestellung ["BestellungId"];
			$recordset = $this->_database->query ( $sql );
			if (! $recordset)
				throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
				
				// Status = 0 bedeutet noch nicht fertig
			$gesamtStatus = 0;
			$record = $recordset->fetch_assoc ();
			while ( $record ) {
				if ($gesamtStatus === 0) {
					$gesamtStatus = $record ["Status"];
				} else {
					if ($gesamtStatus != $record ["Status"])
						$gesamtStatus = 0;
				}
				$record = $recordset->fetch_assoc ();
			}
			$recordset->free ();
			$this->pizzaStati [$bestellung ["BestellungId"]] = $gesamtStatus;
		}
	}
	private function insert_article($bestellung) {
		$status_1 = "";
		$status_2 = "";
		$status_3 = "";
		$actStatus = $this->pizzaStati [$bestellung ["BestellungId"]];
		switch ($actStatus) {
			case 2 :
				$status_1 = "checked";
				break;
			case 3 :
				$status_2 = "checked";
				break;
			case 4 :
				$status_3 = "checked";
				break;
			default :
		}
		$id = htmlspecialchars ( $bestellung ["BestellungId"] );
		$adresse = htmlspecialchars ( $bestellung ["Adresse"] );
		$pizzaNamen = htmlspecialchars ( $this->pizzaNamen [$id] );
		$preis = htmlspecialchars ( $bestellung ["Gesamtpreis"] );
		$uri = $_SERVER ['REQUEST_URI'];
		
		echo <<<HERE
	<article class="fahrer">
		<h2>$adresse</h2>
		<p>$pizzaNamen</p>
		<p>
			Preis: <span>$preis</span> €
		</p>
		<form id="id$id" action="http://localhost$uri"
			accept-charset="UTF-8" method="post">
			<table>
				<tr>
					<th class="hidden">ID</th>
					<th>gebacken</th>
					<th>unterwegs</th>
					<th>ausgeliefert</th>
				</tr>
				<tr>
					<td class="hidden">$id</td>
					<td class="status"><input type="radio" name=$id value="2"
						onclick="document.forms['id$id'].submit();" $status_1 /></td>
					<td class="status"><input type="radio" name=$id value="3"
						onclick="document.forms['id$id'].submit();" $status_2 /></td>
					<td class="status"><input type="radio" name=$id value="4"
						onclick="document.forms['id$id'].submit();" $status_3 /></td>

				</tr>
			</table>
		</form>
	</article>    	
HERE;
	}
	protected function generateView() {
		$this->getViewData ();
		$this->generatePageHeader ( "Pizzaservice", "Fahrer", "onload=\"window.setInterval('refresh()', 10*1000);\"");
		
		foreach ( $this->bestellungen as $bestellung ) {
			$this->insert_article ( $bestellung );
		}
		echo ("<script type=\"text/javascript\" src=\"js/mouseover.js\"></script>");
		$this->generatePageFooter ();
	}
	protected function processReceivedData() {
		parent::processReceivedData ();
		if (count ( $_POST )) {
			// hole alle gueltigen BestellungIDs um $_POST zu ueberpruefen
			$sql = "SELECT BestellungId from Bestellung";
			$recordset = $this->_database->query ( $sql );
			
			if (! $recordset)
				throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
			
			$record = $recordset->fetch_assoc ();
			$bestellungId = array ();
			while ( $record ) {
				$bestellungId [] = $record;
				$record = $recordset->fetch_assoc ();
			}
			// Cursor loslassen
			$recordset->free ();
			// laufe ueber alle gueltigen PizzaIds und mache UPDATE Status
			foreach ( $bestellungId as $id ) {
				if (isset ( $_POST [$id ["BestellungId"]] )) {
					$escapedStatus = $this->_database->real_escape_string ( $_POST [$id ["BestellungId"]] );
					if ($escapedStatus === '4') {
						$sqlUpdate = "DELETE FROM `BestelltePizza` WHERE `fBestellungId`=". $id ["BestellungId"];
						if (! $this->_database->query ( $sqlUpdate )) {
							throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
						}
						$sqlUpdate = "DELETE FROM `Bestellung` WHERE `BestellungId`=". $id ["BestellungId"];
						if (! $this->_database->query ( $sqlUpdate )) {
							throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
						}
					} else {
						$sqlUpdate = "UPDATE `BestelltePizza` SET `Status`=" . $escapedStatus . " WHERE `fBestellungId`=" . $id ["BestellungId"];
						if (! $this->_database->query ( $sqlUpdate )) {
							throw new Exception ( "Abfrage fehlgeschlagen: " . $this->_database->error );
						}
					}
				}
			}
			// POST Redirect GET
			header ( "Location: " . $_SERVER ['REQUEST_URI'] );
		}
	}
	public static function main() {
		try {
			$page = new Fahrer ();
			$page->processReceivedData ();
			$page->generateView ();
		} catch ( Exception $e ) {
			header ( "Content-type: text/plain; charset=UTF-8" );
			echo $e->getMessage ();
		}
	}
}

Fahrer::main ();