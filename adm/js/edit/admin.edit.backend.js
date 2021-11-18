/*DIAFAN.CMS*/

$("select[name=backend]").change(function() {
	if($("select[name=backend] option:selected").data("href"))
	{
		window.open($("select[name=backend] option:selected").data("href"), "_blank");
		$("select[name=backend] option").first().prop("selected", true);
		return false;
	}
	$("#name input[name=name]").val($( "select[name=backend] option:selected" ).text());
});