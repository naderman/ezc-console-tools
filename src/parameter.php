<?php
/**
 * File containing the ezcConsoleParameter class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class for handling console parameters.
 * This class allows the complete handling of parameters submitted
 * to a console based application.
 *
 * <code>
 *
 * $paramHandler = new ezcConsoleParameter();
 * 
 * $help = array(
 *  'short' => 'Get help output.',
 *  'long'  => 'Retreive help on the usage of this command.',
 * );
 * $paramHandler->registerParam( 'h', 'help', $help );
 *
 * $file = array(
 *  'type'     => ezcConsoleParameter::TYPE_STRING
 *  'short'    => 'Process a file.',
 *  'long'     => 'Processes a single file.',
 *  'excludes' => array('d'),
 * )
 * $paramHandler->registerParam( 'f', 'file', $file );
 *
 * $dir = array(
 *  'type'     => ezcConsoleParameter::TYPE_STRING
 *  'short'    => 'Process a directory.',
 *  'long'     => 'Processes a complete directory.',
 *  'excludes' => array('f'),
 * )
 * $paramHandler->registerParam( 'd', 'dir', $dir );
 *
 * $paramHandler->registerAlias( 'd', 'directory', 'd' );
 *
 * try
 * {
 *      $paramHandler->processParams();
 * }
 * catch ( ezcConsoleParameterException $e )
 * {
 *      if ( $e->code === ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET )
 *      {
 *          $consoleOut->outputText(
 *              'Parameter '.$e->paramName." may not occur here.\n", 'error'
 *          );
 *      }
 *      exit( 1 );
 * }
 *
 * </code>
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleParameter
{
    /**
     * Parameter does not cary a value.
     */
    const TYPE_NONE     = 1;

    /**
     * Parameter takes an integer value.
     */
    const TYPE_INT      = 2;

    /**
     * Parameter takes a string value. 
     */
    const TYPE_STRING   = 3;

    /**
     * Array of parameter definitions, indexed by number.
     * This array contains the paremeter definitions (short name, long name and
     * options) assigned to a number index. This index gets referenced by the
     * {@link ezcConsoleParameter::$paramShort} and 
     * {@link ezcConsoleParameter::$paramLong} arrays, which are indexed by the specific
     * parameter name.
     * 
     * @var array(int => array)
     */
    private $paramDefs = array();

    /**
     * Short paraemeter names. Each references a key in 
     * {@link ezcConsoleParameter::$paramDefs}.
     * 
     * @var array(string => int)
     */
    private $paramShort = array();

    /**
     * Long paraemeter names. Each references a key in 
     * {@link ezcConsoleParameter::$paramDefs}.
     * 
     * @var array(string => int)
     */
    private $paramLong = array();

    /**
     * Values submitted for a parameter, indexed by the key used for
     * {ezcConsoleParameter::$paramDefs}.
     * 
     * @var array(int => mixed)
     */
    private $paramValues = array();

    /**
     * Arguments, if submitted, are stored here. 
     * 
     * @var array
     */
    private $arguments = array();

    /**
     * Default values for parameter options. 
     * 
     * @var array(string => mixed)
     */
    private $defaults = array( 
        'type'      => ezcConsoleParameter::TYPE_NONE,
        'default'   => null,
        'multiple'  => false,
        'shorthelp' => 'No help available.',
        'longhelp'  => 'Sorry, there is no help text available for this parameter.',
        'depends'   => array(),
        'excludes'  => array(),
        'arguments' => true,
    );

    /**
     * Are arguments allowed beside parameters? 
     * This attributes changes to false, if a parameter excludes
     * the usage of arguments.
     * 
     * @var bool True if arguments are allowed, otherwise false.
     */
    private $argumentsAllowed = true;

    /**
     * Create parameter handler
     */
    public function __construct()
    {
    }

    /**
     * Register a new parameter.
     * Register a new parameter to be recognized by the parser. The short 
     * option is a single character, the long option can be any string 
     * containing [a-z-]+. Via the options array several options can be 
     * defined for a parameter:
     *
     * <code>
     * array(
     *  'type'      => TYPE_NONE,  // option does not expect a value by 
     *                             // default, use TYPE_* constants
     *  'default'   => null,       // no default value by default
     *  'multiple'  => false,      // are multiple values expected?
     *  'shorthelp' => '',         // no short description by default
     *  'longhelp'  => '',         // no help text by default
     *  'depends'   => array(),    // no depending options by default
     *  'excludes'  => array(),    // no excluded options by default
     *  'arguments' => true,       // are arguments allowed?
     * );
     * </code>
     *
     * Attention: Already existing parameter will be overwriten! If an 
     * already existing alias is attempted to be registered, the alias 
     * will be deleted and replaced by the new parameter.
     *
     * Parameter shortcuts may only contain one character and will be 
     * used in an application call using "-x <value>". Long parameter
     * versions will be used like "--long-parameter=<value>".
     *
     * A parameter can have no value (TYPE_NONE), an integer/string
     * value (TYPE_INT/TYPE_STRING) or multiple of those 
     * ('muliple' => true).
     *
     * A parameter can also include a rule that disallows arguments, when
     * it's used. Per default arguments are allowed and can be retrieved
     * using the {ezcConsoleParameter::getArguments()} method.
     *
     * @see ezcConsoleParameter::unregisterParam()
     *
     * @param string $short          Short parameter
     * @param string $long           Long version of parameter
     * @param array(string) $options See description
     *
     */
    public function registerParam( $short, $long, $options = array() )
    {
        end( $this->paramDefs );
        $nextKey = key( $this->paramDefs ) + 1;
        $this->paramDefs[$nextKey] = array( 
            'long'    => $long,
            'short'   => $short,
            'options' => array_merge( $this->defaults, $options ),
        );
        $this->paramShort[$short] = $nextKey;
        $this->paramLong[$long] = $nextKey;
    }

    /**
     * Register an alias to a parameter.
     * Registers a new alias for an existing parameter. Aliases may
     * then be used as if they were real parameters.
     *
     * @see ezcConsoleParameter::unregisterAlias()
     *
     * @param string $short    Shortcut of the alias
     * @param string $long     Long version of the alias
     * @param strung $refShort Reference to an existing param (short)
     *
     *
     * @throws ezcConsoleParameterException
     * @see ezcConsoleParameterException::PARAMETER_NOT_EXISTS
     */
    public function registerAlias( $short, $long, $refShort )
    {
        if ( !isset( $this->paramShort[$refShort] ) )
        {
            throw new ezcConsoleParameterException( 
                "Unknown parameter reference <{$refShort}>.",
                ezcConsoleParameterException::PARAMETER_NOT_EXISTS, 
                $refShort 
            );
        }
        $this->paramShort[$short] = $this->paramShort[$refShort];
        $this->paramLong[$long] = $this->paramShort[$refShort];
    }

    /**
     * Registeres parameters according to a string specification.
     * Accepts a string like used in eZ publis 3.x to define parameters and
     * registeres all parameters accordingly. String definitions look like
     * this:
     *
     * <code>
     * [s:|size:][u:|user:][a:|all:]
     * </code>
     *
     * This string will result in 3 parameters:
     * -s / --size
     * -u / --user
     * -a / --all
     *
     * @param string $paramDef Parameter definition string.
     * @throws ezcConsoleParameterException If string is not wellformed.
     */
    public function fromString( $paramDef ) 
    {
        $regex = '/\[([a-z0-9-]+)([:?*+])?([^|]*)\|([a-z0-9-]+)([:?*+])?\]/';
        if ( preg_match_all( $regex, $paramDef, $matches ) )
        {
            foreach ( $matches[1] as $id => $short )
            {
                $paramOptions = array();
                if ( empty( $matches[4][$id] )  ) 
                {
                    throw new ezcConsoleParameterException( 
                        "Missing long parameter name for short parameter <-{$short}>",
                        ezcConsoleParameterException::PARAMETER_STRING_NOT_WELLFORMED 
                    );
                }
                $long = $matches[4][$id];
                if ( !empty( $matches[2][$id] ) || !empty( $matches[5][$id] ) )
                {
                    switch ( !empty( $matches[2][$id] ) ? $matches[2][$id] : $matches[5][$id] )
                    {
                        case '*':
                            // Allows 0 or more occurances
                            $paramOptions['multiple'] = true;
                            break;
                        case '+':
                            // Allows 1 or more occurances
                            $paramOptions['multiple'] = true;
                            $paramOptions['type'] = self::TYPE_STRING;
                            break;
                        case '?':
                            $paramOptions['type'] = self::TYPE_STRING;
                            $paramOptions['default'] = '';
                            break;
                        default:
                            break;
                    }
                }
                if ( !empty( $matches[3][$id] ) )
                {
                    $paramOptions['default'] = $matches[3][$id];
                }
                $this->registerParam( $short, $long, $paramOptions );
            }
        }

    }

    /**
     * Remove a parameter to be no more supported.
     * Using this function you will remove a parameter. Depending on the second 
     * option dependencies to this parameter are handled. Per default, just 
     * all dependencies to that actual parameter are removed (false value). 
     * Setting it to true will completely unregister all parameters that depend 
     * on the current one.
     *
     * @see ezcConsoleParameter::registerParam()
     *
     * @param string $short Short option name for the parameter to be removed.
     * @param bool $deps    Handling of dependencies while unregistering. 
     *
     *
     * @throws ezcConsoleParameterException 
     *         If requesting a nonexistant parameter 
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     */
    public function unregisterParam( $short, $deps = false )
    {
        if ( !isset( $this->paramShort[$short] ) )
        {
            throw new ezcConsoleParameterException( 
                "Unknown parameter reference <{$short}>.", 
                ezcConsoleParameterException::PARAMETER_NOT_EXISTS, 
                $short 
            );
        }
        $defKey = $this->paramShort[$short];
        // Unset long reference
        unset( $this->paramLong[$this->paramDefs[$defKey]['long']] );
        // Unset short reference
        unset( $this->paramShort[$short] );
        // Unset parameter definition itself
        unset( $this->paramDefs[$defKey] );

        // Check for depending parameters and remove them
        if ( $deps === true )
        {
            foreach ( $this->paramDefs as $paramRef => $paramDef )
            {
                foreach ( $paramDef['options']['depends'] as $shortDep ) 
                {
                    if ( $shortDep === $short )
                    {
                        $this->unregisterParam( $short, true );
                    }
                }
            }
        }
    }

    /**
     * Returns the options defined for a specific parameter.
     * This method receives the long or short name of a parameter and
     * returns the options associated with it.
     * 
     * @param string $param Short or long name of the parameter.
     * @return array(string) Options set for the parameter.
     */
    public function getParamDef( $paramName )
    {
        return $this->paramDefs[$this->getParamRef($paramName)];
        // Never reached, but shows what can happen with the above call
        throw new ezcConsoleParameterException( 
            "Unknown parameter reference <{$paramName}>.", 
            ezcConsoleParameterException::PARAMETER_NOT_EXISTS,
            $paramName
        );
    }

    /**
     * Process the input parameters.
     * Actually process the input parameters according to the actual settings.
     * 
     * Per default this method uses $argc and $argv for processing. You can 
     * override this setting with your own input, if necessary, using the
     * parameters of this method. (Attention, first argument is always the pro
     * gram name itself!)
     *
     * All exceptions thrown by this method contain an additional attribute "param"
     * which specifies the parameter on which the error occured.
     * 
     * @param array(int -> string) $args The arguments
     *
     * @throws ezcConsoleParameterException 
     *         If dependencies are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If exclusion rules are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If type rules are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If a parameter used does not exist
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     * @throws ezcConsoleParameterException 
     *         If arguments are passed although a parameter dissallowed them
     *         {@link ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED}.
     * 
     * @see ezcConsoleParameterException
     */ 
    public function process( $args = null )
    {
        if ( !isset( $args ) )
        {
            $args = isset( $argv ) ? $argv : isset( $_SERVER['argv'] ) ? $_SERVER['argv'] : array();
        }
        $i = 1;
        while ( $i < count( $args ) )
        {
            // Equalize parameter handling (long params with =)
            if ( substr( $args[$i], 0, 2 ) == '--' )
            {
                $this->preprocessLongParam( $args, $i );
            }
            // Check for parameter
            if ( $this->getParamRef( $args[$i] ) !== false ) 
            {
                $this->processParameter( $args, $i );
            }
            // Must be the arguments
            else
            {
                $this->processArguments( $args, $i );
                break;
            }
        }
        $this->checkRules();
    }

    /**
     * Receive the data for a specific parameter.
     * Returns the data sumbitted for a specific parameter.
     *
     * @param string $param The parameter name (short or long)
     *
     * @return mixed String value of the parameter, true if set without 
     *               value or false on not set.
     */
    public function getParam( $param )
    {
        if ( ( $paramRef = $this->getParamRef( $param ) ) !== false )
        {
            return isset( $this->paramValues[$paramRef] ) ? $this->paramValues[$paramRef] : false;
        }
        return false;
    }

    /**
     * Returns the data for all submitted parameters.
     * This method gives you all submitted parameters with their values. The 
     * returned array is indexed by the parameter shortcut, which is assigned
     * to the value.
     *
     * @return array(string => mixed) Array of parameter shortcut => value 
     *                                association.
     */
    public function getParams()
    {
        $res = array();
        foreach ( $this->paramValues as $paramRef => $val )
        {
            $res[$this->paramDefs[$paramRef]['short']] = $val;
        }
        return $res;
    }

    /**
     * Returns arguments provided to the program.
     * This method returns all arguments provided to a program in an
     * integer indexed array. Arguments are sorted in the way
     * they are submitted to the program. You can disable arguments
     * through the 'arguments' flag of a parameter, if you want
     * to disallow arguments.
     *
     * Arguments are either the last part of the program call (if the
     * last parameter is not a 'multiple' one) or divided via the '--'
     * method which is commonly used on Unix (if the last parameter
     * accepts multiple values this is required).
     *
     * @return array(int => string) Arguments.
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Return the default values for parameter options.
     * 
     * @return array(string => mixed)
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Get help information for your parameters.
     * This method returns an array of help information for your parameters,
     * indexed by integer. Each helo info has 2 fields:
     *
     * 0 => The parameters names ("<short> / <long>")
     * 1 => The help text (depending on the $long parameter)
     *
     * The $long parameter determines if you want to get the short- or longhelp
     * texts. The array returned can be used by {@link ezcConsoleTable}.
     *
     * If using the second parameter, you can filter the parameters shown in the
     * help output (e.g. to show short help for related parameters). Provide
     * as simple number indexed array of short and/or long values to set a filter.
     * 
     * @param bool $long Set this to true for getting the long help version.
     * @param array $params Set of parameters to generate help for, default is all.
     */
    public function getHelp( $long = false, $params = array() )
    {
        $help = array();
        foreach ( $this->paramDefs as $paramRef => $def )
        {
            if ( count($params) === 0 || in_array( $def['short'], $params ) || in_array( $def['long'], $params ) )
            {
                $help[] = array( 
                    '-' . $def['short'] . ' / ' . '--' . $def['long'],
                    $long == false ? $def['options']['shorthelp'] : $def['options']['longhelp'],
                );
            }
        }
        return $help;
    }

    /**
     * Process a parameter.
     * This method does the processing of a single parameter. 
     * 
     * @param int $paramRef The parameter reference.
     * @param array $args The arguments array.
     * @param int The current index in the $args array.
     * @returns void 
     */
    private function processParameter( $args, &$i )
    {
        $paramRef = $this->getParamRef( $args[$i++] );
        // No value expected
        if ( $this->paramDefs[$paramRef]['options']['type'] === ezcConsoleParameter::TYPE_NONE )
        {
            // No value expected
            if ( isset( $args[$i] ) && $this->getParamRef( $args[$i] ) === false )
            {
                // But one found
                throw new Exception( 
                    "Parameter <--{$this->paramDefs[$paramRef]['long']}> does not expect a value but <{$args[$i]}> was submitted.",
                    ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET
                );
            }
            $this->paramValues[$paramRef] = true;
            // Everything fine, nothing to do
            return $i;
        }
        // Value expected, check for it
        if ( isset( $args[$i] ) && substr( $args[$i], 0, 1 ) !== '-' )
        {
            // Type check
            if ( $this->correctType( $paramRef, $args[$i] ) === false )
            {
                throw new ezcConsoleParameterException( 
                    "Parameter <--{$this->paramDefs[$paramRef]['long']}> of incorrect type.",
                    ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET,
                    $this->paramDefs[$paramRef]['long']
                );
            }
            // Multiple values possible
            if ( $this->paramDefs[$paramRef]['options']['multiple'] === true )
            {
                $this->paramValues[$paramRef][] = $args[$i];
            }
            // Only single value expected, check for multiple
            elseif ( isset( $this->paramValues[$paramRef] ) )
            {
                throw new ezcConsoleParameterException( 
                    "Parameter <--{$this->paramDefs[$paramRef]['long']}> expects only 1 value but multiple have been submitted.",
                    ezcConsoleParameterException::TOO_MANY_PARAMETER_VALUES,
                    $this->paramDefs[$paramRef]['long']
                );
            }
            else
            {
                $this->paramValues[$paramRef] = $args[$i];
            }
            $i++;
        }
        // Value found? If not, use default, if available
        if ( !isset( $this->paramValues[$paramRef] ) || ( is_array( $this->paramValues[$paramRef] ) && count( $this->paramValues[$paramRef] ) == 0 ) ) 
        {
            if ( isset( $this->paramDefs[$paramRef]['options']['default'] ) ) 
            {
                $this->paramValues[$paramRef] = $this->paramDefs[$paramRef]['options']['default'];
            }
            else
            {
                throw new ezcConsoleParameterException( 
                    "Parameter value missing for parameter <--{$this->paramDefs[$paramRef]['long']}>.",
                    ezcConsoleParameterException::MISSING_PARAMETER_VALUE,
                    $this->paramDefs[$paramRef]['short']
                );
            }
        }
        return $i;
    }

    /**
     * Process arguments given to the program. 
     * 
     * @todo FIXME: Add test for this!
     * @param array $args The arguments array.
     * @param int $i Current index in arguments array.
     */
    private function processArguments( $args, &$i )
    {
        while ( $i < count( $args ) )
        {
            if ( substr( $args[$i], 0, 1 ) == '-' )
            {
                throw new ezcConsoleParameterException( 
                    "Unexpected parameter in argument list: <{$args[$i]}>.",
                    ezcConsoleParameterException::UNKNOWN_PARAMETER,
                    $args[$i]
                );

            }
            $this->arguments[] = $args[$i++];
        }
    }

    /**
     * Check the rules that may be associated with a parameter.
     * Parameters are allowed to have rules associated for
     * dependencies to other parameters and exclusion of other parameters or
     * arguments. This method processes the checks.
     * 
     *
     * @throws ezcConsoleParameterException 
     *         If dependencies are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If exclusion rules are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If arguments are passed although a parameter dissallowed them
     *         {@link ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED}.
     */
    private function checkRules()
    {
        foreach ( array_keys( $this->paramValues ) as $paramRef )
        {
            // Dependencies
            if ( is_array( $this->paramDefs[$paramRef]['options']['depends'] )
                 && count( $this->paramDefs[$paramRef]['options']['depends'] ) > 0 )
            {
                foreach (  $this->paramDefs[$paramRef]['options']['depends'] as $dependName )
                {
                    $dependRef = $this->paramShort[$dependName];
                    if ( !isset( $this->paramValues[$dependRef] ) )
                    {
                        throw new ezcConsoleParameterException( 
                            "Parameter <--{$this->paramDefs[$paramRef]['long']}> depends on <--{$this->paramDefs[$dependRef]['long']}> which was not submitted.",
                            ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET,
                            $this->paramDefs[$paramRef]['long']
                        );
                    }
                }
            }
            // Exclusions
            if ( is_array( $this->paramDefs[$paramRef]['options']['excludes'] )
                 && count( $this->paramDefs[$paramRef]['options']['excludes'] ) > 0 )
            {
                foreach (  $this->paramDefs[$paramRef]['options']['excludes'] as $excludeName )
                {
                    $excludeRef = $this->paramShort[$excludeName];
                    if ( isset( $this->paramValues[$excludeRef] ) )
                    {
                        throw new ezcConsoleParameterException( 
                            "Parameter <--{$this->paramDefs[$paramRef]['long']}> excludes <--{$this->paramDefs[$excludeRef]['long']}> which was submitted.",
                            ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET,
                            $this->paramDefs[$paramRef]['long']
                        );
                    }
                }
            }
            // Arguments
            if ( $this->paramDefs[$paramRef]['options']['arguments'] === false 
                 && is_array( $this->arguments ) 
                 && count( $this->arguments ) > 0 )
            {
                throw new ezcConsoleParameterException( 
                    "Parameter <--{$this->paramDefs[$paramRef]['long']}> excludes the usage of arguments, but arguments have been passed.",
                    ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED,
                    $this->paramDefs[$paramRef]['long']
                );
            }
        }
    }

    /**
     * Checks if a value is of a given type. Converts the value to the
     * correct PHP type on success.
     *  
     * @param int $paramRef Reference to the parameter.
     * @param string $val The value tu check.
     * @return bool True on succesful check, otherwise false.
     */
    private function correctType( $paramRef, &$val )
    {
        $res = false;
        switch ( $this->paramDefs[$paramRef]['options']['type'] )
        {
            case ezcConsoleParameter::TYPE_STRING:
                $res = true;
                $val = preg_replace( '/^(["\'])(.*)\1$/', '\2', $val );
                break;
            case ezcConsoleParameter::TYPE_INT:
                $res = preg_match( '/^[0-9]+$/', $val ) ? true : false;
                if ( $res )
                {
                    $val = (int)$val;
                }
                break;
        }
        return $res;
    }

    /**
     * Returns the parameter reference to a given parameter name.
     * This method determines the reference to a parameter out of his short or
     * long name. The name can start with the typical signature for a 
     * parameter name ('-' for short, '--' for long). If the name is not a valid
     * parameter name (no '-' / '--'), the method tries bothe alternatives (beware
     * of conflicts!) and return false if it still finds no alternative. If the name is
     * syntactically valid, but the parameter does not exist, it will throw an 
     * exception. On success the parameter reference is returned.
     * 
     * @param string $str The string to check.
     * @return mixed Int reference on success, false on wrong syntax.
     *
     *
     * @throws ezcConsoleParameterException 
     *         If a parameter used does not exist
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     */
    private function getParamRef( $arg )
    {
        $paramRef = false;
        // Long parameter name
        if ( substr( $arg, 0, 2 ) == '--' && substr( $arg, 2, 1 ) != ' ' ) 
        {
            $paramName = substr( $arg, 2 );
            if ( isset( $this->paramLong[$paramName] ) )
            {
                $paramRef = $this->paramLong[$paramName];
            }
            else
            {
                throw new ezcConsoleParameterException( 
                    "Unknown parameter <{$paramName}>",
                    ezcConsoleParameterException::PARAMETER_NOT_EXISTS
                );

            }
        }
        // Short parameter name
        elseif ( substr( $arg, 0, 1 ) == '-' && substr( $arg, 1, 1 ) != '-' ) 
        {
            $paramName = substr( $arg, 1 );
            if ( isset( $this->paramShort[$paramName] ) )
            {
                $paramRef = $this->paramShort[$paramName];
            }
            else
            {
                throw new ezcConsoleParameterException( 
                    "Unknown parameter <{$paramName}>.",
                    ezcConsoleParameterException::PARAMETER_NOT_EXISTS
                );

            }
        }
        // No prefix given, check both
        else
        {
            $paramName = $arg;
            if ( isset( $this->paramShort[$paramName] ) )
            {
                $paramRef = $this->paramShort[$paramName];
            }
            elseif ( isset( $this->paramLong[$paramName] ) )
            {
                $paramRef = $this->paramLong[$paramName];
            }
        }
            
        return $paramRef;
    }

    /**
     * Split parameter and value for long parameter names. This method checks 
     * for long parameters, if the value is passed using =. If this is the case
     * parameter and value get split and replaced in the arguments array.
     * 
     * @param array $args The arguments array
     * @param int $i Current arguments array position
     */
    private function preprocessLongParam( &$args, $i )
    {
        // Value given?
        if ( preg_match( '/^--\w+\=[^ ]/i', $args[$i] ) )
        {
            // Split param and value and replace current param
            $parts = explode( '=', $args[$i], 2 );
            array_splice( $args, $i, 1, $parts );
        }
    }
}
?>
