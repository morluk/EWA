/**
 * Wird nicht benutzt!!
 */
var warenkorb;

function Warenkorb() {
	"use strict";
	var pizzaSelection = document.getElementById("pizzaSelection");

	var preisElement = document.getElementById("preis");

	this.add = function(name, preis) {
		var option = document.createElement("option");
		option.innerHTML = name;
		option.setAttribute("data-preis", preis);
		pizzaSelection.appendChild(option);

		preisElement.innerHTML = parseFloat(preisElement.innerHTML)
				+ parseFloat(preis);
	}

	this.deleteAll = function() {
		while (pizzaSelection.firstChild) {
			pizzaSelection.removeChild(pizzaSelection.firstChild);
		}
		preisElement.innerHTML = 0;
	}

	this.deleteSelected = function() {
		var firstSelected = pizzaSelection.selectedIndex;
		while (pizzaSelection.selectedOptions.length > 0) {
			var option = pizzaSelection.selectedOptions.item(0);
			preisElement.innerHTML = parseFloat(preisElement.innerHTML)
					- parseFloat(option.getAttribute("data-preis"));
			option.remove();
		}
		pizzaSelection.selectedIndex = firstSelected;
	}
}

function init() {
	warenkorb = new Warenkorb();
}