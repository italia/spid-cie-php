<?php

class MyCustomObject
{
      private $value;
      public function __construct( $value )
      {
            $this->value = $value;
      }
      public function gt( $other )
      {
            return ( $this->value > $other );
      }
      public function getValue()
      {
            return $this->value;
      }
}