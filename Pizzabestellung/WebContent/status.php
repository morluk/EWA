<?php	// UTF-8 marker äöüÄÖÜß€

require_once './Page.php';
require_once './statusBlock.php';

class Status extends Page
{
    // declare reference variables for members 
    // representing substructures/blocks
    private $statusBlock;
    
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
		
        $this->statusBlock->generateView();
			
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
    
	protected function processReceivedData() {
		parent::processReceivedData ();
		$this->statusBlock->processReceivedData ();
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

Status::main();