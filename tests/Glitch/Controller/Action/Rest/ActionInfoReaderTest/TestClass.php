<?php

class Glitch_Controller_Action_Rest_ActionInfoReaderTest_TestClass
{
    /**
     * This docblock is empty on purpose.
     */
    public function noFilter()
    {

    }

    /**
     * @filter bar * A bar parameter description
     */
    public function foo()
    {

    }

    /**
     * @filter foo bar|baz Fixed parameter values
     */
    public function bar()
    {

    }

    /**
     * @filter foo range(1,45) Fixed parameter values
     */
    public function barRange()
    {

    }

    /**
     * @filter foo[] foo|bar|baz Fixed parameter values
     */
    public function barArray()
    {

    }

    /**
     * @filter bar * A bar parameter description
     * @filter foo[] foo|bar|baz Fixed parameter values
     */
    public function multipleChoiceFilters()
    {

    }
}
