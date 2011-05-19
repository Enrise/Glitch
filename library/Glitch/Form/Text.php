<?php
class Glitch_Form_Element_Text extends Zend_Form_Element_Text
{
    /**#@+
     * Constants that are used for types of elements
     *
     * @var string
     */
    const DEFAULT_TYPE = 'text';
    const FIELD_EMAIL = 'email';
    const FIELD_EMAIL_ADDRESS = 'emailaddress';
    const FIELD_URL = 'url';
    const FIELD_NUMBER = 'number';
    const FIELD_RANGE = 'range';
    const FIELD_DATE = 'date';
    const FIELD_MONTH = 'month';
    const FIELD_WEEK = 'week';
    const FIELD_TIME = 'time';
    const FIELD_DATE_TIME = 'datetime';
    const FIELD_DATE_TIME_LOCAL = 'datetime-local';
    const FIELD_SEARCH = 'search';
    const FIELD_COLOR = 'color';
    /**#@-*/

    /**
     * Mapping of key => value pairs for the elements
     *
     * @var array
     */
    protected static $_mapping = array(
        self::FIELD_EMAIL => 'email',
        self::FIELD_EMAIL_ADDRESS => 'email',
        self::FIELD_URL => 'url',
        self::FIELD_NUMBER => 'number',
        self::FIELD_RANGE => 'range',
        self::FIELD_DATE => 'date',
        self::FIELD_MONTH => 'month',
        self::FIELD_WEEK => 'week',
        self::FIELD_TIME => 'time',
        self::FIELD_DATE_TIME => 'datetime',
        self::FIELD_DATE_TIME_LOCAL => 'datetime-local',
        self::FIELD_SEARCH => 'search',
        self::FIELD_COLOR => 'color',
    );

    /**
     * Check if the validators should be auto loaded
     *
     * @var bool
     */
    private $_autoloadValidators = true;

    /**
     * Check if the filters should be auto loaded
     *
     * @var bool
     */
    private $_autoloadFilters = true;

    /**
     * Constructor that takes into account the type given, if given
     * Proxies its parent constructor to provide rest of functionality
     *
     * @param $spec
     * @param $options
     * @uses Zend_Form_Element
     */
    public function __construct($spec, $options = null)
    {
        if ($this->_isHtml5() && !isset($options['type']))
        {
            $options['type'] = $this->_getType($spec);
        }
        parent::__construct($spec, $options);
    }

    /**
     * Flag if the the validators should be autoloaded
     *
     * @param bool $flag
     * @return Glitch_Form_Element_Text Provides a fluent interface
     */
    public function setAutoloadValidators($flag)
    {
        $this->_autoloadValidators = (bool) $flag;
        return $this;
    }

    /**
     * Flag if the the validators should be autoloaded
     *
     * @return bool
     */
    public function isAutoloadValidators()
    {
        return $this->_autoloadValidators;
    }

    /**
     * Flag if the the filters should be autoloaded
     *
     * @param bool $flag
     * @return Glitch_Form_Element_Text Provides a fluent interface
     */
    public function setAutoloadFilters($flag)
    {
        $this->_autoloadFilters = (bool) $flag;
        return $this;
    }

    /**
     * Flag if the the validators should be autoloaded
     *
     * @return bool
     */
    public function isAutoloadFilters()
    {
        return $this->_autoloadFilters;
    }

    /**
     * Check if the doctype is HTML5
     *
     * @return bool
     */
    private function _isHtml5()
    {
        return $this->getView()->getHelper('doctype')->isHtml5();
    }

    /**
     * Check if the given type is specified in the mapping and use it if it's available
     * Else return the constant DEFAULT_TYPE value
     *
     * @param $spec
     * @return string
     */
    private function _getType($spec)
    {
        if (array_key_exists(strtolower($spec), self::$_mapping))
        {
            return self::$_mapping[$spec];
        }
        return self::DEFAULT_TYPE;
    }
}