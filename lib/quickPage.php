<?php
namespace Quickplus\Lib;
use Quickplus\Lib\Tools\UrlTools;
	class QuickPage
	{
		protected static function getDataTableJs($src,$form,$tableId="quickTable")
        {
            $isreport =0;
            if($form->isReport())
            {
              $isreport =1;
            }
            $js= " $(document).ready(function() {var quickTable = $('#".$tableId."').DataTable({ 
                        paging: false,
                        bLengthChange:false,
                        bInfo:false,
                        bFilter:false, 
                        colReorder: true,  
                        fixedHeader: true,
                        responsive: true,
                        bAutoWidth : true, 
                        rowReorder: true,
                        order:[]";
            if($form->isShowTableButton())
            {
                $js.=",buttons: [ 
                     {text: 'Print',action: function ( e, dt, node, config ) {\$('#quickTable').print();}},";
                $js.=$form->getStatisticsScript();
                $js.=$form->getChartsScript();
                $js.=$form->getColVisScript();
                $js.= $form->getTemplateScript(); 

                $js.="  {extend: 'colvis',collectionLayout: 'fixed four-column'},
                         {
                                extend: 'colvisGroup',
                                text: 'Show All',
                                show: ':hidden'
                            } ]});
                quickTable.buttons().container()
                    .appendTo( '#quickTable_wrapper .col-sm-6:eq(0)' 
                ";
            }
            else
            {
              $js.="}";
            }
            $js.=");";
            if($form->hasExtendInfo())
            {
              $srcStr = "";
              foreach($src as $n=>$v)
              {
                if($n!="method")
                {
                  $srcStr.=",".$n.":$('#".$n."').val()";
                }
              }
              $js.=" $('#".$tableId." tbody').on('click', '.quickform_extendinfobutton', function () {
                  var tr = $(this).closest('tr');
                  var row = quickTable.row( tr );
                  if ( row.child.isShown() ) {
                      row.child.hide();
                  }
                  else {
                    $.post( '".QuickFormConfig::$quickFormMethodPath."getAjaxHtml.php'".',{formmark:"'.$form->getFormMark().'",isreport:'.$isreport.',method:"getQuickFormCommonExtendInfo",dbname:$(this).attr("dbname"),mainid:tr.attr("mainid")'. $srcStr.'}, function( data ) {
                         data = $.trim(data);
                        if(data!="")
                        {
                         row.child(data).show();
                        }
                      });
                     
                  }
              } );';
            }
            $js.="});";
            return $js;
        }

        public static function getPageJs($src,$form,$searchSign,$tableId="quickTable")
        {
            $url = UrlTools::getFullUrl();
            $statUrl =  UrlTools::getFullUrl(QuickFormConfig::$quickFormMethodPath."statistics.php");
            $chartUrl =  UrlTools::getFullUrl(QuickFormConfig::$quickFormMethodPath."charts.php");
            $js="<script language='javascript' >";
           $js.=self::getDataTableJs($src,$form,$tableId);
            $js.="function _clear()
                    {
                        window.location.href ='".$url."';
                    }";
            $js.="function _jumpPage(page)
                    {

                         document.getElementById('curPage').value = page; 
                         document.getElementById('method').value = '';
                         document.getElementById('exportmode').value = '';      
                         document.getElementById('searchSign').value = '".$searchSign."';
                         document.quickForm.target = '';
                         document.quickForm.action = '".$url."';
                         document.quickForm.submit();  
                    }";
            $js.=" function _changePage(id,max)
			    {
			             var pagenum = document.getElementById(id).value;
			             var reg = new RegExp('^[0-9]*$');      
			             var v = true;
			             if(!reg.test(pagenum)){
			                v = false;
			             }   
			             else
			             {
			                 if(pagenum<1||pagenum>max)
			                 {
			                     v = false;
			                 }
			             } 
			             if(v)
			             {
			                 _jumpPage(pagenum);
			             }
			             else
			             {
                            swal(
                                  'Please input a number between 1-'+max+'.',
                                  '',
                                  'error'
                                );
			                   
			             }
			    }";
			$js.="function _newSearch()
				    {
				         document.getElementById('qp_keeprowsids').value = '';    
				         document.getElementById('qp_excluderowsids').value = '';    
				         _search();
				    }";
			$js.="function _search()
					    {
					         document.getElementById('curPage').value = 1;    
					         document.getElementById('searchSign').value = 1;
					         document.getElementById('method').value = ''; 
					         document.getElementById('exportmode').value = ''; 
					         document.quickForm.target = '';
					         document.quickForm.action = '".$url."';
					         document.getElementById('ed_processname').value = ''; 
					         document.quickForm.submit();  
					    }";	  
            $js.="   function _pageExportWithFormat(format)
                     {
                        document.getElementById('exportFormat').value = format;
                        document.getElementById('curPage').value = '".$form->getCurPage()."';    
                        document.getElementById('exportmode').value = 'page'; 
                        document.quickForm.target = 'export_frame';
                        document.quickForm.submit();  
                     }";  
            $js.="function _showStat(setname)
                    {
                         document.getElementById('curPage').value = 1;    
                         document.getElementById('searchSign').value = 1;
                         document.getElementById('method').value = ''; 
                         document.getElementById('exportmode').value = '';
                         document.quickForm.target = '_blank';
                         document.quickForm.action = '".$statUrl."';
                         document.getElementById('ed_processname').value = ''; 
                         document.getElementById('_statistics_setname').value = setname; 
                         document.quickForm.submit();  
                    }";
            $js.="  function _showChart(charid)
                    {
                         document.getElementById('curPage').value = 1;    
                         document.getElementById('searchSign').value = 1;
                         document.getElementById('method').value = ''; 
                         document.getElementById('exportmode').value = ''; 
                         document.quickForm.target = '_blank';
                         document.quickForm.action = '".$chartUrl."';
                         document.getElementById('ed_processname').value = ''; 
                         document.getElementById('_statistics_setname').value = charid; 
                         document.quickForm.submit();  
                    }";
            $js.="  function _export()
                    { 
                        swal({
                              title: 'if the result have a lot of records,may be some error will happen,do you want to continue?',
                              text: \"\",
                              type: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Ok'
                            }).then(function() {
                             document.getElementById('curPage').value = 1;    
                                         document.getElementById('exportmode').value = 'all'; 
                                         document.quickForm.target = 'export_frame';
                                         document.quickForm.action = '".$url."';
                                         document.getElementById('exportFormat').value = '".$form->getDefaultExportFormat()."';
                                         document.getElementById('ed_processname').value = ''; 
                                         document.quickForm.submit();  
                            });
                        
                     }";
              $js.="  function _pageExport()
                        {
                             document.getElementById('curPage').value = '".$form->getCurPage()."';    
                             document.getElementById('exportmode').value = 'page'; 
                             document.quickForm.target = 'export_frame';
                             document.quickForm.action = '".$url."';
                             document.getElementById('exportFormat').value = '".$form->getDefaultExportFormat()."';
                             document.getElementById('ed_processname').value = ''; 
                             document.quickForm.submit();  
                        }";
             $js.=" function _getMainIds()
                    {
                         var batchName = '".$form->getCheckBoxId()."';
                         var elms  =  document.getElementsByName(batchName);
                         var result = new Array();
                         if(elms.length>0)
                         {
                             for(var i=0;i<elms.length;i++)   
                             {
                                 if(elms[i].type=='checkbox')  
                                 {
                                      if(elms[i].checked == true && elms[i].disabled!=true) 
                                      {
                                            result.push(elms[i].value); 
                                      } 
                                 }
                             }       
                         }    
                        return result;            
                    }";

            $js.=" function _changePagerows(id)
                 {
              
                      var pagerows = document.getElementById(id).value;  
                      document.getElementById('ed_processname').value = ''; 
                      var reg = new RegExp('^[0-9]*$');      
                      var v = true;
                      if(!reg.test(pagerows)){
                          v = false;
                      }   
                      else
                      {
                             if(pagerows<1)
                             {
                                 v = false;
                             }
                      } 
                         if(v)
                         {
                            var oldpage =  '".$form->getCurPage()."';
                            var oldpagerows =  '".$form->getPageRows()."';
                            var newpage =  '";
                            $pages = "1";
                            if($form->getPageRows()>0)
                            {
                              $pages = ceil(($form->getCurPage()-1)*$form->getPageRows()+1)/$form->getPageRows();
                            }
                            $js.=$pages."';
                            document.getElementById('pageRows').value = pagerows;
                            document.getElementById('curPage').value = newpage;
                            _search();
                         }
                         else
                         {
                              swal(
                                  'Please input a number greater than zero.',
                                  '',
                                  'error'
                                ); 
                             document.getElementById(id).value = '".$form->getPageRows()."';
                         }
                 }";
            $js.="  function  _checkAllCheckBox(obj){   
                   var batchName = '".$form->getCheckBoxId()."';
                   var  elms  =  document.getElementsByName(batchName);       
                   if  (elms.length<1)  
                   {
                        return;   
                   }
                   for  (var  i=0;i<elms.length;i++)   
                   {
                         if(elms[i].type=='checkbox'&&elms[i].disabled==false)  
                         {                     
                                 if(obj.checked == true)
                                 {
                                     elms[i].checked = true;
                                 }
                                 else
                                 {
                                     elms[i].checked = false;
                                 }
                         }  
                   }} ";
            $js.="function _add()
                {
                    document.getElementById('ed_dataid').value = ''; 
                    document.getElementById('ed_processname').value = ''; 
                    document.editData.submit();
                }";
             $js.=" function _delete()
                    {
                        var elms = _getMainIds();
                        if(elms.length <1)
                        {
                            swal(
                                  'Please choose a record at first.',
                                  '',
                                  'error'
                                ); 
                            return false;
                        }
                        else
                        {
                            var id = '';
                            for (var  i=0;i<elms.length;i++)
                            {
                                if(id !='')
                                {
                                    id = id + \",\";
                                }
                                id = id + \"'\" + elms[i] + \"'\"
                            }   
                            document.getElementById('deleteid').value = id;
                          
                           _search();
                        }
                    }";
             $js.="function _edit()
				    {
				        var elms = _getMainIds();
				        if(elms.length <1)
				        {
                             swal(
                                  'Please choose a record at first.',
                                  '',
                                  'error'
                                ); 
				            return false;
				        }
				        else if(elms.length > 1)
				        {
                              swal(
                                  'choose one record for edit.',
                                  '',
                                  'error'
                                ); 
				            return false;
				        }   
				        else
				        {
				             document.getElementById('ed_dataid').value = elms[0];
				             document.getElementById('ed_processname').value = ''; 
				             document.editData.submit();
				        }
				    }";
			$js.=" function _customProcess(processName)
				    {
				        var elms = _getMainIds();
				        if(elms.length <1)
				        {
				               swal(
                                  'Please choose a record at first.',
                                  '',
                                  'error'
                                );    
				            return false;
				        }
				        else if (elms.length > 1)
				        {
                              swal(
                                  'Please choose one record to operate.',
                                  '',
                                  'error'
                                ); 
				            return false;
				        }   
				        else
				        {
				             document.getElementById('ed_dataid').value = elms[0];
				             document.getElementById('ed_processname').value = processName; 
				             document.editData.submit();
				        }
				    }"; 
			$js.="function _keepRows()
			      {
				        var elms = _getMainIds();
				        if(elms.length <1)
				        {
				             swal(
                                  'Please choose a record at first.',
                                  '',
                                  'error'
                                );      
				            return false;
				        }
				        else
				        {
				            var id = '';
				            for (var  i=0;i<elms.length;i++)
				            {
				                if(id !='')
				                {
				                    id = id + ',';
				                }
				                id = id + \"'\" + elms[i] + \"'\"
				            }   
				            document.getElementById('qp_keeprowsids').value = id;
				           _search();
				        }
				    }";
			$js.=" function _excludeRows()
				    {
				        var elms = _getMainIds();
				        if(elms.length <1)
				        {
				             swal(
                                  'Please choose a record at first.',
                                  '',
                                  'error'
                                );      
				            return false;
				        }
				        else
				        {
				            var id = '';
				            for (var  i=0;i<elms.length;i++)
				            {
				                if(id !='')
				                {
				                    id = id + ',';
				                }
				                id = id + \"'\" + elms[i] + \"'\"
				            }   
				            var oldvalue = document.getElementById('qp_excluderowsids').value;
				            if(oldvalue!='')
				            {
				                id = oldvalue+','+id;
				            }
				            document.getElementById('qp_excluderowsids').value = id;
				           _search();
				        }
				    }";
         $autoRefreshTime = 1000*$form->getAutoRefreshTime($src);  
         if($form->getAutoRefresh()&&$autoRefreshTime>0)
         {
            $js .="setTimeout('window.location.reload()',".$autoRefreshTime.");";
         }
        
		     $js.="</script>";
		     return $js;
        }
	}
?>