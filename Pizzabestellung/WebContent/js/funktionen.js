/**
 * Funktionen für Warenkorb in bestellung.html
 */

// Klasse Warenkorb
function Warenkorb(idSelect, idPrice) {
	"use strict";
	// private member
	var m_nodeSelect = idSelect;
	var m_nodePrice = idPrice;
	var itemArray = [];
	var totalPrice = 0.0;
	var i = 0;

	// methoden
	// private: erneuert textbox und preis
	var updateView = function() {
		var liste = document.getElementById(m_nodeSelect);
		// Liste loeschen
		while (liste.firstChild !== null) {
			liste.removeChild(liste.firstChild);
		}
		// Liste fuellen
		for (i = 0; i < itemArray.length; ++i) {
			var neuesElement = document.createElement("option");
			var neuerText = document.createTextNode(itemArray[i].name);
			neuesElement.appendChild(neuerText);
			liste.appendChild(neuesElement);
		}
		// Preis aktualisieren
		var preisTag = document.getElementById(m_nodePrice);
		preisTag.innerHTML = totalPrice.toFixed(2);
	};
	// private: löscht item mit name aus liste
	var remove = function(name) {
		for (i=0;i<itemArray.length;++i) {
			if (itemArray[i].name === name) {
				totalPrice -= itemArray[i].price;
				itemArray.splice(i, 1);
				break;
			}		
		}
	};
	// public: fügt item zu item liste hinzu
	this.add = function(new_id, new_price, new_name) {
		var new_item = {id: new_id, price: Number(new_price), name: new_name};
		itemArray[itemArray.length] = new_item;
		totalPrice += Number(new_price);
		updateView();
	};
	// public: löscht gesamten Warenkorb
	this.clear = function() {
		itemArray.splice(0, itemArray.length);
		totalPrice = 0.0;
		updateView();
	};
	// public: löscht selektierte Items
	this.removeSelected = function() {
		var liste = document.getElementById(m_nodeSelect);
		for (i=0; i<liste.options.length; ++i) {
			if (liste.options[i].selected) {
				var name = liste.options[i].text;
				remove(name);
			}
		}
		updateView();
	};
	// public: selektiert alle items bevor Warenkorb abgeschickt wird
	this.submit = function() {
		var options = document.getElementById(m_nodeSelect).options;
		for (i=0; i<options.length; ++i) {
			options[i].selected = "1";
		}
	};
}

/**
 * Funktionsfluss
 */
var warenkorb;

// onload="init()"
function init() {
	"use strict";
	warenkorb = new Warenkorb("selectBox", "preis");
	warenkorb.clear();	//wg. UpdateView fuer Preis
}
// bei click auf pizza in Speisekarte
function add(node) {
	"use strict";
	var price = node.getAttribute("data-price");
	var id = node.getAttribute("data-id");
	var name = node.getAttribute("data-name");
	warenkorb.add(id, price, name);
}
