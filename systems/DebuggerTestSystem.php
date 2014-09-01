<?php 


class DebuggerTestSystem
{
    
    
    public static function getInputType(&$data)
    {
        if ($data['USER_INPUT'])
            $data['NUMBER_TYPE'] = 'TEST_NUMBER';
    }
    
}
