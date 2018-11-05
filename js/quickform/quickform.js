function _jumpStep(fromid,step)
{
  var stepIsValid = true;
  var validator =$("#"+fromid).validate();
  if(step>1)
  {
    var i = 1;
    for(i=1;i<step;i++)
    {
       $(':input', $("#fieldset_step_"+i)).each( function(index) {
            
              var xy = validator.element(this);
              stepIsValid = stepIsValid && (typeof xy == 'undefined' || xy);
       });
       if(!stepIsValid)
       {
          return false;
       }
    }
  }
  if(stepIsValid)
  {
    $("#"+fromid).formToWizard( 'GotoStep',step);
  }
}
function _submitForm(action,method)
{ 
   document.quickForm.target = "";
   document.getElementById("exportmode").value = "";
   document.getElementById("qp_dataid").value = _getMainIds();
	if(action!="")
	{
		 document.quickForm.action = action;
	}
	if(method!="")
	{
		 document.getElementById("method").value = method;
	}
  
    document.quickForm.submit();

}

 function _editItem(id,processname)
 {
      document.getElementById("ed_dataid").value = id;
      document.getElementById("ed_processname").value = processname; 
      document.editData.submit();       
 }


function _refreshPage(anchor)
{ 
    try
    {
      document.getElementById("qp_anchor").value = anchor;
    }
    catch(err) {
    }   
    _search();
}

function _methodWithExport(action,method,exportmode)
{ 
 document.quickForm.target = "";
  document.getElementById("exportmode").value = "";
  if(action!="")
  {
     document.quickForm.action = action;
  }
  if(method!="")
  {
     document.getElementById("method").value = method;
  }
  if(exportmode!="")
  {
    document.getElementById("curPage").value = cur_page;
    document.quickForm.target = "export_frame";
    document.getElementById("exportmode").value = exportmode;

  }

    document.quickForm.submit();

}


     function _exportWithFormat(format)
     { 
       swal({
                              title: 'if the result have a lot of records,may be some error will happen,do you want to continue?',
                              text: '',
                              type: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Ok'
                            }).then(function() {
             document.getElementById("exportFormat").value = format;
             document.getElementById("curPage").value = 1;    
             document.getElementById("exportmode").value = "all"; 
             document.quickForm.target = "export_frame";
             document.quickForm.submit();  
         });
      }
     
    
 

 
function _exportForm(action,exportmode)
{
	  _methodWithExport(action,"",exportmode);
}

function _chooseAllCheckBoxes(obj,id)
{
		var elms  =  document.getElementsByName(id+"[]");
		 if(elms.length>0)
         {
             for(var i=0;i<elms.length;i++)   
             {
                 if(elms[i].type=="checkbox")  
                 {
                      elms[i].checked = obj.checked;
                 }
             }       
         }    
}

function _quickEdit(id,url)
{
	 
    var data = new Object();
    var vformmark =  $("#ed_formmark").val();
    data["formmark"] = vformmark;
    var visreport =  $("#ed_isreport").val();
    data["isreport"] = visreport;
    var vkey = $("#"+id).attr("key");
    data["key"] = vkey;
    var vkeyvalue = $("#"+id).attr("keyvalue");
    data["keyvalue"] = vkeyvalue;
    var vmainid = $("#"+id).attr("mainid");
    data["mainid"] = vmainid;
    var vmethod = $("#"+id).attr("method");  
    data["method"] = vmethod;
    var vdbname = $("#"+id).attr("dbname");  
    data["dbname"] = vdbname;
    var vcolsign =  $("#"+id).attr("name");
    data["colsign"] = vcolsign;
      elms = document.getElementsByName(vcolsign);
      if(elms.length>0)
         {
             for(var i=0;i<elms.length;i++)   
             {
                data[elms[i].id] = elms[i].value;
             }       
         }    
   
       
	  	$.post(url,data,
	  	function(result){
	  		
	  	}
	  	);
          
}

function _colMapping(obj,formid,prefix,aeid,ajaxmethod,typeid,type,keyid,key,mainidid,mainid,quickEditid)
{
	var options = {url:  '/include/quickform/quickAjaxColMapping.php',type:'post',dataType:'json',success: function (data) { $.each(data.html,function(key,value){
    $('#'+ prefix +key).html(value);   
    if(quickEditid!="")
    {
    	_quickEdit(quickEditid);
    }
    try{
         eval(data.validate);
        }
        catch(err)
        {

        }

    });}};  $('#ed_colmappingchoosedvalue').val(obj.value);$('#'+mainidid).val(mainid);$('#'+keyid).val(key);$('#'+typeid).val(type);$('#'+aeid).val(ajaxmethod);$('#'+formid).ajaxForm(options).ajaxSubmit(options);
}



$(document).ready(function(){
   $(":button").addClass("btn btn-primary btn-sm");
   $(":submit").addClass("btn btn-primary btn-sm");
   $(":file").addClass("form-control");
   $("table[class!=skipbootstrap]").addClass("table table-condensed");
   $('#searchBarCollapse').on('hidden.bs.collapse', function () {
    document.getElementById("searchBarCollapseStatus").value = 1; 
});
$(":checkBox[BootstrapSwitch]").bootstrapSwitch();
$('#searchBarCollapse').on('shown.bs.collapse', function () {
        
   document.getElementById("searchBarCollapseStatus").value = 0;
});
});