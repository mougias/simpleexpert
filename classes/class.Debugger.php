<?php

/**
 * Tool used to receive a number from the user, recognize its type, and fetch information from all
 * relevant systems
 * 
 * @author Stefanos Demetriou
 *
 */
class Debugger
{

	const MAX_STEPS = 50;
	
	const STEP_TYPE_SYSTEM = 0;
	const STEP_TYPE_JUMP = 1;
	const STEP_TYPE_EVALUATE = 2;
	
	private $vars = array();
	private $steps = array();
	private $canRun = false;

	/**
	 * constructor
	 * @param string $userInput
	 */
	public function __construct($userInput)
	{
		// for the debugger to start working it must be given an initial value
		if (empty($userInput))
			return;
		
		// init data array, put USER_INPUT in
		$this->vars['USER_INPUT'] = $userInput;

		// mark that we can now run
		$this->canRun = true;
		

		// fetch steps from DB
		$db = DB::getInstance();
		$result = $db->query('SELECT id, parent_id, name, type, system, method, jump_id, condition_variable_name, condition_variable_Value, assert_expression, assert_variable_name FROM debugger_step');
		while ($arr = $result->fetch_assoc())
		    $this->steps[] = StepFactory::createFromArray($arr);

		if (!$this->steps) {
		    throw new Exception('Could not fetch steps from the database');
		}
	}
	

   
	
	
	/**
	 * performs main loop
	 */
	public function run()
	{
		if (!$this->canRun)
			throw new Exception('Debugger is not ready to run.');
		
		$currentStep = $this->_getNextStep(null); // fetch next step
		
		$count = 0;
		while ($currentStep && $count < MAX_STEPS) {
			$nextStep = null;
			$prevData = $this->vars;
			// process step
			$output = '';
			ob_start();
			switch ($currentStep->getType()) {
				case STEP_TYPE_SYSTEM:
				    list($system, $method) = array($currentStep->getSystem(), $currentStep->getMethod());
					if (method_exists($system, $method))
						$system::$method($this->vars);
					else {
						sendMessage($currentStep->getId(), 'Error while processing '.$currentStep->getSystem().'::'.$currentStep->getMethod(), '', $data);
						throw new Exception('Step '.$currentStep->getId().' requesting non-callable system/method');
					}
					// get next step
					$nextStep = getNextStep($currentStep->getId());
					break;
				case STEP_TYPE_JUMP:
					// this will force the step marked to execute even if its conditions don't match current data
					// need to think about this, maybe it shouldn't be like that
					echo 'Jumping to step '.$currentStep['jumpId']."\n";
					$nextStep = getStepById($currentStep['jumpId'], $stepsArray);
					if (!$currentStep)
						echo "Step not found.  Check your configuration\n";
					break;
				case STEP_TYPE_EVALUATE:
					// get next step
					$nextStep = getNextStep($currentStep['id'], $stepsArray, $data);
					break;
			}
			$output = ob_get_clean();
			$output = nl2br($output);
			ob_end_clean();
		
			// send output
			sendMessage($currentStep['id'], $currentStep['name'], $output, $prevData);
		
			$currentStep = $nextStep;
			$count++;
		}
	}
	
	
	/**
	 * Loops through all of the current step's children and evaluates their condition variable
	 * according to the current data array.  Returns the step that matches, or null
	 * 
	 * @param int $currentStep
	 * @return int
	 */
	private function _getNextStep($currentStep)
	{
		foreach ($this->steps as $step)
		    if ($step->getParentId() == $currentStep)
		        if (empty($step->getConditionVariableName()) || $this->vars[$step->getConditionVariableName()] == $step->getConditionVariableValue())
                    return $step;

		return null;
	}
	
	
	/**
	 * Used when execution has started to send the output of each step back to the client
	 * @param unknown $id
	 * @param unknown $msg
	 * @param unknown $output
	 * @param unknown $data
	 */
	private function _sendMessage($id, $msg, $output)
	{
		$stringJS = '$("#stepsContainer").append('."'";
		$stringJS .= '<div style="float: left; width: 100%">';
		$stringJS .= '<a href="javascript:activateStep('.$id.')">'.$id.' - '.$msg.'</a>';
		$stringJS .= '</div>';
		$stringJS .= "');";
	
		$dataText = var_export($this->vars, true);
		$dataText = str_replace("'", '"', $dataText);
		$dataText = nl2br($dataText);
		$dataText = str_replace("\n", '', $dataText);
	
		$dataJS = '$("#dataContainer").append('."'";
		$dataJS .= '<div class="canhide" id="data'.$id.'">';
		$dataJS .= $dataText;
		$dataJS .= '</div>'."');";
		$dataJS .= '$("#data'.$id.'").hide();';
	
		$outputJS = '$("#outputContainer").append('."'";
		$outputJS .= '<div class="canhide" id="output'.$id.'">';
		$outputJS .= $output;
		$outputJS .= '</div>'."');";
		$outputJS .= '$("#output'.$id.'").hide();';

		echo $stringJS."\n\n";
		echo $dataJS."\n\n";
		echo $outputJS."\n\n";

	}

}
	
