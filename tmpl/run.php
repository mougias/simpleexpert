<div id="stepsContainer" style="float: left; width: 250px">
<h2>Steps:</h2>
</div>

<div id="outputContainer" style="float: left; width: 250px">
<h2>Output:</h2>
</div>

<div id="dataContainer" style="float: left; width: 250px">
<h2>Data:</h2>
</div>

<div id="debuggerDebug">
</div>

<script type="text/javascript">
function createXMLHttpRequest() {
	try { return new XMLHttpRequest(); } catch(e) {}
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	alert("XMLHttpRequest not supported");
	return null;
}

var xhReq = createXMLHttpRequest();
xhReq.open("get", "<?php echo Engine::getCurrentPage()->getComponentPath().DS.'run.php?user_input='.$_POST['user_input'] ?>", true);
xhReq.onreadystatechange = function() {
	if (xhReq.readyState != 4)  { return; }
	var serverResponse = xhReq.responseText;
	console.log(serverResponse);
	eval(serverResponse);
};
xhReq.send(null);




function activateStep(id) {
	$(".canhide").hide();

	$("#output" + id).show();
	$("#data" + id).show();
}
</script>