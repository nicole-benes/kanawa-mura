<?php

// Try to load my dump of the families pages
$handle = fopen("source/skills.wiki", "r");

// Set up a placeholder for our skills array
$skills = [];

// Ensure we loaded the file
if ( $handle ) {

	// Placeholders for pesky scope
	$type = '';
	$skill = [];
	$subtypeBlock = false;
	$descriptionBlock = false;

	// Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {

		// Check if this is a line denoting a new skill type
		if( substr( $line, 0, 3 ) == '+++' ) {
			// Remove the leading markup
			$type = substr( $line, 3 );

			// Remove any pesky whitespace
			$type = trim( $type );
		
		// Check if this line is a new skill
		} else if( substr( $line, 0, 2 ) == '+ ' || substr( $line, 0, 2 ) == '++' ) {
			
			// Check if there is a skill in the skills array
			if( array_key_exists( 'name', $skill ) ) {

				// Push this skill onto the skills array before we move on
				$skills[] = $skill;
			}

			// Erase the skill array
			$skill = [];

			// Reset the triggers to tell if we're in multi-line content
			$subtypeBlock = false;

			// Assume this isn't a weapon skill
			$lineStart = 2;

			// See if this is a weapon skill
			if( substr( $line, 0, 2 ) == '++' ) {

				// These start 3 characters in
				$lineStart = 3;
			}
			
			// Find where the trait starts
			$traitStart = strpos( $line, '(' );

			// Pull the skill name out of the line and remove whitespace
			$name = trim( substr( $line, $lineStart, $traitStart - $lineStart ) );

			// Find where the trait ends
			$traitEnd = strpos( $line, ')' );

			// Pull out the text in the paranthesis
			$traitText = substr( $line, $traitStart + 1, $traitEnd - $traitStart - 1 );

			// Check if this trait is a pesky multi trait
			$traitText = str_replace( ' or ', ',', $traitText );

			// Various is dumb, so put the most common traits
			if( $traitText == 'Varies' || $traitText == 'Various' ) {

				if( $name === 'Craft' ) {
					$traitText = 'Intelligence,Agility,Awareness';
				} else if( $name == 'Weapons' ) {
					$traitText = 'Agility,Reflexes';
				} else if( $name == 'Games' ) {
					$traitText = 'Intelligence,Agility,Awareness';
				} else if( $name == 'Perform' ) {
					$traitText = 'Agility,Awareness';
				}
			}

			// Put the name of this skill in our skill array
			$skill[ 'name' ] = $name;
			
			// Put the traits onto our skill array
			$skill[ 'traits' ] = explode( ',', $traitText );

			// Set the type of the skill too
			$skill[ 'type' ] = $type;

		// Is this the subtype line?
		} else if( strpos( $line, '**Sub-types:**' ) !== false ) {

			// Mark that are in a subtype incase this subtype has multiple lines
			$subtypeBlock = true;

			// Pull out the subtype text from the line and put it in our array
			$skill[ 'subtypes' ] = trim( substr( $line, 14 ) );

			// Some lines have "sub-type" rather than being plural. Yay for consistency.
		} else if( strpos( $line, '**Sub-type:**' ) !== false ) {

			// Mark that are in a subtype incase this subtype has multiple lines
			$subtypeBlock = true;

			// Pull out the subtype text from the line and put it in our array
			$skill[ 'subtypes' ] = trim( substr( $line, 13 ) );

		// Is this the emphasis line?
		} else if( strpos( $line, '**Emphases:**' ) !== false || strpos( $line, '**Emphasis:**' ) !== false ) {
			
			// We are done with subtypes
			$subtypeBlock = false;

			// Pull out the emphases
			$skill[ 'emphases' ] = trim( substr( $line, 13 ) );

		// Is this the beginning of the description?
		} else if( strpos( $line, '**Description:**' ) !== false ) {

			// Indicate we are in the description block
			$descriptionBlock = true;

			// Pull out the first line of the description
			$skill[ 'description' ] = [
				trim( substr( $line, 16 ) )
			];

		// Check if we have hit the mastery abilities line
		} else if( strpos( $line, '**Mastery Abilities:**' ) !== false ) {

			// Indicate we are done with the description
			$descriptionBlock = false;

		// Check if we are in a mastery abilities line
		} else if( strpos( $line, '* Rank' ) !== false ) {

			// Pull out the numeric rank
			$rank = substr( $line, 7, 1 );

			// Pull out the mastery description
			$description = trim( substr( $line, 10 ) );

			// Push the mastery onto the skill
			$skill[ 'mastery' ][ $rank ] = $description;

		// Either an empty line or a line of text
		} else {

			// Check if the previous line was a subtype
			if( $subtypeBlock ) {

				$skill[ 'subtypes' ] .= ' ' . trim( $line );
			
			// Check if the previous line was in the description
			} else if ($descriptionBlock ) {
				$skill[ 'description' ][] = trim( $line );
			}
		}
	}	

	// Close the input file
	fclose( $handle );
}

//print_r( $skills[ 0 ] );

// Write this out as JSON
file_put_contents( '../web/modules/custom/last_haiku_import/json/skills.json', json_encode( $skills ) );