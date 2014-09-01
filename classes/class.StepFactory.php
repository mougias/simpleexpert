<?php

/**
 * 
 * @author Stefanos Demetriou
 *
 */
class StepFactory
{
    /**
     * fetches full array of steps from the database
     * @return multitype:Step
     */
    static public function fetchTree() {
        $db = DB::getInstance();
        
        $out = array();
        $tmpArr = array();
        $result = $db->query('SELECT * FROM debugger_step');
        while ($tmpArr[] = $result->fetch_assoc());
        foreach ($tmpArr as $tmp)
            if (isset($tmp['parent_id']) && $tmp['parent_id'] == 0)
                $out[] = new Step($tmp, $tmpArr);

        return $out;
    }
    
    
    /**
     * fetches a single Step from the database given by its id
     * @param int $id
     * @return Step|NULL
     */
    static public function fetch($id) {
        $db = DB::getInstance();
        
        $query = $db->prepare('SELECT * FROM debugger_step WHERE id = ?');
        $query->bind_param('i', $id);
        $query->execute();
        $res = $query->get_result();
        
        if ($res->num_rows)
            return new Step($res->fetch_assoc());

        return null;
    }
    
    
    /**
     * delete a single step from the database given its id
     * @param int $id
     * @return boolean
     */
    static function delete($id) {
        $db = DB::getInstance();

        $query = $db->prepare('DELETE FROM debugger_step WHERE id = ?');
        $query->bind_param('i', $id);
        $query->execute();
                        
        if ($query->affected_rows)
            return true;
        
        return false;
    }
    
    
  
    
    /**
     * creates a step object from an array
     * @param array $step
     * @throws Exception
     */
    static public function createFromArray(array $step) {
        if (isset($step['parentId'])) $step['parent_id']= $step['parentId'];
        if (isset($step['jumpId'])) $step['jump_id']= $step['jumpId'];
        if (isset($step['conditionVariableName'])) $step['condition_variable_name'] = $step['conditionVariableName'];
        if (isset($step['conditionVariableValue'])) $step['condition_variable_value'] = $step['conditionVariableName'];
        if (isset($step['evaluateExpression'])) $step['evaluate_expression'] = $step['evaluateExpression'];
        if (isset($step['evaluateVariableName'])) $step['evaluate_variable_name'] = $step['evaluateVariableName'];
                
        return new Step($step);
    }
}