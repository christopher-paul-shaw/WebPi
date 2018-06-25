<?php
use PHPUnit\Framework\TestCase;
class DummyTest extends TestCase {
	public function testFalseIsNotTrue() {
		self::assertNotTrue(false);
	}
}
