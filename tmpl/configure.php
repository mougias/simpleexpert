<html>

<head>
    <link rel="stylesheet" type="text/css" href="tmpl/css/style.css">
    <link rel="stylesheet" type="text/css" href="tmpl/css/simpletreemenu.css">
    <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
    <script src="tmpl/js/simpletreemenu.js" type="text/javascript"></script>
</head>

<body>
<?php

function displayChildren($steps = array(), $parentId = 0)
{
    echo '<ul id="tree' . $parentId . '"';
    if ($parentId == 0)
        echo ' class="treeview"';
    echo '>';
    
    foreach ($steps as $step) {
        echo '<li>';
        echo '<a draggable="true" class="stepItem" data-parentId="' . $step->getParentId() . '" data-stepId="' . $step->getId() . '" onclick="javascript: showInfo(' . $step->getId() . ')">' . $step->getName() . '</a>';
        displayChildren($step->getChildren(), $step->getId());
        echo '</li>';
    }
    echo '<li><a draggable="false" href="javascript:showPopup(' . $parentId . ');">New step</a></li>';
    
    echo '</ul>';
}



function displayStepDiv(Step $step)
{
    global $vars;
    echo "
        <div id=\"info{$step->getId()}\">
            <h2>Step Properties:</h2>
            <form method=\"post\" name=\"infoForm{$step->getId()}\">
                <fieldset>
                    <table>
						<tr>
							<td>Step ID:</td>
							<td>{$step->getId()}</td>
						</tr>
						<tr>
							<td>Step Name:</td>
							<td><input type=\"text\" name=\"info[{$step->getId()}][name]\" value=\"{$step->getName()}\" size=60 /></td>
						</tr>
						<tr>
							<td>Condition Variable:</td>
							<td><input type=\"text\" name=\"info[{$step->getId()}][conditionVariableName]\" value=\"{$step->getConditionVariableName()}\" size=30 /></td>
						</tr>
						<tr>
							<td>Condition Value:</td>
							<td><input type=\"text\" name=\"info[{$step->getId()}][conditionVariableValue]\" value=\"{$step->getConditionVariableValue()}\" size=30 /></td>
						</tr>
						<tr>
							<td>Type:</td>
							<td>
                                <select name=\"info[{$step->getId()}][type]\" onchange=\"changeStepType({$step->getId()}, this.value)\">";
                                if (isset($vars['types']) && is_array($vars['types'])) {
                                    foreach ($vars['types'] as $key => $val) {
                                        echo "<option value=\"{$key}\"";
                                        if ($key == $step->getType())
                                            echo ' selected="selected"';
                                        echo '>'.$val.'</option>';
                                    }
                                }
    echo "                                                                    
                                </select>
                            </td>
						</tr>
						<tr id=\"system{$step->getId()}\">
							<td>System:</td>
							<td>
                                <select name=\"info[{$step->getId()}][system]\" onchange=\"updateMethods(this.value, {$step->getId()})\">
                                <option></option>";
                                if (isset($vars['systems']) && is_array($vars['systems']))
                                    foreach ($vars['systems'] as $key => $val) {
                                        echo "<option value=\"{$val['name']}\"";
                                        if ($val['name'] == $step->getSystem())
                                            echo ' selected=seleceted';
                                        echo ">{$val['name']}</option>";
                                    }
    echo "
                                </select>
                            </td>
						</tr>
						<tr id=\"method{$step->getId()}\">
							<td>Method:</td>
							<td>
                                <select id=\"methods{$step->getId()}\"	name=\"info[{$step->getId()}][method]\">";
                                    if (isset($vars['methods'][$step->getSystem()]))
                                        foreach ($vars['methods'][$step->getSystem()] as $key => $val) { 
                                            echo "<option value=\"{$val}\"";
                                            if ($val == $step->getMethod())
                                                echo ' selected=selected';
                                            echo ">{$val}</option>";
                                        }
    echo "
                                </select>
                            </td>
                        </tr>
						<tr id=\"jump{$step->getId()}\">
							<td>Jump to step:</td>
							<td>
                                <input type=\"text\" class=\"jump\"	name=\"info[{$step->getId()}][jumpId]\" value=\"{$step->getJumpId()}\" size=5 />
							</td>
						</tr>
						<tr id=\"evaluateExpression{$step->getId()}\">
                            <td>Evaluate Expression:</td>
							<td>
                                <input type=\"textbox\" name=\"info[{$step->getId()}][evaluateExpression]\" value=\"{$step->getEvaluateExpression()}\" size=60 />
							</td>
						</tr>
						<tr id=\"evaluateVariable{$step->getId()}\">
							<td>Evaluate Result:</td>
							<td>
                                <input type=\"text\" name=\"info[{$step->getId()}][evaluateVariableName]\" value=\"{$step->getEvaluateVariableName()}\" />
							</td>
						</tr>
						<tr>
							<td colspan=\"2\">&nbsp;</td>
						</tr>
						<tr>
							<td colspan=\"2\"><input type=\"submit\" value=\"Update\" /></td>
						</tr>
					</table>
				</fieldset>
			</form>

			<form method=\"post\" name=\"deleteForm[{$step->getId()}\">
                <fieldset style=\"width: 100%\">
				    <input type=\"hidden\" name=\"deleteStep\" value=\"{$step->getId()}\" />
				    <table>
					   <tr>
						  <td colspan=\"2\"><input type=\"submit\" value=\"Delete\" /></td>
					   </tr>
				    </table>
                </fieldset>
			</form>
			<p>**If you leave condition variable and condition value empty this
				step will always match for execution</p>
		</div>";

    foreach ($step->getChildren() as $step)
        displayStepDiv($step);

}
?>



