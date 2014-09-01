<html>

<head>
    <link rel="stylesheet" type="text/css" href="tmpl/css/style.css">
    <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
</head>

<body>

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




function activateStep(id) {
	$(".canhide").hide();

	$("#output" + id).show();
	$("#data" + id).show();
}
</script>

</body>
</html>