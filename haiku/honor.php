<?php

// Load the honor CSV
$honorArray = array_map('str_getcsv', file('./source/honor.csv'));

// The HTML for the table will go here
$honorTable = '';

foreach( $honorArray as $row ) {

     // Skip the header
     if( $row[ 0 ] == 'Act' ) {

          // Move to the next row
          continue;
     }

     // New row
     $honorTable .= '<tr>';

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