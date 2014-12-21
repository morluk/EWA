<?php	// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';
require_once './statusBlock.php';


class Baecker extends Page
{
    // declare reference variables for members 
    // representing substructures/blocks
	private $statusBlock;
	
    protected function __construct() 
    {
        parent::__construct();
        // instantiate members representing substructures/blocks
        $this->statusBlock = new StatusBlock($this->_database, 'baecker');
    }
    
    protected function __destruct() 
    {
        parent::__destruct();
    }

    protected function generateView() 
    {
        $this->generatePageHeader("Pizzaservice", "Bäcker", "onload=\"window.setInterval('refresh()', 10*1000);\"");
        // call generateView() for all members
        // output view of this page
        $this->statusBlock->generateView();
        echo ("<script type=\"text/javascript\" src=\"js/mouseover.js\"></script>");
        $this->generatePageFooter();
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
    
    protected function processReceivedData() 
    {
        parent::processReceivedData();
        if (count ( $_POST )) {
        	// $_POST Baecker UPDATE verarbeiten
        	//schlecht weil jedes mal die DB gefragt werden muss.
        	//Woher bekomme ich die Ids, oder den Namen von _POST?
        	$this->processUpdate ();
        	// POST Redirect GET
        	header ( "Location: " . $_SERVER ['REQUEST_URI'] );
        }
    }
  
    public static function main() 
    {
        try {
            $page = new Baecker();
            $page->processReceivedData();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Baecker::main();