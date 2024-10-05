<?php

class CoreException extends Exception
{
    public function __construct($message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if( !empty($message) )
            $this->displayMessage();
    }

    /**
     * This method acts like an error handler, if dev mode is on, display the error else use a better silent way.
     */
    public function displayMessage()
    {
        header('HTTP/1.1 500 Internal Server Error');
        if( _DEBUG_ )
        {
            // Display error message
            echo '<style>
                #coreException{font-family: Verdana; font-size: 14px}
                #coreException h2{color: #F20000}
                #coreException p{padding-left: 20px}
                #coreException ul li{margin-bottom: 10px}
                #coreException a{font-size: 12px; color: #000000}
                #coreException .coreTrace, #coreException .coreArgs{display: none}
                #coreException pre{border: 1px solid #236B04; background-color: #EAFEE1; padding: 5px; font-family: Courier; width: 99%; overflow-x: auto; margin-bottom: 30px;}
                #coreException .coreArgs pre{background-color: #F1FDFE;}
                #coreException pre .selected{color: #F20000; font-weight: bold;}
            </style>';
            echo '<div id="coreException">';
            echo '<h2>[' . get_class($this) . ']</h2>';
            echo $this->getExtendedMessage();

            $this->displayFileDebug($this->getFile(), $this->getLine());

            // Display debug backtrace
            echo '<ul>';
            foreach( $this->getTrace() as $id => $trace )
            {
                $relative_file = (isset($trace['file'])) ? ltrim(str_replace([_PATH_, '\\'], ['', '/'], $trace['file']), '/') : '';
                $current_line = (isset($trace['line'])) ? $trace['line'] : '';
                echo '<li>';
                echo '<b>' . ((isset($trace['class'])) ? $trace['class'] : '') . ((isset($trace['type'])) ? $trace['type'] : '') . $trace['function'] . '</b>';
                echo ' - <a style="font-size: 12px; color: #000000; cursor:pointer; color: blue;" onclick="document.getElementById(\'coreTrace_' . $id . '\').style.display = (document.getElementById(\'coreTrace_' . $id . '\').style.display != \'block\') ? \'block\' : \'none\'; return false">[line ' . $current_line . ' - ' . $relative_file . ']</a>';

                if( isset($trace['args']) && count($trace['args']) )
                    echo ' - <a style="font-size: 12px; color: #000000; cursor:pointer; color: blue;" onclick="document.getElementById(\'coreArgs_' . $id . '\').style.display = (document.getElementById(\'coreArgs_' . $id . '\').style.display != \'block\') ? \'block\' : \'none\'; return false">[' . count($trace['args']) . ' Arguments]</a>';

                if( $relative_file )
                    $this->displayFileDebug($trace['file'], $trace['line'], $id);

                if( isset($trace['args']) && count($trace['args']) )
                {
                    $args = $this->hideCriticalArgs($trace);
                    $this->displayArgsDebug($args, $id);
                }
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        // Log the error in the disk
        el($this->getExtendedMessage(false), false, false);

        if( _DEBUG_ )
            exit;
    }

    /**
     * Display lines around current line.
     *
     * @param string $file
     * @param int $line
     * @param string $id
     */
    protected function displayFileDebug($file, $line, $id = null)
    {
        $lines = file($file);
        $offset = $line - 6;
        $total = 11;
        if( $offset < 0 )
        {
            $total += $offset;
            $offset = 0;
        }
        $lines = array_slice($lines, $offset, $total);
        ++$offset;

        echo '<div class="coreTrace" id="coreTrace_' . $id . '" ' . ((null === $id ? 'style="display: block"' : '')) . '><pre>';
        foreach( $lines as $k => $l )
        {
            $string = ($offset + $k) . '. ' . htmlspecialchars($l);
            if( $offset + $k == $line )
                echo '<span class="selected">' . $string . '</span>';
            else
                echo $string;
        }
        echo '</pre></div>';
    }

    /**
     * Prevent critical arguments to be displayed in the debug trace page (e.g. database password)
     * Returns the array of args with critical arguments replaced by placeholders.
     *
     * @param array $trace
     *
     * @return array
     */
    protected function hideCriticalArgs(array $trace)
    {
        $args = $trace['args'];
        if( empty($trace['class']) || empty($trace['function']) )
            return $args;

        $criticalParameters = [
            'pwd',
            'pass',
            'passwd',
            'password',
            'database',
            'server',
        ];
        $hiddenArgs = [];

        try
        {
            $class = new \ReflectionClass($trace['class']);
            /** @var \ReflectionMethod $method */
            $method = $class->getMethod($trace['function']);
            /** @var \ReflectionParameter $parameter */
            foreach( $method->getParameters() as $argIndex => $parameter )
            {
                if( $argIndex >= count($args) )
                    break;

                if( in_array(strtolower($parameter->getName()), $criticalParameters) )
                    $hiddenArgs[] = '**hidden_' . $parameter->getName() . '**';
                else
                    $hiddenArgs[] = $args[$argIndex];
            }
        }
        catch( ReflectionException $e )
        {
            //In worst case scenario there are some critical args we could't detect so we return an empty array
        }

        return $hiddenArgs;
    }

    /**
     * Display arguments list of traced function.
     *
     * @param array $args List of arguments
     * @param string $id ID of argument
     */
    protected function displayArgsDebug($args, $id)
    {
        echo '<div class="coreArgs" id="coreArgs_' . $id . '"><pre>';
        foreach( $args as $arg => $value )
        {
            echo '<b>Argument [' . Tools::safeOutput($arg) . "]</b>\n";
            echo Tools::safeOutput(print_r($value, true));
            echo "\n";
        }
        echo '</pre>';
    }

    /**
     * Return the content of the Exception.
     *
     * @return string content of the exception
     */
    protected function getExtendedMessage($html = true)
    {
        $format = '<p><b>%s</b><br /><i>at line </i><b>%d</b><i> in file </i><b>%s</b></p>';
        if (!$html) {
            $format = strip_tags(str_replace('<br />', ' ', $format));
        }

        return sprintf(
            $format,
            Tools::safeOutput($this->getMessage(), true),
            $this->getLine(),
            ltrim(str_replace([_PATH_, '\\'], ['', '/'], $this->getFile()), '/')
        );
    }
}
