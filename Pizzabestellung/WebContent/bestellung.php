<?php	// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';

class Bestellung extends Page
{

    protected function __construct() 
    {
        parent::__construct();
    }
    
    protected function __destruct() 
    {
        parent::__destruct();
    }

    protected function getViewData()
    {
        $sql = "SELECT * FROM Angebot";

        //Cursor auf DB Abfrage records
        $recordset = $this->_database->query($sql);
        if (!$recordset)
        	throw new Exception("Abfrage fehlgeschlagen: ".$this->database->error);
        
        //read selected records into result array
        $speisekarte = array();
        //Cursor auf erstes Record
        $record = $recordset->fetch_assoc();
        while ($record) {
        	$speisekarte[] = $record;
        	$record = $recordset->fetch_assoc();
        }
        //Cursor loslassen
        $recordset->free();
        return $speisekarte;
    }
    
	private function insert_tr($name, $bild, $preis) {
		echo <<<HERE
			<tr>
				<td onclick="add(this)" data-price="$preis" data-name="$name"><img src="$bild" width="150" height="150" alt=""
					title="Pizza" /></td>
				<td>$name</td>
				<td>$preis €</td>
			</tr>		
HERE;

	}
	
    protected function generateView() 
    {
        $speisen = $this->getViewData();
        $this->generatePageHeader("Pizzaservice", "Bestellung", "onload=\"init();\"");

        echo <<<HERE
		<article class="speisekarte">
		<table>
			<tr class="hidden">
				<th>Bild</th>
				<th>Name</th>
				<th>Preis</th>
			</tr>
HERE;
        
        foreach($speisen as $speise) $this->insert_tr($speise["PizzaName"],$speise["Bilddatei"],$speise["Preis"]);
        
        echo <<<HERE
		</table>
		</article>
		<article class="warenkorb">
			<form action="http://localhost/ewa/status.php"
				accept-charset="UTF-8" method="post">
				<div class="textbox">
					<select id="selectBox" name="PizzaName[]" size="5" multiple>
						<option>test</option>
					</select>
				</div>
				<p class="bestellung">
					<span id="preis">15,70</span> €
				</p>
				<div class="adress">
					<input type="text" name="Adresse" value="" size="30" maxlength="40" required />
				</div>
				<div class="button1">
					<input onclick="warenkorb.clear();" type="reset" name="Alle löschen" value="Alle löschen" />
				</div>
				<div class="button2">
					<input onclick="warenkorb.removeSelected()" type="button" name="Auswahl löschen" value="Auswahl löschen" />
					<input onclick="warenkorb.selectAll()" type="submit" name="Bestellen" value="Bestellen" />
				</div>
			</form>
		</article>
		<!-- Script Einbinden -->
		<script type="text/javascript" src="js/funktionen.js"> </script>
		<noscript>
		<p>Bitte aktivieren Sie JavaScript !</p>
		</noscript>
        
HERE;
        $this->generatePageFooter();
    }
    
    protected function processReceivedData() 
    {
        parent::processReceivedData();
    }

    public static function main() 
    {
        try {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Bestellung::main();