<div style="width: 480px; float: left;">
    <h2>Steps:</h2>
    <?php displayChildren($vars['steps'], 0); ?>
</div>

<div style="width: 480px; float: left;">
    <?php foreach ($vars['steps'] as $step) : ?>
        <?php displayStepDiv($step); ?>
    <?php endforeach; ?> 




    <div id="fade" class="black_overlay"></div>
    <div id="light" class="popup_box">
        <h2>New Step:</h2>
        <form method="post" name="infoFormNew">
            <fieldset>
                <table>
                    <input type="hidden" id="hiddenNewParentId"	name="infoNew[parentId]" />
                    <tr>
                        <td>Step Name:</td>
                        <td><input type="text" name="infoNew[name]" size=60 /></td>
                    </tr>
                    <tr>
                        <td>Condition Variable:</td>
                        <td><input type="text" name="infoNew[conditionVariableName]" size=30 /></td>
                    </tr>
                    <tr>
                        <td>Condition Value:</td>
                        <td><input type="text" name="infoNew[conditionVariableValue]" size=30 /></td>
                    </tr>
                    <tr>
                        <td>Type:</td>
                        <td>
                            <select name="infoNew[type]" onchange="changeStepType('New', this.value)">
                            <?php if (isset($vars['types']) && is_array($vars['types'])) foreach ($vars['types'] as $key => $val) :?>
                                <option value="<?=$key?>"><?=$val?></option>
                            <?php endforeach;?>
                            </select>
                        </td>
                    </tr>
                    <tr id="systemNew">
                        <td>System:</td>
                        <td>
                            <select name="infoNew[system]" onchange="updateMethods(this.value, 'New')">
                                <option value="" />
                                <?php if (isset($vars['systems']) && is_array($vars['systems'])) foreach ($vars['systems'] as $key => $val) :?>
                                    <option value="<?=$va['name']?>"><?=$val['name']?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                    </tr>
                    <tr id="methodNew">
                        <td>Method:</td>
                        <td><select id="methodsNew" name="infoNew[method]"></select></td>
                    </tr>
                    <tr id="jumpNew">
                        <td>Jump to step:</td>
                        <td><input type="text" class="jump" name="infoNew[jumpId]" size=5 /></td>
                    </tr>
                    <tr id="evaluateExpressionNew">
                        <td>Evaluate Expression:</td>
                        <td><input type="text" name="infoNew[evaluateExpression]" size=60 /></td>
                    </tr>
                    <tr id="evaluateVariableNew">
                        <td>Evaluate Result:</td>
                        <td><input type="text" name="infoNew[evaluateVariableName]" /></td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Save" /></td>
                    </tr>
                </table>
                <p>**If you leave condition variable and condition value empty this step will always match for execution</p>
            </fieldset>
        </form>
		
        <form id="moveStepForm" name="moveStepForm" method="post">
            <input type="hidden" name="moveStep" value=1 />
            <input type="hidden" name="stepId" id="moveStepFormStepId" />
            <input type="hidden" name="newParentId" id="moveStepFormNewParentId" />
        </form>
				
        <div style="position: absolute; top: 0%; left: 95%">
            <a href="javascript:hidePopup()"><img src="tmpl/images/icon_close.png" width=32 height=32 /></a>
        </div>
    </div>
</div>


<script type="text/javascript">
$(document).ready(function() {

	ddtreemenu.createTree("tree0", true);
<?php

function hideStepOptionsAndChildren($step) {
    echo '$("#system' . $step->getId() . '").hide();';
    echo '$("#method' . $step->getId() . '").hide();';
    echo '$("#jump' . $step->getId() . '").hide();';
    echo '$("#evaluateExpression' . $step->getId() . '").hide();';
    echo '$("#evaluateVariable' . $step->getId() . '").hide();';

    if ($step->getType() == Debugger::STEP_TYPE_SYSTEM) {
        echo '$("#system' . $step->getId() . '").show();';
        echo '$("#method' . $step->getId() . '").show();';
    } elseif ($step->getType() == Debugger::STEP_TYPE_JUMP) {
        echo '$("#jump' . $step->getId() . '").show();';
    } elseif ($step->getType() == Debugger::STEP_TYPE_EVALUATE) {
        echo '$("#evaluateExpression' . $step->getId() . '").show();';
        echo '$("#evaluateVariable' . $step->getId() . '").show();';
    }
    
    foreach ($step->getChildren() as $step)
        hideStepOptionsAndChildren($step);
}

foreach ($vars['steps'] as $step)
    hideStepOptionsAndChildren($step);
?>

	$("#evaluateExpressionNew").hide();
	$("#evaluateVariableNew").hide();
	$("#jumpNew").hide();	
	showInfo(0);
});



