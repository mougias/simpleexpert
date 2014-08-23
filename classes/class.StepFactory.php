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
     * saves a step back into the database
     * @param Step $step
     */
    static public function save(Step $step) {
        //if (!($step instanceof Step))
            //throw new Exception('StepFactory::save expects object ot class Step');
        
        $db = DB::getInstance();
        
        if (!empty($step->getId())) {
            $query = $db->prepare('UPDATE debugger_step SET'.
                ' parent_id = ?,'.
                ' name = ?,'.
                ' type = ?,'.
                ' system = ?,'.
                ' method = ?,'.
                ' jump_id = ?,'.
                ' condition_variable_name = ?,'.
                ' condition_variable_value = ?,'.
                ' evaluate_expression = ?,'.
                ' evaluate_variable_name = ?'.
                ' WHERE id = ?');
        
            list($id, $name, $type, $system, $method, $jumpId, $conditionVariableName, $conditionVariableValue, $evaluateExpression, $evaluateVariableName, $parentId)
                    = array( $step->getId(),
                            $step->getName(),
                            $step->getType(),
                            $step->getSystem(),
                            $step->getMethod(),
                            $step->getJumpId(),
                            $step->getConditionVariableName(),
                            $step->getConditionVariableValue(),
                            $step->getEvaluateExpression(),
                            $step->getEvaluateVariableName(),
                            $step->getParentId()
                        );
            
            $query->bind_param('isississssi', $parentId, $name, $type, $system, $method, $jumpId, $conditionVariableName, $conditionVariableValue, $evaluateExpression, $evaluateVariableName, $id);
            $query->execute();
        }
        // new step
        else {
            $query = $db->prepare('INSERT INTO debugger_step ('.
                'parent_id, name, type, system, method, jump_id, condition_variable_name,'.
                'condition_variable_value, evaluate_expression, evaluate_variable_name)'.
                'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        
            list($id, $name, $type, $system, $method, $jumpId, $conditionVariableName, $conditionVariableValue, $evaluateExpression, $evaluateVariableName)
                    = array( $step->getParentId(),
                            $step->getName(),
                            $step->getType(),
                            $step->getSystem(),
                            $step->getMethod(),
                            $step->getJumpId(),
                            $step->getConditionVariableName(),
                            $step->getConditionVariableValue(),
                            $step->getEvaluateExpression(),
                            $step->getEvaluateVariableName()
                        );
            
            $query->bind_param('isississss', $id, $name, $type, $system, $method, $jumpId, $conditionVariableName, $conditionVariableValue, $evaluateExpression, $evaluateVariableName);
            $query->execute();
        
            if (!empty($db->insert_id))
                return $db->insert_id;
        }
    }
    
    
    
    /**
     * creates a step object from an array
     * @param array $step
     * @throws Exception
     */
    static public function createFromArray(array $step) {
        if (!is_array($step))
            throw new Exception ('StepFactory::createFromArray expects array as a parameter');
        
        return new Step($step);
    }
}