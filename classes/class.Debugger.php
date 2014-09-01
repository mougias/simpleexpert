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
		
		// include all systems
		foreach (glob('systems'.DS.'*.php') as $filename)
		{
		    include $filename;
		}
		
		// init data array, put USER_INPUT in
		$this->vars['USER_INPUT'] = $userInput;

		// fetch steps tree from db
		$this->steps = StepFactory::fetchTree();
		
		// mark that we can now run
		$this->canRun = true;
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
		while ($currentStep && $count < self::MAX_STEPS) {
			$nextStep = null;
			$prevData = $this->vars;
			// process step
			$output = '';
			ob_start();
			switch ($currentStep->getType()) {
				case self::STEP_TYPE_SYSTEM:
				    list($system, $method) = array($currentStep->getSystem(), $currentStep->getMethod());
					if (method_exists($system, $method))
						$system::$method($this->vars);
					else {
						sendMessage($currentStep->getId(), 'Error while processing '.$currentStep->getSystem().'::'.$currentStep->getMethod(), '', $data);
						throw new Exception('Step '.$currentStep->getId().' requesting non-callable system/method');
					}
					// get next step
					$nextStep = $this->_getNextStep($currentStep);
					break;
				case self::STEP_TYPE_JUMP:
					// this will force the step marked to execute even if its conditions don't match current data
					// need to think about this, maybe it shouldn't be like that
					throw new exception ('Not implemented yet.');
				    break;
				case self::STEP_TYPE_EVALUATE:
				    // implement this.  try Eval Math from phpclasses
					throw new exception ('Not implemented yet.');
					break;
			}
			$output = ob_get_clean();
			$output = nl2br($output);
		
			// send output
			$this->_sendMessage($currentStep->getId(), $currentStep->getName(), $output, $prevData);
		
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
	private function _getNextStep(Step $currentStep = null)
	{
        if (!$currentStep) {
            // first run, scan top level steps
            foreach ($this->steps as $step)
                if (empty($step->getConditionVariableName()) || $this->vars[$step->getConditionVariableName()] == $step->getConditionVariableValue())
                    return $step;
        }
        else {
            foreach ($currentStep->getChildren() as $step)
                if (empty($step->getConditionVariableName()) || $this->vars[$step->getConditionVariableName()] == $step->getConditionVariableValue())
                    return $step;
        }
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

		echo '<script type="text/javascript">';
		echo $stringJS."\n\n";
		echo $dataJS."\n\n";
		echo $outputJS."\n\n";
		echo '</script>';
		
		flush();

	}

}
	
