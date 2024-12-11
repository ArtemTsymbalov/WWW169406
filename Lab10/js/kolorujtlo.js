/**
 * Unit Converter and Background Color Manager
 * 
 * This module provides two main functionalities:
 * 1. Unit conversion calculator
 * 2. Background color manipulation
 * 
 * The calculator handles decimal inputs and unit conversions
 * while preventing multiple decimal points.
 */

// Calculator state
var computed = false;  // Tracks if calculation has been performed
var decimal = 0;      // Tracks decimal point usage (0 = none, 1 = used)

/**
 * Performs unit conversion calculation
 * Takes values from source unit and converts to target unit
 * 
 * @param {HTMLFormElement} entryform Form containing the calculator inputs
 * @param {HTMLSelectElement} from Source unit dropdown
 * @param {HTMLSelectElement} to Target unit dropdown
 */
function convert(entryform, from, to) {
    var convertfrom = from.selectedIndex;
    var convertto = to.selectedIndex;
    
    // Calculate conversion using unit ratios
    entryform.display.value = (
        entryform.input.value * 
        from[convertfrom].value / 
        to[convertto].value
    );
}

/**
 * Handles character input for the calculator
 * Manages decimal point usage and triggers conversion
 * 
 * @param {HTMLInputElement} input Calculator input field
 * @param {string} character Character to be added
 */
function addChar(input, character) {
    // Handle decimal point or regular number input
    if ((character === '.' && decimal === 0) || character !== '.') {
        // Replace initial zero or empty value
        input.value = (input.value === "" || input.value === "0") 
            ? character 
            : input.value + character;
        
        // Perform conversion and update state
        convert(input.form, input.form.measure1, input.form.measure2);
        computed = true;
        
        // Track decimal point usage
        if (character === '.') {
            decimal = 1;
        }
    }
}

/**
 * Resets calculator form to initial state
 * 
 * @param {HTMLFormElement} form Calculator form to reset
 */
function clear(form) {
    form.input.value = 0;
    form.display.value = 0;
    decimal = 0;
}

/**
 * Updates page background color
 * 
 * @param {string} hexNumber Hexadecimal color code (e.g., '#FF0000')
 */
function changeBackground(hexNumber) {
    document.body.style.backgroundColor = hexNumber;
}
