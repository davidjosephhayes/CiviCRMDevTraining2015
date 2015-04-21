<?php

require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * Test class for functions in devtraining.php
 */
class DevtrainingTest extends CiviUnitTestCase {
  /**
   * Test that, given a valid postal code, the fetcher returns the correct county
   */
  function testFetchPostalCodeSuccess() {
    $county = _devtraining_fetch_county_by_postal_code('33629');
    $this->assertEquals('Hillsborough', $county);
  }

  /**
   * Test that, given an invalid postal code, the fetch returns boolean false
   */
  function testFetchPostalCodeFailure() {
    $county = _devtraining_fetch_county_by_postal_code('POPSICLE');
    // note: assertSame does a strict comparison (i.e., ===) while assertEquals checks values only
    $this->assertSame(FALSE, $county);
  }
}
