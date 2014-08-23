<?php 


class DebuggerTestSystem {
    
    
    public function getInputType(&$data) {
        if ($data['USER_INPUT'])
            $data['NUMBER_TYPE'] = 'Yes';
        
    }
    
}

?>