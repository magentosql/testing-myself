<?php
function debug ($var, $die = true, $echo = true, $backtrace_show = false)
{
    $text = '<pre class="debug-error">';
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    if ($echo)
    {
        $text.= 'file: '.$caller['file'].'<br>';
        $text.= 'line: '.$caller['line'].'<br>';
    }

    if (is_object($var) || is_array($var))
    {
        $text.= print_r($var,true);
        if ($backtrace_show)
        {
            $text.= '<br />'.print_r($bt,true);
        }
    }
    else
    {
        $text.= $var;
    }

    $text.= '</pre>'.PHP_EOL;

    if (!$echo)
    {
        return $text;
    }
    else
    {
        echo $text;
    }

    if ($die)
    {
        die;
    }
}

Mage::setIsDeveloperMode(true);

ini_set('display_errors', 1);