/**
 * used to hide the div holding the forms of all steps
 * and show only the one that is passed as parameter (i.e. clicked on)
 */
function showInfo($id) {
<?php
function hideStepAndChildren($step) {
    echo "\t" . '$("#info' . $step->getId() . '").hide();' . "\n";
    foreach ($step->getChildren() as $step)
        hideStepAndChildren($step);
}

foreach ($vars['steps'] as $step)
    hideStepAndChildren($step);
?>
	$("#info"+$id).show();
}


/**
 * triggers when a step type is changed.  hides the old relevant fields and displays the new ones
 */
function changeStepType($id, $type) {
	$("#system" + $id).hide();
	$("#method" + $id).hide();
	$("#jump" + $id).hide();
	$("#evaluateExpression" + $id).hide();
	$("#evaluateVariable" + $id).hide();	
	if ($type == <?php echo Debugger::STEP_TYPE_SYSTEM?>) {
		$("#system" + $id).show();
		$("#method" + $id).show();
	}
	else if ($type == <?php echo Debugger::STEP_TYPE_JUMP?>) {
		$("#jump" + $id).show();
	}
	else if ($type == <?php  echo Debugger::STEP_TYPE_EVALUATE?>) {
		$("#evaluateExpression" + $id).show();
		$("#evaluateVariable" + $id).show();
	}
}


/**
 * shows the popup (lightbox type) for creating a new item
 */
function showPopup($id) {
	$("#hiddenNewParentId").val($id);
	$("#light").show();
	$("#fade").show();
}


/**
 * hides the popup (lightbox type) for creating a new item
 */
function hidePopup() {
	$("#light").hide();
	$("#fade").hide();
}



function updateMethods(system, id) {
	var methods = new Object();
	<?php
	   global $vars;
        foreach ($vars['methods'] as $system => $names) {
            echo "methods['$system'] = [];";
            foreach ($names as $method)
                echo 'methods["' . $system . '"].push("' . $method . '");' . "\n";
        }
    ?>

	$("#methods" + id).find('option').remove();

	$(methods[system]).each(function(key, value) {
		
		$("#methods" + id).append(new Option(value, value));
	});
		
}


// drag and drop stuff

/**
 * handles the drop operation when a list item is dragged and dropped.
 * Used to move a step to a new parent, or to auto-insert its id in the jump type 
 */
function handleDragDrop(e) {
	if (e.stopPropagation) {
		e.stopPropagation(); // stops the browser from redirecting.
	}

	var stepId = e.dataTransfer.getData('stepId');
	var newParentId = $(e.target).attr("data-stepId");
	var oldParentId = e.dataTransfer.getData('parentId');

	if (!stepId) { // something went wrong
		return false;
	}

	if (!newParentId) {
		newParentId = 0;
	}

	if (stepId == newParentId) { // cannot move to self
		false;
	}

	if (newParentId == oldParentId) { // cannot move to same parent
		return false;
	}
	
	if (!confirm("Are you sure you want to move this?")) {
		return false;
	}

	if ($(e.target).hasClass('jump')) {
		($(e.target).val(stepId));
	}
		
	$("#moveStepFormStepId").val(stepId);
	$("#moveStepFormNewParentId").val(newParentId);
	$("form[name=moveStepForm]").submit();

	return false;
}


/**
 * triggers when a drag is started
 * used to store step id and parent step id in the data transfer to be read by the drop trigger
 */
function handleDragStart(e) {
	e.dataTransfer.effectAllowed = 'move';
	e.dataTransfer.setData('stepId', $(e.target).attr("data-stepId"));
	e.dataTransfer.setData('parentId', $(e.target).attr("data-parentId"));
}


/**
 * triggers when a dragged item is moved over another item
 * I needed to prevent default browser behaviour here to continue dragging
 * so it can be dropped later
 */
function handleDragOver(e) {
	if (e.preventDefault) {
		e.preventDefault();
	}

	e.dataTransfer.dropEffect = 'move';

	return false; 
}


var as  = document.querySelectorAll('a.stepItem');
[].forEach.call(as, function(a) {
  a.addEventListener('dragstart', handleDragStart, false);
  a.addEventListener('dragover', handleDragOver, false);
  a.addEventListener('drop', handleDragDrop, false);
});



</script>
</body>
</html>