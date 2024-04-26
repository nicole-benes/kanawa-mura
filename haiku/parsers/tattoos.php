<?php

// Include common functions
require_once( 'includes/functions.php' );

// Try to load my dump of the equipment page
$handle = fopen("source/tattoos.wiki", "r");

// Set up a placeholder for our 
$tats = [];

// Ensure we loaded the file
if ( $handle ) {

     // Ye Olde Scope Declarations
     $tat = [];

     // Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {

		// Check if this is a line denoting a category
		if( strpos( $line, '* **' ) !== false ) { 

               // Reset the tat array
               $tat = [];

               // Find where the title ends
               $titleEnd = strpos( $line, ':**' );

               // Pull out the name
               $tat[ 'name' ] = trim( substr( $line, 4, $titleEnd - 4 ) );

               // Pull out the description
               $tat[ 'description' ] = trim( substr( $line, $titleEnd + 3 ) );

               // Assume source is CR
               $tat[ 'source' ] = 'CR';

               // Assume Togashi Monks
               $tat[ 'keywords' ] = [
                    'Togashi Tattooed Order',
                    'Monk',
               ];

               // Push this tat anto the array
               $tats[] = $tat;

          // Never should be another kind of line
          } else {

               // Output an error and quit
               echo "Unparsed line: \n$line\n";
               exit();
          }
     }

	// Write our json file
	file_put_contents( '../web/modules/custom/last_haiku_import/json/tattoos.json', json_encode( $tats ) );      
}

echo colorize([
     [ 
          'string' => 'Parsing file: ',
          'color' => '',
     ],
     [ 
          'string' => "tattoos.wiki\n",
          'color' => 'bold_purple',
     ],

     [
          'string' => "     \u{029F} Parsed ",
          'color' => '',
     ],
     [
          'string' => count( $tats ) . " tattoos",
          'color' => 'purple',
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
          'string' => "../web/modules/custom/last_haiku_import/json/tattoos.json",
          'color' => 'dark_gray',
     ],
     [    
          'string' => ".\n\n",
          'color' => '',
     ],          
]);