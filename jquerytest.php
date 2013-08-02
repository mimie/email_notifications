<script type="text/javascript" src="jquery-1.9.1.js" ></script>
<p id="test1"> this is div 1</p><br>
<p id=test2>this is div2</p><br>
<div id=test>
	<form>
		test me not
	</form>

</div>
<div id=test_2>
	test number2
</div>

<script>
$('#test1').click(function(){
	$('#test').show();
	$('#test_2').hide();

});

$('#test2').click(function(){
	$('#test_2').show();
	$('#test').hide();

});
</script>
