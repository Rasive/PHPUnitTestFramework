PHPUnitTestFramework
====================

Just a simple unit test framework for PHP... might be a bit buggy, contributions welcome!


Usage
=====
1. Create a php file to be used for testing, and include the PHPUnitTest.php file
2. Make a function for each test case, and include @PHPUnitTest in the comment field, an example:

```PHP
/**
 * @PHPUnitTest
 * @TestSuite  		Service Test
 * @TestCase			Service instantiation
 * @TestDescription		Testing instantiation of the serviceclass singleton
 */
function serviceInit() {
	$service = Service::instance();
	assertNotNull($service);
}
```
Done!
