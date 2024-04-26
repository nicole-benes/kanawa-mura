<?php

// Include common functions
require_once( 'includes/functions.php' );

// Try to load my dump of the equipment page
$handle = fopen("source/equipment.wiki", "r");

// Set up a placeholder for our 
$equipment = [];

// Counting is fun!
$armorCount = 0;
$weaponCount = 0;

// Ensure we loaded the file
if ( $handle ) {

	// Scope
     $itemCategory = '';
     $itemType = '';
     $item = [];
     $description = '';

     // Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {

		// Check if this is a line denoting a category
		if( strpos( $line, '+-+ ' ) !== false ) { 

               // Do we have an equipment item?
               if( isset( $item[ 'name' ] ) ) {

                    // Push the description on
                    $item[ 'description' ] = trim( $description );

                    // Put the item on the equipment array
                    $equipment[ $itemCategory ][ $itemType ][] = $item;

                    // Reset the item array
                    $item = [];

                    // Reset the description
                    $description = '';
               }

               // Pull out the category
               $itemCategory = trim( substr( $line, 4 ) );


          // Is this a new type within the cateogry?
          } else if( strpos( $line, '++ ' ) !== false ) {

               // Do we have an equipment item?
               if( isset( $item[ 'name' ] ) ) {

                    // Push the description on
                    $item[ 'description' ] = trim( $description );

                    // Put the item on the equipment array
                    $equipment[ $itemCategory ][ $itemType ][] = $item;

                    // Reset the item array
                    $item = [];

                    // Reset the description
                    $description = '';
               }

               // Pull out the type
               $itemType = trim( substr( $line, 3 ) );

          // New item
          } else if( strpos( $line, '**' ) !== false ) {

               // Do we have an equipment item?
               if( isset( $item[ 'name' ] ) ) {

                    // Push the description on
                    $item[ 'description' ] = trim( $description );

                    // Reset the description
                    $description = '';

                    // Put the item on the equipment array
                    $equipment[ $itemCategory ][ $itemType ][] = $item;

                    // Reset the item array
                    $item = [];
               }

               // Counting is fun!
               if( $itemCategory == 'Armor' ) {
                    $armorCount++;
               } else {
                    $weaponCount++;
               }


               // Get the position of the source
               $sourcePosition = strpos( $line, '[' );

               // Does this item have a source?
               if( $sourcePosition !== false ) {

                    // Pull out the name
                    $item[ 'name' ] = trim( substr( $line, 2, $sourcePosition - 2 ) );

                    // Pull out the source
                    $item[ 'source' ] = trim( substr( $line, $sourcePosition + 1, 2 ) );

               // No Source
               } else {

                    // Pull out the name
                    $item[ 'name' ] = trim( substr( $line, 2, strlen( $line ) - 5 ) );
               }

          // Keywords
          } else if( strpos( $line, '* Keywords' ) !== false ) {

               // Trim out the junk
               $line = parseLine( $line, 'Keywords' );

               // Explode the string
               $keywords = explode( ',', $line );

               // Set up our keywords array
               $item[ 'keywords' ] = [];

               // Grab all the keywords
               foreach( $keywords as $keyword ) {

                    // Push it onto the array
                    $item[ 'keywords' ][] = trim( $keyword );
               }

          // DR Line
          } else if( strpos( $line, '* Damage rating' ) !== false ) {
               
               // Trim the line
               $item[ 'damage_rating' ] = parseLine( $line, 'Damage rating' );
     
          // Special Line
          } else if( strpos( $line, '* Special' ) !== false ) {

               // Pull out the special line
               $item[ 'special' ] = parseLine( $line, 'Special' );

          // Cost Line
          } else if( strpos( $line, '* Cost' ) !== false ) {

               // Pull out the cost
               $item[ 'cost' ] = parseLine( $line, 'Cost' );

          // Price Line
          } else if( strpos( $line, '* Price' ) !== false ) {

               // Pull out the cost
               $item[ 'cost' ] = parseLine( $line, 'Price' );

               // Range Line
          } else if( strpos( $line, '* Range' ) !== false ) {

               // Pull out the range
               $item ['range' ] = parseLine( $line, 'Range' );

          // Strength Line
          } else if( strpos( $line, '* Strength' ) !== false ) {

               // Pull out the range
               $item ['strength' ] = parseLine( $line, 'Strength' );

          // Armor TN Line
          } else if( strpos( $line, '* Bonus to Armor TN' ) !== false ) {

               // Filter out the bonus TN
               $item[ 'tn' ] = parseLine( $line, 'Bonus to Armor TN' );

          // Reduction Line
          } else if( strpos( $line, '* Damage reduction' ) !== false ) {

               // Filter out the bonus TN
               $item[ 'damage_reduction' ] = parseLine( $line, 'Damage reduction' );

          // Anything else should just be the description
          } else {

               // Is there something in this line?
               if( strlen( trim( $line ) ) > 0 ) {

                    // push it onto the descritpion.
                    $description .= ' ' . trim( $line );
               }
          }           
     }   

     // Push the description on
     $item[ 'description' ] = trim( $description );

     // Push the last item on the equipment array
     $equipment[ $itemCategory ][ $itemType ][] = $item;

	// Write our json file
	file_put_contents( '../web/modules/custom/last_haiku_import/json/equipment.json', json_encode( $equipment ) ); 
     
     echo colorize([
          [ 
               'string' => 'Parsing file: ',
               'color' => '',
          ],
          [ 
               'string' => "equipment.wiki\n",
               'color' => 'brown',
          ],

          [
               'string' => "     \u{029F} Parsed ",
               'color' => '',
          ],
          [
               'string' => "$armorCount armors",
               'color' => 'yellow',
          ],
          [    
               'string' => " and ",
               'color' => '',
          ],
          [    
               'string' =>  "$weaponCount weapons",
               'color' => 'yellow',
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
               'string' => "../web/modules/custom/last_haiku_import/json/equipment.json",
               'color' => 'dark_gray',
          ],
          [    
               'string' => ".\n\n",
               'color' => '',
          ],          
     ]);        
}
