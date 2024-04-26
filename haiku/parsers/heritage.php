<?php

// Include common functions
require_once( 'includes/functions.php' );

// Try to load my dump of the families pages
$handle = fopen("source/heritage.wiki", "r");

// Set up a placeholder for our heritages
$heritages = [];

// Counting
$tableCount = 0;

// Ensure we loaded the file
if ( $handle ) {

     // Scope variables
     $clan = '';
     $type = '';
     $heritage = [];
     $ranges = 0;
     $initial = 0;

     // Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {
		
          // Check if this is a line denoting a new clan
		if( strpos( $line, '+-+ ' ) !== false ) {

               // Do we have a heritage before we swap to a new clan?
               if( count( $heritage ) > 0 ) {

                    // Push the heritage onto the array
          //         $heritages[ $clan ][ $type ][] = $heritage;

                    // Reset all of our variables
                    $clan = '';
                    $type = '';
                    $heritage = [];
                    $ranges = 0;
                    $initial = 0;
               }

               // Remove the extra stuff
               $line = trim( str_replace( '+-+', '', $line ) );

               // Figure out where the source starts
               $sourceStart = strpos( $line, '[' );

               // Pull out the clan
               $clan = trim( substr( $line, 0, $sourceStart ) );

               // Pull out the source
               $heritages[ $clan ][ 'source' ] = substr( $line, $sourceStart + 1, -1 );

               // Increase the counter
               $tableCount++;

          // Is this a roll result header
          } else if( strpos( strtolower( $line ), 'd10 roll result' ) !== false ) {

               // Incirment the number of headers we've seen
               $ranges += 1;

               // Is this the first range?
               if( $ranges == 1 ) {

                    // It's the initial heritage type roll
                    $type = 'ranges';

               // Is this the second?
               } else if( $ranges == 2 ) {

                    // It's the shameful group 
                    $type = 'shameful';

               // Is this the third?
               } else if( $ranges == 3 ) {

                    // It's the illustrious group 
                    $type = 'illustrious';
               
               // Is this the fourth?
               } else if( $ranges == 4 ) {

                    // It's the mixed group 
                    $type = 'mixed';

               // If we get here, oops..
               } else {

                    echo "Error with $clan:\n$line\n";
                    exit();
               }

          // Some kind of a range line
          } else if( strpos( $line, '* ' ) !== false ) {
               
               // Remove the list marker
               $line = trim( substr( $line, 2 ) );

               // Find out where the first space is
               $firstSpace = strpos( $line, ' ' );

               // Pull out the range
               $range = trim( substr( $line, 0, $firstSpace ) );

               // Are we still in the initial D10 block?
               if( $initial < 3 ) {

                    // Incirment our counter by one
                    $initial += 1;

                    // Explode the range
                    $range = explode( '-', $range );

                    // First is the shameful range
                    if( $initial == 1 ) {

                         // Set the sameful low
                         $heritages[ $clan ][ 'initial' ][ 'shameful' ][ 'low' ] = $range[ 0 ];

                         // Do we have a high?
                         if( count( $range ) > 1 ) {

                              // Set the sameful high
                              $heritages[ $clan ][ 'initial' ][ 'shameful' ][ 'high' ] = $range[ 1 ];
                         }

                         // Give a description
                         $heritages[ $clan ][ 'initial' ][ 'shameful' ][ 'description' ] = 'Shameful Past';
                    
                    // Second is illustrious
                    } else if( $initial == 2 ) {

                         // Set the illustrious low
                         $heritages[ $clan ][ 'initial' ][ 'illustrious' ][ 'low' ] = $range[ 0 ];

                         // Do we have a high?
                         if( count( $range ) > 1 ) {

                              // Set the illustrious high
                              $heritages[ $clan ][ 'initial' ][ 'illustrious' ][ 'high' ] = $range[ 1 ];
                         }                         

                         // Give a description
                         $heritages[ $clan ][ 'initial' ][ 'illustrious' ][ 'description' ] = 'Illustrious Past';

                    // Last is mixed
                    } else if( $initial == 3 ) {

                         // Set the mixed low
                         $heritages[ $clan ][ 'initial' ][ 'mixed' ][ 'low' ] = $range[ 0 ];

                         // Do we have a high?
                         if( count( $range ) > 1 ) {

                              // Set the mixed high
                              $heritages[ $clan ][ 'initial' ][ 'mixed' ][ 'high' ] = $range[ 1 ];
                         }  
                         
                         // Give a description
                         $heritages[ $clan ][ 'initial' ][ 'mixed' ][ 'description' ] = 'Mixed Blessing';
                    } 

               // We're either in shameful, illustrious, or mixed blocks
               } else {

                    // Reset the heritage array
                    $heritage = [];

                    // Pull out the description
                    $description = trim( substr( $line, $firstSpace + 1 ) );

                    // Explode the range
                    $range = explode( '-', $range );

                    // The low is first
                    $heritage[ 'low' ] = $range[ 0 ];

                    // Do we have a high?
                    if( count( $range ) > 1 ) {

                         // Set the high
                         $heritage[ 'high' ] = $range[ 1 ];
                    } 

                    // Set the description ofr this heritage
                    $heritage[ 'description' ] = $description;

                    // Add this to the heritage array
                    $heritages[ $clan ][ $type ][] = $heritage;
               }
          }
     }

     // Write our json file
	file_put_contents( '../web/modules/custom/last_haiku_import/json/heritages.json', json_encode( $heritages ) );

     echo colorize([
          [ 
               'string' => 'Parsing file: ',
               'color' => '',
          ],
          [ 
               'string' => "heretage.wiki",
               'color' => 'blue',
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
               'string' => "$tableCount heritage tables",
               'color' => 'bold_blue',
          ],
          [    
               'string' => ".\n",
               'color' => '',
          ],
          [    
               'string' => "     \u{029F} Saved to: ",
               'color' => '',
          ],          
          [
               'string' => "../web/modules/custom/last_haiku_import/json/heritages.json",
               'color' => 'dark_gray',
          ],
          [    
               'string' => ".\n\n",
               'color' => '',
          ],     
     ]);
}