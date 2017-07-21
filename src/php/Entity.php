<?php

namespace PHPObjectExplorer;

class Entity{

	protected $type;
	protected $class;
	protected $value;
	protected $recursion = false;
	public $static = false;
	public $visibility = false;
	public $dynamic = false;
	protected $properties = array();

	public function __construct( $object, $entities_lookup = array() ){
		$this->type = gettype( $object );
		switch( gettype( $object ) ){
			case 'object':{
				// Objects -- iterate over properties
				$this->class = get_class( $object ) . ' object';
				$hash = spl_object_hash( $object );
				if( !in_array( $hash, $entities_lookup ) ){
					$entities_lookup[] = $hash;
					$ref = new \ReflectionClass( $object );
					foreach( $ref->GetProperties() as $property ){
						$property->SetAccessible( true );
						$value = $property->GetValue( $object );
						$entity = new Entity( $value, $entities_lookup );
						$this->properties[ $property->GetName() ] = $entity;
						$entity->static = $property->isStatic();
						if( $property->isPublic() ){
							$entity->visibility = 'public';
						}
						if( $property->isProtected() ){
							$entity->visibility = 'protected';
						}
						if( $property->isPrivate() ){
							$entity->visibility = 'private';
						}
					}
					// Pick up runtime properties
					foreach( $object as $property => $value ){
						if( !array_key_exists( $property, $this->properties ) ){
							$entity =  new Entity( $value, $entities_lookup );
							$this->properties[ $property ] = $entity;
							$entity->visibility = 'public';
							$entity->dynamic = true;
						}
					}
				} else {
					$this->recursion = true;
				}
				break;
			}
			case 'array':{
				// Array -- iterate over elements
				foreach( $object as $index => $value ){
					$this->properties[ $index ] = new Entity( $value, $entities_lookup );
				}
				break;
			}
			default:{
				// Everything else (i..e primitives)
				// Just store the value
				$this->value = $object;
			}
		}
	}
	public function Render( $name = false ){
		$properties = '';
		$classes = array( $this->type );

		$title = '';
		if( $name !== false ){
			$title = '[' . (string)$name . '] ';
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
			$title .= $this->visibility . ' ';
		}

		switch( $this->type ){
			case 'object':
			{
				$properties .= '<div class="properties">';
				foreach( $this->properties as $property_name => $property ){
					$properties .= '<div class="property">' . $property->Render( $property_name ) . '</div>';
				}
				$properties .= '</div>';
				$title .= $this->class;
				if( $this->recursion ){
					$classes[] = 'recursion';
					$title .= ' ** RECURSION **';
				}
				break;
			}
			case 'array':
			{
				$properties .= '<div class="properties">';
				foreach( $this->properties as $property_name => $property ){
					$properties .= '<div class="property">' . $property->Render( $property_name ) . '</div>';
				}
				$properties .= '</div>';
				$title .= $this->type . ' (' . count( $this->properties ) . ')';
				if( count( $this->properties ) === 0 ){
					$classes[] = 'empty';
				}
				break;
			}
			default:{
				$title .= $this->type;
				$properties .= '<div class="properties">';
				$properties .= '<div class="property"><div class="entity">' . htmlentities( $this->value ) . '</div></div>';
				$properties .= '</div>';
				break;
			}
		}

		$output = '<div class="entity ' . implode( ' ', $classes ) . '">
			<span class="title">' . $title . '</span>
		';

		$output .= $properties;

		$output .= '</div>';
		return $output;
	}
}
