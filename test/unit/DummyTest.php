<?php
namespace App\Test;

use PHPUnit\Framework\TestCase;

class DummyTest extends TestCase {
	public function testFalseIsNotTrue() {
		self::assertFalse(true);
	}
}
