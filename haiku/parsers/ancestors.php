<?php

// Include common functions
require_once( 'includes/functions.php' );

// Try to load my dump of the families pages
$handle = fopen("source/ancestors.wiki", "r");

// Set up a placeholder for our 
$ancestors = [];

// Variable to count how many ancestors
$ancestorCount = 0;

// Ensure we loaded the file
if ( $handle ) {

     // Scope variables
     $clan = '';
     $ancestor = [];

     // Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {

		// Check if this is a line denoting a new clan
		if( strpos( $line, '+ ' ) !== false ) {

               // Is there still an ancestor to push?
               if( count( $ancestor ) > 0 ) {

                    // Push this ancestor before we get the new clan
                    $ancestors[ $clan ][] = $ancestor;

                    // Empty the ancestor array so this ancestor isn't pushed into the new clan
                    $ancestor = [];
               }

               // Pull out the new clan
               $clan = substr( $line, 2, strlen( $line ) - 13 );

               // Set up a new array for this clan
               $ancestors[ $clan ] = [];

          // New Ancestor
          } else if( strpos( $line, '__**' ) !== false ) {

               // Figure out where the cost starts
               $cost = strpos( $line, ' [' );

               // Is ther an ancestor we were working on?
               if( count( $ancestor ) > 0 ) {

                    // Add this ancestor to the array
                    $ancestors[ $clan ][] = $ancestor;

                    // Incriment the ancestor counter
                    $ancestorCount++;

                    // Reset and set up the ancestory array
                    $ancestor = [
                         'name' => '',
                         'cost' => [],
                         'description' => [],
                    ];
               }

               // Pull out the name
               $ancestor[ 'name' ] = substr( $line, 4, $cost - 4 );

               // Figure out where the cost ends
               $costEnd = strpos( $line, ']' );

               // Strip out the point cost from the line    
               $pointCost = preg_replace( "/[^0-9\/]/", "", substr( $line, $cost, $costEnd ) );

               // Agasha is special
               if( $ancestor[ 'name' ] == 'Agasha' ) {

                    // Put the points on manually
                    $ancestor[ 'cost' ] = [ 6, 10 ];

               // Just a normal ancestor with only one cost
               } else {

                    $ancestor[ 'cost' ] = [ (int) $pointCost ];
               }

               // Pull out anything left (ie, a source if one exists)
               $source = substr( $line, $costEnd + 3 );

               // Find out where the source ends
               $source = substr( $source, 0, strpos( $source, ']' ) );

               // Anything left?
               if( strlen( $source ) > 0 ) {

                    // Set the source for this ancestor
                    $ancestor[ 'source' ] = $source;
               }
               

          // The demands line
          } else if( strpos( $line, 'Demands:' ) !== false ) {

               // Can just parse out the garbage
               $ancestor[ 'demands' ] = parseLine( $line, 'Demands' );

          // Must be the description
          } else {

               // Remove junk
               $line = trim( $line );

               // Anythign left?
               if( strlen( $line ) > 0 ) {
                    
                    // Add it to the description
                    $ancestor[ 'description' ][] = $line;
               }
          }
     }

     // Push that last ancestor
     $ancestors[ $clan ][] = $ancestor;

     // Write our json file
	file_put_contents( '../web/modules/custom/last_haiku_import/json/ancestors.json', json_encode( $ancestors ) );

     echo colorize([
          [ 
               'string' => 'Parsing file: ',
               'color' => '',
          ],
          [ 
               'string' => "ancestors.wiki",
               'color' => 'white',
          ],
          [    
               'string' => ".\n",
               'color' => '',
          ],
          [
               'string' => "     \u{029F} Parsed ",
               'color' => '',
          ],
          [
               'string' => "$ancestorCount ancestors.\n",
               'color' => 'dark_gray',
          ],
          [    
               'string' => "     \u{029F} Saved to ",
               'color' => '',
          ],
          [
               'string' => "../web/modules/custom/last_haiku_import/json/ancestors.json",
               'color' => 'dark_gray',
          ],
          [    
               'string' => ".\n\n",
               'color' => '',
          ],          
     ]);     
}
