<?php

use Glitch\Controller\Action\Rest\ResourceFilter as filter;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * This class tests the ActionInfoReader, but also (implicitly) tests the
 * ResourceFilterFactory.
 */
class Glitch_Controller_Action_Rest_ActionInfoReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Glitch_Controller_Action_Rest_ActionInfoReader
     */
    protected $reader;

    /**
     * @var Glitch_Controller_Action_Rest_ActionInfoReaderTest_TestClass
     */
    protected $testClass;

    /**
     * Prepare all tests. load the TestClass, and init a new ActionInfoReader
     */
    protected function setUp()
    {
        require_once __DIR__ . '/ActionInfoReaderTest/TestClass.php';
        $this->reader = new Glitch_Controller_Action_Rest_ActionInfoReader();
    }

    /**
     * Tests the reader on a non-existing method.
     */
    public function testGetResourceInfoNonExistingMethod()
    {
        $this->assertNull($this->reader->getResourceInfo('nonExistentClass', 'action'));
        $this->assertNull(
            $this->reader->getResourceInfo(
                'Glitch_Controller_Action_Rest_ActionInfoReaderTest_TestClass',
                'NonExistingMethod'
            )
        );
    }

    /**
     * Test the reader on a method with no filter specified
     */
    public function testGetResourceInfoNoFilterMethod()
    {
        $this->assertInternalType(
            'array',
            $this->reader->getResourceInfo(
                'Glitch_Controller_Action_Rest_ActionInfoReaderTest_TestClass',
                'noFilter'
            )
        );
    }

    /**
     * Test the reader on a method with filter specified, but constraint as NONE
     */
    public function testGetResourceInfoWithFilterNoConstraint()
    {
        // Test filter with no constraint
        $testAnnotationsNoConstraint = $this->reader->getResourceInfo(
            'Glitch_Controller_Action_Rest_ActionInfoReaderTest_TestClass',
            'foo'
        );
        $this->assertInternalType(
            'array',
            $testAnnotationsNoConstraint
        );
        $this->assertCount(1, $testAnnotationsNoConstraint);

        $filterNoConstraint = $testAnnotationsNoConstraint[0];
        $this->assertEquals(
            Glitch_Controller_Action_Rest_Annotation_ResourceFilter::FILTER_CONSTRAINT_NONE,
            $filterNoConstraint->getConstraint()
        );
        $this->assertEquals('bar', $filterNoConstraint->getName());
        $this->assertEquals('A bar parameter description', $filterNoConstraint->getDescription());
    }

    /**
     * Test the reader on a method with filter specified, but constraint as VALUES
     */
    public function testGetResourceInfoWithFilterListConstraint()
    {
        // Test filter with list constraint
        $testAnnotationsListConstraint = $this->reader->getResourceInfo(
            'Glitch_Controller_Action_Rest_ActionInfoReaderTest_TestClass',
            'bar'
        );
        $this->assertCount(1, $testAnnotationsListConstraint);
        $this->assertInternalType(
            'array',
            $testAnnotationsListConstraint
        );

        $filterListConstraint = $testAnnotationsListConstraint[0];
        $this->assertEquals(
            Glitch_Controller_Action_Rest_Annotation_ResourceFilter::FILTER_CONSTRAINT_VALUES,
            $filterListConstraint->getConstraint()
        );
        $this->assertEquals(array('bar', 'baz'), $filterListConstraint->getAllowedValues());

        $this->assertEquals('foo', $filterListConstraint->getName());
        $this->assertEquals('Fixed parameter values', $filterListConstraint->getDescription());
    }

    /**
     * Test the reader on a method with filter specified, but constraint as VALUES
     * And, can be an array of values
     */
    public function testGetResourceInfoWithFilterListConstraintAndIsArray()
    {
        // Test filter with list constraint and multiple select
        $testAnnotationsListArrayConstraint = $this->reader->getResourceInfo(
            'Glitch_Controller_Action_Rest_ActionInfoReaderTest_TestClass',
            'barArray'
        );
        $this->assertCount(1, $testAnnotationsListArrayConstraint);
        $this->assertInternalType(
            'array',
            $testAnnotationsListArrayConstraint
        );

        $filterListArrayConstraint = $testAnnotationsListArrayConstraint[0];
        $this->assertEquals(
            Glitch_Controller_Action_Rest_Annotation_ResourceFilter::FILTER_CONSTRAINT_VALUES,
            $filterListArrayConstraint->getConstraint()
        );
        $this->assertEquals(array('foo', 'bar', 'baz'), $filterListArrayConstraint->getAllowedValues());
        $this->assertTrue($filterListArrayConstraint->canSelectMultiple());

        $this->assertEquals('foo', $filterListArrayConstraint->getName());
        $this->assertEquals('Fixed parameter values', $filterListArrayConstraint->getDescription());
    }

    /**
     * Test the reader on a method with filter specified, but constraint as RANGE
     */
    public function testGetResourceInfoWithFilterRangeConstraint()
    {
        // Test filter with range constraint
        $testAnnotationsRangeConstraint = $this->reader->getResourceInfo(
            'Glitch_Controller_Action_Rest_ActionInfoReaderTest_TestClass',
            'barRange'
        );
        $this->assertCount(1, $testAnnotationsRangeConstraint);
        $this->assertInternalType(
            'array',
            $testAnnotationsRangeConstraint
        );

        $filterRangeConstraint = $testAnnotationsRangeConstraint[0];
        $this->assertEquals(
            Glitch_Controller_Action_Rest_Annotation_ResourceFilter::FILTER_CONSTRAINT_RANGE,
            $filterRangeConstraint->getConstraint()
        );
        $this->assertEquals(1, $filterRangeConstraint->getRangeMinimum());
        $this->assertEquals(45, $filterRangeConstraint->getRangeMaximum());

        $this->assertEquals('foo', $filterRangeConstraint->getName());
        $this->assertEquals('Fixed parameter values', $filterRangeConstraint->getDescription());
    }
}
