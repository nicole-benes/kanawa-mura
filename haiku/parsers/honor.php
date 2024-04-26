<?php

// Include common functions
require_once( 'includes/functions.php' );

// Load the honor CSV
$honorArray = array_map('str_getcsv', file('./source/honor.csv'));

// The HTML for the table will go here
$honorTable = '';

// I like counting
$rowCount = 0;

foreach( $honorArray as $row ) {

     // Skip the header
     if( $row[ 0 ] == 'Act' ) {

          // Move to the next row
          continue;
     }

     // New row
     $honorTable .= '<tr>';

     // Up the row counter
     $rowCount++;

     // Loop through each of the cells
     foreach( $row as $data ) {
          
          // Make a table cell
          $honorTable .= '<td>' . trim( $data ) . '</td>';
     }

     // Close the table row
     $honorTable .= '</tr>';
}

// Write our json file
file_put_contents( '../web/modules/custom/last_haiku_import/html/honor-table.html', $honorTable );

echo colorize([
     [ 
          'string' => 'Parsing file: ',
          'color' => '',
     ],
     [ 
          'string' => "honor.csv\n",
          'color' => 'bold_green',
     ],

     [
          'string' => "     \u{029F} Parsed ",
          'color' => '',
     ],
     [
          'string' => "$rowCount rows in the honor table",
          'color' => 'green',
     ],
     [    
          'string' => ".\n",
          'color' => '',
     ],
     [    
          'string' => "     \u{029F} Saved to ",
          'color' => '',
     ],          
     [
          'string' => "../web/modules/custom/last_haiku_import/html/honor-table.html",
          'color' => 'dark_gray',
     ],
     [    
          'string' => ".\n\n",
          'color' => '',
     ],          
]);