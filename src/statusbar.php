<?php
/**
 * File containing the ezcConsoleStatusbar class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Creating  and maintaining statusbars to be printed to the console. 
 *
 * <code>
 *
 * // ... creating ezcConsoleOutput object
 * 
 * $opt = array(
 *     'successChar'   => '+',
 *     'failureChar'   => '-',
 * );
 * $status = new ezcConsoleStatusbar( $opt );
 * foreach ( $files as $file )
 * {
 *      $res = $file->upload();
 *      $status->add( $res ); // $res is true or false
 * }
 *
 * $msg = $status->getSuccess() . ' succeeded, ' . $status->getFailure() . ' failed.';
 * $out->outputText( "Finished uploading files: $msg\n" );
 *
 * </code>
 *  
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleStatusbar
{
    /**
     * Options
     *
     * <code>
     * array(
     *   'successChar' => '+',     // Char to indicate success
     *   'failureChar' => '-',     // Char to indicate failure
     * );
     * </code>
     *
     * @var array(string)
     */
    protected $options = array(
        'successChar' => '+',     // Char to indicate success
        'failureChar' => '-',     // Char to indicate failure
    );

    /**
     * The ezcConsoleOutput object to use.
     *
     * @var ezcConsoleOutput
     */
    protected $outputHandler;

    /**
     * Counter for success and failure outputs. 
     * 
     * @var array
     */
    protected $counter = array( 
        true  => 0,
        false => 0,
    );

    /**
     * Creates a new status bar.
     *
     * @param ezcConsoleOutput $outHandler Handler to utilize for output
     * @param array(string) $settings      Settings
     * @param array(string) $options       Options
     *
     * @see ezcConsoleStatusbar::$options
     */
    public function __construct( ezcConsoleOutput $outHandler, $successChar = '+', $failureChar = '-' )
    {
        $this->outputHandler = $outHandler;
        $this->__set( 'successChar', $successChar );
        $this->__set( 'failureChar', $failureChar );
    }

    /**
     * Property read access.
     * 
     * @param string $key Name of the property.
     * @return mixed Value of the property or null.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     */
    public function __get( $key )
    {
        if ( isset( $this->options[$key] ) )
        {
            return $this->options[$key];
        }
        throw new ezcBasePropertyNotFoundException( $key );
    }

    /**
     * Property write access.
     * 
     * @param string $key Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a desired property could not be found.
     * @throws ezcBaseConfigException
     *         If a desired property value is out of range
     *         {@link ezcBaseConfigException::VALUE_OUT_OF_RANGE}.
     */
    public function __set( $key, $val )
    {
        switch ( $key )
        {
            case 'successChar':
            case 'failureChar':
                if ( strlen( $val ) < 1 )
                {
                    throw new ezcBaseConfigException( $key, ezcBaseConfigException::VALUE_OUT_OF_RANGE, $val );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $key );
        }
        $this->options[$key] = $val;
    }

    /**
     * Set options for the statusbar.
     *
     * @see ezcConsoleStatusbar::$options
     * 
     * @param array $options Options to set.
     */
    public function setOptions( $options )
    {
        foreach ( $options as $name => $val ) 
        {
            if ( isset( $this->options[$name] ) ) 
            {
                $this->options[$name] = $val;
            } 
            else 
            {
                trigger_error( "Unknown option <{$name}>.", E_USER_WARNING );
            }
        }
    }

    /**
     * Add a status to the status bar.
     * Adds a new status to the bar which is printed immediatelly. If the
     * cursor is currently not at the beginning of a line, it will move to
     * the next line.
     *
     * @param bool $status Print successChar on true, failureChar on false.
     */
    public function add( $status )
    {
        switch ( $status )
        {
            case true:
                $this->outputHandler->outputText( $this->successChar, 'success' );
                break;

            case false:
                $this->outputHandler->outputText( $this->failureChar, 'failure' );
                break;
            
            default:
                trigger_error( 'Unknown status '.var_export( $status, true ).'.', E_USER_WARNING );
                return;
                break;
        }
        $this->counter[$status]++;
    }

    /**
     * Reset the state of the statusbar object to its initial one. 
     * 
     */
    public function reset()
    {
        foreach ( $this->counter as $status => $count )
        {
            $this->counter[$status] = 0;
        }
    }

    /**
     * Returns number of successes during the run.
     * Returns the number of success characters printed from this status bar.
     * 
     * @returns int Number of successes.
     */
    public function getSuccesses()
    {
        return $this->counter[true];
    }

    /**
     * Returns number of failures during the run.
     * Returns the number of failure characters printed from this status bar.
     * 
     * @returns int Number of failures.
     */
    public function getFailures()
    {
        return $this->counter[false];
    }
}
?>
