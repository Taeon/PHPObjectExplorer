<?php

namespace PhpObjectExplorer;

class Explorer{

	/**
	 * Pass in the entitty to be dumped
	 *
	 * @param		mixed
	 */
	public function __construct( $entity ){
		$this->root_entity = new Entity( $entity, array() );
	}
	/**
	 * Output the object structure as an HTML page
	 *
	 * @returns		string
	 */
	public function Render(){
		return str_replace(
			array(
				'{{OUTPUT}}',
				'{{JS}}',
				'{{CSS}}',
			),
			array(
				// The structure
				$this->root_entity->Render(),
				// Javascript
				file_get_contents( realpath( dirname( __FILE__ ) . '/../js/explorer.js' )  ),
				// CSS
				file_get_contents( realpath( dirname( __FILE__ ) . '/../css/explorer.css' )  )
			),
			// The HTML template
			file_get_contents( realpath( dirname( __FILE__ ) . '/../html/template.html' )  )
		);
	}
}
