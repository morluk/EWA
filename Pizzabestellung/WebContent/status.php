<?php	// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';
require_once './statusBlock.php';

class Status extends Page
{
    // declare reference variables for members 
    // representing substructures/blocks
    private $statusBlock;
    private $pizzaName = array ();
    private $adresse = 'xxx';
    
    protected function __construct() 
    {
        parent::__construct();
        // instantiate members representing substructures/blocks
        $this->statusBlock = new StatusBlock($this->_database);
    }
    
    protected function __destruct() 
    {
        parent::__destruct();
    }
    
    protected function generateView() 
    {
        $this->generatePageHeader("Pizzaservice", "Bestellung", "onload=\"window.setInterval('refresh()', 2*1000);\"");
        $this->statusBlock->generateView($this->adresse);
			
			echo <<<HERE
		      <footer>
				<ul>
				  <li class="ButtonNormal" onmouseover="Mouseover(this);"
						onmouseout="Mouseout(this);" onmousedown="Mousedown(this);"><a href="bestellung.php">Neue Bestellung</a></li>
				</ul>
		      </footer>
		   <script type="text/javascript" src="js/mouseover.js"></script>
        
HERE;
        $this->generatePageFooter();
    }
    
    private function processBestellung() {
    	// Annahme: neue Bestellung = INSERT
    	$sql = "INSERT INTO Bestellung(Adresse) VALUES ('" . $this->adresse . "')";
    	$this->_database->query ( $sql );
    	$bestellungID = $this->_database->insert_id;
    	if (empty($_SESSION["BestellungId"])) {
    		$_SESSION["BestellungId"] = $bestellungID;
    	}
    	foreach ( $this->pizzaName as $pizza ) {
    		$sqlpizza = "INSERT INTO BestelltePizza(fBestellungID,fPizzaName,Status) VALUES
    			('" . $bestellungID . "','" . $pizza . "',0)";
    		$this->_database->query ( $sqlpizza );
    	}
    }
    
	protected function processReceivedData() {
		parent::processReceivedData ();
		$get_info = "";
		if (isset ( $_POST ["Adresse"] )) {
			$this->adresse = $this->_database->real_escape_string ( $_POST ["Adresse"] );
			$get_info = "?Adresse=" . $_POST ["Adresse"];
		}
		if (isset ( $_POST ["PizzaName"] )) {
			for($i = 0; $i < count ( $_POST ['PizzaName'] ); $i ++) {
				$this->pizzaName [$i] = $this->_database->real_escape_string ( $_POST ['PizzaName'] [$i] );
			}
			// $_POST Bestellung verarbeiten .. INSERT
			$this->processBestellung ();
		}
		if (count ( $_POST )) {
			// POST Redirect GET
			// adresse mitgeben
			header ( "Location: " . $_SERVER ['REQUEST_URI'] . $get_info );
		}
		// wenn Adresse ueber $_GET kommt
		if (isset ( $_GET ["Adresse"] )) {
			$this->adresse = $this->_database->real_escape_string ( $_GET ["Adresse"] );
		}
	}
  
    public static function main() 
    {
        try {
            $page = new Status();
            $page->processReceivedData();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

session_start();
Status::main();