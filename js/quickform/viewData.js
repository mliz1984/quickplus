function _viewDataDetail(id,isReport,formMark)
{
	 var oriaction =  document.editData.action;
	 var oritarget =  document.editData.target;
	 var oriIsReport = document.getElementById("ed_isreport").value;
	 var oriFormMark = document.getElementById("ed_formmark").value;
	 document.getElementById("ed_dataid").value = id; 
	 document.getElementById("ed_isreport").value = isReport; 
	 document.getElementById("ed_formmark").value = formMark; 
	 document.editData.action = "/include/quickform/viewData.php";
	 document.editData.target="_blank";
	 document.editData.submit();
	 document.editData.action = oriaction;
	 document.editData.target= oritarget;
	 document.getElementById("ed_isreport").value = oriIsReport;
	 document.getElementById("ed_formmark").value = oriFormMark;
}