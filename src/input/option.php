<?php
/**
 * File containing the ezcConsoleOption class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct like class to store data about a single option for ezcConsoleInput.
 *
 * This class represents a single command line option, which can be handled by 
 * the ezcConsoleInput class. This classes only purpose is the storage of
 * the parameter data, the handling of options and arguments is done by the
 * class {@link ezcConsoleInput}.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleOption {

    /**
     * Properties, which provide only read access.
     * Stores the short and long name of a parameter which are readonly after 
     * being set once during construction.
     * 
     * @var array(string)
     */
    protected $properties = array( 
        'short' => '',
        'long'  => '',
    );

    /**
     * Value type of this parameter, default is ezcConsoleInput::TYPE_NONE.
     * @see ezcConsoleInput::TYPE_NONE
     * @see ezcConsoleInput::TYPE_INT
     * @see ezcConsoleInput::TYPE_STRING
     * 
     * @var int
     */
    public $type = ezcConsoleInput::TYPE_NONE;

    /**
     * Default value if the parameter is submitted without value.
     * If a parameter is eg. of type ezcConsoleInput::TYPE_STRING and 
     * therefore expects a value when being submitted, it may be submitted
     * without a value and automatically get the default value sepcified here.
     * 
     * @var mixed
     */
    public $default;

    /**
     * Is the submition of multiple instances of this parameters allowed? 
     * 
     * @var bool
     */
    public $multiple = false;
    
    /**
     * Short help text. Ususally displayed when showing parameter help overview.
     * 
     * @var string
     */
    public $shorthelp = 'No help available.';
    
    /**
     * Long help text. Ususally displayed when showing parameter detailed help.
     * 
     * @var string
     */
    public $longhelp = 'Sorry, there is no help text available for this parameter.';

    /**
     * Dependency rules of this parameter.
     * 
     * @see ezcConsoleParamemterStruct::addDependency()
     * @see ezcConsoleParamemterStruct::removeDependency()
     * @see ezcConsoleParamemterStruct::hasDependency()
     * @see ezcConsoleParamemterStruct::getDependencies()
     * @see ezcConsoleParamemterStruct::resetDependencies()
     * 
     * @var array(string=>ezcConsoleParamemterRule)
     */
    protected $dependencies = array();

    /**
     * Exclusion rules of this parameter.
     * 
     * @see ezcConsoleParamemterStruct::addExclusion()
     * @see ezcConsoleParamemterStruct::removeExclusion()
     * @see ezcConsoleParamemterStruct::hasExclusion()
     * @see ezcConsoleParamemterStruct::getExclusions()
     * @see ezcConsoleParamemterStruct::resetExclusions()
     * 
     * @var array(string=>ezcConsoleParamemterRule)
     */
    protected $exclusions = array();

    /**
     * Whether arguments to the program are allowed, when this parameter is submitted. 
     * 
     * @var bool
     */
    public $arguments = true;

    /**
     * Wether a parameter is mandatory to be set.
     * If this flag is true, the parameter must be submitted whenever the 
     * program is run.
     * 
     * @var bool
     */
    public $mandatory = false;

    /**
     * The value the parameter was assigned to when being submitted.
     * Boolean false indicates the parameter was not submitted, boolean
     * true means the parameter was submitted, but did not have a value.
     * In any other case, this caries the submitted value.
     * 
     * @var mixed
     */
    public $value = false;

    /**
     * Create a new parameter struct.
     * Creates a new basic parameter struct with the base information "$short"
     * (the short name of the parameter) and "$long" (the long version). You
     * simply apply these parameters as strings (without '-' or '--'). So
     *
     * <code>
     * $param = new ezcConsoleOption( 'f', 'file' );
     * </code>
     *
     * will result in a parameter that can be accessed using
     * 
     * <code>
     * $ mytool -f
     * </code>
     *
     * or
     * 
     * <code>
     * $ mytool --file
     * </code>
     * .
     *
     * The newly created parameter contains only it's 2 names and each other 
     * attribute is set to it's default value. You can simply manipulate
     * those attributes by accessing them directly.
     * 
     * @param string $short     Short name of the parameter without '-' (eg. 'f').
     * @param string $long      Long name of the parameter without '--' (eg. 'file').
     * @param int $type         Value type of the parameter. One of ezcConsoleInput::TYPE_*.
     * @param mixed $default    Default value the parameter holds if not submitted.
     * @param bool $multiple    If the parameter may be submitted multiple times.
     * @param string $shorthelp Short help text.
     * @param string $longhelp  Long help text.
     * @param array(int=>ezcConsoleOptionRule) $dependencies Dependency rules.
     * @param array(int=>ezcConsoleOptionRule) $exclusions   Exclusion rules.
     * @param bool $arguments   Whether supplying arguments is allowed when this parameter is set.
     * @param bool $mandatory   Whether the parameter must be always submitted.
     */
    public function __construct( 
        $short, 
        $long, 
        $type = ezcConsoleInput::TYPE_NONE, 
        $default = null, 
        $multiple = false,
        $shorthelp = 'No help available.',
        $longhelp = 'Sorry, there is no help text available for this parameter.', 
        array $dependencies = array(),
        array $exclusions = array(), 
        $arguments = true,
        $mandatory = false
    ) {
        if ( !self::validateOptionName( $short ) )
        {
            throw new ezcConsoleInputException(  
                "Invalid parameter name: <$short>.",
                ezcConsoleInputException::PARAMETER_NAME_INVALID 
            );
        }
        $this->properties['short'] = $short;
        
        if ( !self::validateOptionName( $long ) )
        {
            throw new ezcConsoleInputException(  
                "Invalid parameter name: <$long>.",
                ezcConsoleInputException::PARAMETER_NAME_INVALID 
            );
        }
        $this->properties['long'] = $long;
        
        $this->type      = $type         !== null ? $type      : ezcConsoleInput::TYPE_NONE ;
        $this->default   = $default      !== null ? $default   : null;
        $this->multiple  = $multiple     !== null ? $multiple  : false ;
        $this->shorthelp = $shorthelp    !== null ? $shorthelp : 'No help available.';
        $this->longhelp  = $longhelp     !== null ? $longhelp  : 'Sorry, there is no help text available for this parameter.';
        
        $dependencies    = $dependencies !== null && is_array( $dependencies ) ? $dependencies : array();
        foreach ( $dependencies as $dep )
        {
            $this->addDependency( $dep );
        }
        
        $exclusions = $exclusions !== null && is_array( $exclusions ) ? $exclusions : array();
        foreach ( $exclusions as $exc )
        {
            $this->addExclusion( $exc );
        }

        $this->mandatory = $mandatory !== null ? $mandatory : false;
    }

    /* Add a new dependency for a parameter.
     * This registeres a new dependency rule with the parameter. If you try
     * to add an already registered rule it will simply be ignored. Else,
     * the submitted rule will be added to the parameter as a dependency.
     *
     * @param ezcConsoleOptionRule $rule The rule to add.
     * @return void
     */
    public function addDependency( ezcConsoleOptionRule $rule )
    {
        foreach ( $this->dependencies as $existRule )
        {
            if ( $rule === $existRule )
            {
                return;
            }
        }
        $this->dependencies[] = $rule;
    }
    
    /**
     * Remove a dependency rule from a parameter.
     * This removes a given rule from a parameter, if it exists. If the rule is
     * not registered with the parameter, the method call will simply be ignored.
     * 
     * @param ezcConsoleOptionRule $rule The rule to be removed.
     * @return void
     */
    public function removeDependency( ezcConsoleOptionRule $rule )
    {
        foreach ( $this->dependencies as $id => $existRule )
        {
            if ( $rule === $existRule )
            {
                unset( $this->dependencies[$id] );
            }
        }
    }
    
    /**
     * Remove all dependency rule refering to a parameter.
     * This removes all dependency rules from a parameter, that refer to as specific 
     * parameter. If no rule is registered with this parameter as reference, the 
     * method call will simply be ignored.
     * 
     * @param ezcConsoleOption $param The param to be check for rules.
     * @return void
     */
    public function removeAllDependencies( ezcConsoleOption $param )
    {
        foreach ( $this->dependencies as $id => $rule )
        {
            if ( $rule->param === $param )
            {
                unset( $this->dependencies[$id] );
            }
        }
    }
    
    /**
     * Returns if a given dependency rule is registered with the parameter.
     * Returns true if the given rule is registered with this parameter,
     * otherwise false.
     * 
     * @param ezcConsoleOptionRule $rule The rule to be removed.
     * @return bool True if rule is registered, otherwise false.
     */
    public function hasDependency( ezcConsoleOption $param )
    {
        foreach ( $this->dependencies as $id => $rule )
        {
            if ( $rule->param === $param )
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns the dependency rules registered with this parameter.
     * Returns an array of registered dependencies.
     *
     * For example:
     * <code>
     * array(
     *      0 => ezcConsoleOptionRule,
     *      1 => ezcConsoleOptionRule,
     *      2 => ezcConsoleOptionRule,
     * );
     * </code>
     * 
     * @return array Dependency definition as described or an empty array.
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Reset existing dependency rules.
     * Deletes all registered dependency rules from the parameter definition.
     * 
     * @return void
     */
    public function resetDependencies() 
    {
        $this->dependencies = array();
    }

    /* Add a new exclusion for a parameter.
     * This registeres a new exclusion rule with the parameter. If you try
     * to add an already registered rule it will simply be ignored. Else,
     * the submitted rule will be added to the parameter as a exclusion.
     *
     * @param ezcConsoleOptionRule $rule The rule to add.
     * @return void
     */
    public function addExclusion( ezcConsoleOptionRule $rule )
    {
        foreach ( $this->exclusions as $existRule )
        {
            if ( $rule === $existRule )
            {
                return;
            }
        }
        $this->exclusions[] = $rule;
    }
    
    /**
     * Remove a exclusion rule from a parameter.
     * This removes a given rule from a parameter, if it exists. If the rule is
     * not registered with the parameter, the method call will simply be ignored.
     * 
     * @param ezcConsoleOptionRule $rule The rule to be removed.
     * @return void
     */
    public function removeExclusion( ezcConsoleOptionRule $rule )
    {
        foreach ( $this->exclusions as $id => $existRule )
        {
            if ( $rule === $existRule )
            {
                unset( $this->exclusions[$id] );
            }
        }
    }
    
    /**
     * Remove all exclusion rule refering to a parameter.
     * This removes all exclusion rules from a parameter, that refer to as specific 
     * parameter. If no rule is registered with this parameter as reference, the 
     * method call will simply be ignored.
     * 
     * @param ezcConsoleOption $param The param to be check for rules.
     * @return void
     */
    public function removeAllExclusions( ezcConsoleOption $param )
    {
        foreach ( $this->exclusions as $id => $rule )
        {
            if ( $rule->param === $param )
            {
                unset( $this->exclusions[$id] );
            }
        }
    }
    
    /**
     * Returns if a given exclusion rule is registered with the parameter.
     * Returns true if the given rule is registered with this parameter,
     * otherwise false.
     * 
     * @param ezcConsoleOptionRule $rule The rule to be removed.
     * @return bool True if rule is registered, otherwise false.
     */
    public function hasExclusion( ezcConsoleOption $param )
    {
        foreach ( $this->exclusions as $id => $rule )
        {
            if ( $rule->param === $param )
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns the exclusion rules registered with this parameter.
     * Returns an array of registered exclusions.
     *
     * For example:
     * <code>
     * array(
     *      0 => ezcConsoleOptionRule,
     *      1 => ezcConsoleOptionRule,
     *      2 => ezcConsoleOptionRule,
     * );
     * </code>
     * 
     * @return array Exclusion definition as described or an empty array.
     */
    public function getExclusions()
    {
        return $this->exclusions;
    }

    /**
     * Reset existing exclusion rules.
     * Deletes all registered exclusion rules from the parameter definition.
     *
     * @return void
     */
    public function resetExclusions() 
    {
        $this->exclusions = array();
    }
    
    /**
     * Property read access.
     * Provies read access to the properties of the object.
     * 
     * @param string $key The name of the property.
     * @return mixed The value if property exists and isset, otherwise null.
     */
    public function __get( $key )
    {
        if ( isset( $this->properties[$key] ) )
        {
            return $this->properties[$key];
        }
    }

    /**
     * Property write access.
     * 
     * @param string $key Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBasePropertyPermissionException
     *         If the property you try to access is read-only.
     * @return void
     */
    public function __set( $key, $val )
    {
        throw new ezcBasePropertyPermissionException( $key, ezcBasePropertyPermissionException::READ );
    }
 
    /**
     * Property isset access.
     * 
     * @param string $key Name of the property.
     * @return bool True is the property is set, otherwise false.
     */
    public function __isset( $key )
    {
        return isset( $this->properties[$key] );
    }

    /**
     * Returns if a given name if valid for use as a parameter name a paremeter. 
     * Checks if a given parameter name is generaly valid for use. It checks a)
     * that the name does not start with '-' or '--' and b) if it contains
     * whitespaces. Note, that this method does not check any conflicts with already
     * used parameter names.
     * 
     * @param string $name The name to check.
     * @return bool True if the name is valid, otherwise false.
     */
    public static function validateOptionName( $name )
    {
        if ( substr( $name, 0, 1 ) === '-' || strpos( $name, ' ' ) !== false )
        {
            return false;
        }
        return true;
    }
}

?>
