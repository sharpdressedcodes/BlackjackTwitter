<?php

namespace WebsiteConnect\Blackjack\Core;

abstract class AbstractType {

	static public function getTypes(){

		$r = new \ReflectionClass(get_called_class());
		return $r->getConstants();

	}

}