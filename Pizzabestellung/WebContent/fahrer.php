<?php
// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 5
 *
 * @category File
 * @package Pizzaservice
 * @author Bernhard Kreling, <b.kreling@fbi.h-da.de>
 * @author Ralf Hahn, <ralf.hahn@h-da.de>
 * @license http://www.h-da.de none
 *          @Release 1.2
 * @link http://www.fbi.h-da.de
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 *
 * @author Bernhard Kreling, <b.kreling@fbi.h-da.de>
 * @author Ralf Hahn, <ralf.hahn@h-da.de>
 */
class PageTemplate extends Page {
	private $pizzaAdressArray = array ();
	private $pizzaOrderIdArray = array ();
	private $pizzaStatusArray = array ();
	private $pizzaPizzaArray = array (
			array () 
	);
	private $pizzaPriceArray = array ();
	private $counter;
	
	/**
	 * Instantiates members (to be defined above).
	 *
	 * Calls the constructor of the parent i.e. page class.
	 * So the database connection is established.
	 *
	 * @return none
	 */
	protected function __construct() {
		parent::__construct ();
		// to do: instantiate members representing substructures/blocks
	}
	
	/**
	 * Cleans up what ever is needed.
	 *
	 * Calls the destructor of the parent i.e. page class.
	 * So the database connection is closed.
	 *
	 * @return none
	 */
	protected function __destruct() {
		parent::__destruct ();
	}
	
	/**
	 * Fetch all data that is necessary for later output.
	 * Data is stored in an easily accessible way e.g. as associative array.
	 *
	 * @return none
	 */
	protected function getViewData() {
		$this->counter = 0;
		try {
			$Recordset = $this->_database->query ( "select * from bestellung" );
			while ( $Record = $Recordset->fetch_assoc () ) {
				$adresse = $Record ['adresse'];
				$bestellungId = $Record ['bestellungId'];
				
				$Recordset2 = $this->_database->query ( "select 
						fPizzaName, status, sum(preis) as preisSum
						from bestelltePizza, angebot
						where fBestellungId = $bestellungId
						and pizzaName=fPizzaName" );
				
				$i = 0;
				$show = true;
				$pizzaNames = array ();
				$status;
				while ( $Record2 = $Recordset2->fetch_assoc () ) {
					$pizzaNames [$i] = $Record2 ['fPizzaName'];
					if ($Record2 ['status'] == 4 || $Record2 ['status'] < 2) {
						$show = false;
					} else if ($Record2 ['status'] == 2) {
						$status = 0;
					} else {
						$status = 1;
					}
					$i ++;
					$preis = $Record2 ['preisSum'];
				}
				
				if ($show == true) {
					$this->pizzaOrderIdArray [$this->counter] = $bestellungId;
					$this->pizzaAdressArray [$this->counter] = $adresse;
					
					$this->pizzaPizzaArray [$this->counter] = $pizzaNames;
					
					$this->pizzaPriceArray [$this->counter] = $preis;
					$this->pizzaStatusArray [$this->counter] = $status;
					$this->counter ++;
				}
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * First the necessary data is fetched and then the HTML is
	 * assembled for output.
	 * i.e. the header is generated, the content
	 * of the page ("view") is inserted and -if avaialable- the content of
	 * all views contained is generated.
	 * Finally the footer is added.
	 *
	 * @return none
	 */
	protected function generateView() {
		$this->getViewData ();
		$this->generatePageHeader ( 'Fahrer' );
		echo '<meta http-equiv="refresh" content="5; url=fahrer.php" />';
		
		echo '<h1>Fahrer</h1>';
		
		for($i = 0; $i < $this->counter; $i ++) {
			echo '<article class="fahrer">';
			echo '<h2>';
			echo $this->pizzaAdressArray [$i];
			echo '</h2>';
			echo '<p>';
			for($p = 0; $p < count ( $this->pizzaPizzaArray [$i] ); $p ++) {
				echo $this->pizzaPizzaArray [$i] [$p];
				echo ", ";
			}
			echo '</p>';
			echo '<p>';
			echo 'Preis: <span id="preis">';
			echo $this->pizzaPriceArray [$i];
			echo '</span> €';
			echo '</p>';
			echo '<form id="formid';
			echo $i;
			echo '" action="fahrer.php"';
			echo 'accept-charset="UTF-8" method="post">';
			echo '<table>';
			echo '<tr>';
			echo '<th>gebacken</th>';
			echo '<th>unterwegs</th>';
			echo '<th>ausgeliefert</th>';
			echo '</tr>';
			echo '<tr>';
			
			for($p = 0; $p < 3; $p ++) {
				echo '<td class="status"><input type="radio" name="status" value="';
				echo $p;
				echo '"';
				echo 'onclick="document.forms[\'formid';
				echo $i;
				echo '\'].submit();" ';
				
				if ($this->pizzaStatusArray [$i] == $p) {
					echo 'checked = "checked" ';
				}
				
				echo '/>';
				echo '<input type="hidden" name="id" value="' . $this->pizzaOrderIdArray [$i] . '" ';
				echo 'form="formid' . $i . '" />';
				echo '</td>';
			}
			
			echo '</tr>';
			echo '</table>';
			echo '</form>';
			echo '</article>';
		}
		
		$this->generatePageFooter ();
	}
	
	/**
	 * Processes the data that comes via GET or POST i.e.
	 * CGI.
	 * If this page is supposed to do something with submitted
	 * data do it here.
	 * If the page contains blocks, delegate processing of the
	 * respective subsets of data to them.
	 *
	 * @return none
	 */
	protected function processReceivedData() {
		parent::processReceivedData ();
		if (isset ( $_POST ['id'] )) {
			$this->saveData ();
		}
	}
	protected function saveData() {
		$id = $_POST ['id'];
		$status = $_POST ['status'];
		$status += 2;
		try {
			$Recordset = $this->_database->query ( "update bestelltePizza, bestellung
					set status='$status' where bestellungId='$id'
					and bestellungId = fBestellungId" );
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * This main-function has the only purpose to create an instance
	 * of the class and to get all the things going.
	 * I.e. the operations of the class are called to produce
	 * the output of the HTML-file.
	 * The name "main" is no keyword for php. It is just used to
	 * indicate that function as the central starting point.
	 * To make it simpler this is a static function. That is you can simply
	 * call it without first creating an instance of the class.
	 *
	 * @return none
	 */
	public static function main() {
		try {
			$page = new PageTemplate ();
			$page->processReceivedData ();
			$page->generateView ();
		} catch ( Exception $e ) {
			header ( "Content-type: text/plain; charset=UTF-8" );
			echo $e->getMessage ();
		}
	}
}

// This call is starting the creation of the page.
// That is input is processed and output is created.
PageTemplate::main ();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >