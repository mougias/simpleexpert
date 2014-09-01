<?php 

/**
 * 
 * @author Stefanos Demetriou
 *
 */
class Step
{
    
    private $id;
    private $parent_id;
    private $name;
    private $type;
    private $system;
    private $method;
    private $jump_id;
    private $condition_variable_name;
    private $condition_variable_value;
    private $evaluate_expression;
    private $evaluate_variable_name;
    
    // this makes Step a doubly linked tree
    private $children = array();
    
    public function __construct(array $step, array $allSteps = null) {
        if (isset($step['id'])) $this->id = $step['id'];
        if (isset($step['parent_id'])) $this->parent_id = $step['parent_id'];
        if (isset($step['name'])) $this->name = $step['name'];
        if (isset($step['type'])) $this->type = $step['type'];
        if (isset($step['system'])) $this->system = $step['system'];
        if (isset($step['method'])) $this->method = $step['method'];
        if (isset($step['jump_id'])) $this->jump_id = $step['jump_id'];
        if (isset($step['condition_variable_name'])) $this->condition_variable_name = $step['condition_variable_name'];
        if (isset($step['condition_variable_value'])) $this->condition_variable_value = $step['condition_variable_value'];
        if (isset($step['evaluate_expression'])) $this->evaluate_expression = $step['evaluate_expression'];
        if (isset($step['evaluate_variable_name'])) $this->evaluate_variable_name = $step['evaluate_variable_name'];

        if (is_array($allSteps))
            foreach ($allSteps as $tmp)
                if ($tmp['parent_id'] == $this->id)
                    $this->children[] = new Step($tmp, $allSteps);
    }
    
    
    public function getId() {
        return $this->id;
    }
    
    public function getParentId() {
        return $this->parent_id;
    }
    
    public function setParentId($pid) {
        $this->parent_id = $pid;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function setType($type) {
        $this->type = $type;
    }
    
    public function getSystem() {
        return $this->system;    
    }
    
    public function setSystep($system) {
        $this->system = $system;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function setMethod($method) {
        $this->method = $method;
    }
    
    public function getJumpId() {
        return $this->jump_id;
    }
    
    public function setJumpId($jumpId) {
        $this->jump_id = $jumpId;
    }
    
    public function getConditionVariableName() {
        return $this->condition_variable_name;
    }
    
    public function setConditionVariableName($conditionVariableName) {
        $this->condition_variable_name = $conditionVariableName;
    }
    
    public function getConditionVariableValue() {
        return $this->condition_variable_value;
    }
    
    public function setConditionVariableValue($conditionVariableValue) {
        $this->condition_variable_value = $conditionVariableValue;
    }
    
    public function getEvaluateExpression() {
        return $this->evaluate_expression;
    }
    
    public function setEvaluateExpression($evaluateExpression) {
        $this->evaluate_expression = $evaluateExpression;
    }
    
    public function getEvaluateVariableName() {
        return $this->evaluate_variable_name;
    }
    
    public function setEvaluateVariableName($evaluateVariableName) {
        $this->evaluate_variable_name = $evaluateVariableName;
    }
    
    
    
    public function getChildren() {
        return $this->children;
    }

    /**
     * gets the current execution data array and returns whether its condition matches with this data.
     * 
     * @param array $data
     * @throws Exception
     * @return boolean
     */
    public function condtionMatches(array $data) {
        //if (!is_array($data))
          //  throw new Exception ('How on earth did you pass a non-array to conditionMatches?');
        
        
        // when no condition was given the step always matches
        if (empty($this->condition_variable_name))
            return true;
        
        if (!empty($data[$this->condition_variable_name]) && $data[$this->condition_variable_name] == $this->condition_variable_value)
            return true;
        
        return false;
    }
    
    
    
    /**
     * saves a step back into the database
     * @param Step $step
     */
    public function save() {
    
        $db = DB::getInstance();
    
        if (!empty($this->getId())) {
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
            = array( $this->getId(),
                $this->getName(),
                $this->getType(),
                $this->getSystem(),
                $this->getMethod(),
                $this->getJumpId(),
                $this->getConditionVariableName(),
                $this->getConditionVariableValue(),
                $this->getEvaluateExpression(),
                $this->getEvaluateVariableName(),
                $this->getParentId()
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
            = array( $this->getParentId(),
                $this->getName(),
                $this->getType(),
                $this->getSystem(),
                $this->getMethod(),
                $this->getJumpId(),
                $this->getConditionVariableName(),
                $this->getConditionVariableValue(),
                $this->getEvaluateExpression(),
                $this->getEvaluateVariableName()
            );
    
            $query->bind_param('isississss', $id, $name, $type, $system, $method, $jumpId, $conditionVariableName, $conditionVariableValue, $evaluateExpression, $evaluateVariableName);
            $query->execute();
    
            if (!empty($db->insert_id))
                return $db->insert_id;
        }
    }    
    
}