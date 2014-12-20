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
        $this->generatePageHeader('Pizzaservice', 'Bäcker');
        // call generateView() for all members
        // output view of this page
        $this->statusBlock->generateView();
        
        $this->generatePageFooter();
    }
    
    protected function processReceivedData() 
    {
        parent::processReceivedData();
        //call processReceivedData() for all members
        $this->statusBlock->processReceivedData ();
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