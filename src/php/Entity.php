<?php

namespace PHPObjectExplorer;

class Entity{

	public $static = false;
	public $visibility = false;
	public $dynamic = false;

	public function __construct( $entity ){
		$this->entity = $entity;
	}
	public function Render( $name = false, &$entities_lookup = array() ){

		$entity_type = gettype( $this->entity );
		$properties_html = '';
		$title = '';
		$classes = array( $entity_type );

		if( $name !== false ){
			$title .= '<span class="name">' . (string)$name . '</span> ';
		}
		if( $this->static !== false ){
			$classes[] = 'static';
			$title .= 'static ';
		}
		if( $this->dynamic !== false ){
			$classes[] = 'dynamic';
			$title .= 'dynamic ';
		}
		if( $this->visibility !== false ){
			$classes[] = $this->visibility;
			$title .= '<span class="visibility">' . $this->visibility . '</span> ';
		}
		switch( $entity_type ){
			case 'object':{
				// Objects -- iterate over properties
				$entity_class = get_class( $this->entity ) . ' object';
				$hash = spl_object_hash( $this->entity );

				$title .= get_class( $this->entity );

				$properties = array();

				if( !in_array( $hash, $entities_lookup ) ){
					$entities_lookup[] = $hash;
					$ref = new \ReflectionClass( $this->entity );
					foreach( $ref->GetProperties() as $property ){
						$property->SetAccessible( true );
						$value = $property->GetValue( $this->entity );
						$child_entity = new Entity( $value, $entities_lookup );
						$child_entity->static = $property->isStatic();
						if( $property->isPublic() ){
							$child_entity->visibility = 'public';
						}
						if( $property->isProtected() ){
							$child_entity->visibility = 'protected';
						}
						if( $property->isPrivate() ){
							$child_entity->visibility = 'private';
						}
						$properties_html .= '<div class="property">' . $child_entity->Render( $property->getName(), $entities_lookup ) . '</div>';
						$properties[] = $property->getName();
					}
					// Pick up runtime properties
					foreach( $this->entity as $property => $value ){
						if( !in_array( $property, $properties ) ){
							$child_entity = new Entity( $value );
							$child_entity->visibility = 'public';
							$child_entity->dynamic = true;
							$properties_html .= '<div class="property">' . $child_entity->Render( $property, $entities_lookup ) . '</div>';
						}
					}
				} else {
					$title .= ' ** RECURSION **';
				}
				break;
			}
			case 'array':{
				$title .= $entity_type . ' (' . count( $this->entity ) . ')';
				if( count( $this->entity ) === 0 ){
					$classes[] = 'empty';
				}

				// Iterate over elements
				foreach( $this->entity as $index => $value ){
					$properties_html .= '<div class="property">' . ( new Entity( $value ) )->Render( $index, $entities_lookup ) . '</div>';
				}
				break;
			}
			default:{
				// Everything else (i..e primitives)
				$title .= $entity_type;
				if( $entity_type == 'string' ){
					$title .= ' (' . (string)strlen( $this->entity ) . ')';
				}
				$properties_html .= '<div class="property"><div class="entity value ' . gettype( $this->entity ) . '">' . htmlentities( $this->entity ) . '</div></div>';
			}
		}

		$output = '<div class="entity ' . implode( ' ', $classes ) . '">
			<span class="title">' . $title . '</span>
		';

		if( $properties_html != '' ){
			$output .= '<div class="properties">' . $properties_html . '</div>';
		}

		$output .= '</div>';

		return $output;
	}
}
