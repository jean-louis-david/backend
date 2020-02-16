<?php
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = 'Preferences';

$iCompanyId = isset($_REQUEST['iCompanyId']) ? $_REQUEST['iCompanyId'] : '';

//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY rd.iDriverId DESC';
if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY rd.vName ASC";
  else
  $ord = " ORDER BY rd.vName DESC";
}
if($sortby == 2){
  if($order == 0)
  $ord = " ORDER BY c.vCompany ASC";
  else
  $ord = " ORDER BY c.vCompany DESC";
}
if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY rd.vEmail ASC";
  else
  $ord = " ORDER BY rd.vEmail DESC";
}

if($sortby == 4){
  if($order == 0)
  $ord = " ORDER BY rd.tRegistrationDate ASC";
  else
  $ord = " ORDER BY rd.tRegistrationDate DESC";
}

if($sortby == 5){
  if($order == 0)
  $ord = " ORDER BY rd.eStatus ASC";
  else
  $ord = " ORDER BY rd.eStatus DESC";
}

if($sortby == 6){
  if($order == 0)
  $ord = " ORDER BY `count` ASC";
  else
  $ord = " ORDER BY `count` DESC";
}
//End Sorting
$dri_ssql = "";
if (SITE_TYPE == 'Demo') {
    $dri_ssql = " And rd.tRegistrationDate > '" . WEEK_DATE . "'";
}

// Start Search Parameters
$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
$searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
$ssql = '';
if($keyword != ''){
    if($option != '') {
        if (strpos($option, 'eStatus') !== false) {
            $ssql.= " AND ".stripslashes($option)." LIKE '".$generalobjAdmin->clean($keyword)."'";
			
		}else {
            $ssql.= " AND ".stripslashes($option)." LIKE '%".$generalobjAdmin->clean($keyword)."%'";
			
		}
    }else {
        $ssql.= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR c.vCompany LIKE '%".$generalobjAdmin->clean($keyword)."%' OR rd.vEmail LIKE '%".$generalobjAdmin->clean($keyword)."%' OR rd.vPhone LIKE '%".$generalobjAdmin->clean($keyword)."%' OR rd.eStatus LIKE '%".$generalobjAdmin->clean($keyword)."%')";
		
	}
}

if($iCompanyId != "") {
	$ssql .= " AND rd.iCompanyId='".$iCompanyId."'";
}

// End Search Parameters

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(iPreferenceId) AS Total FROM preferences 
		WHERE 1=1 $ssql $dri_ssql";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page);//total pages we going to have
$show_page = 1;

//-------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             //it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    } else {
        // error - show first set of results
        $start = 0;
        $end = $per_page;
    }
} else {
    // if page isn't set, show first set of results
    $start = 0;
    $end = $per_page;
}
// display pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$tpages=$total_pages;
if ($page <= 0)
    $page = 1;
//Pagination End

$sql = "select iPreferenceId from preferences WHERE preferences='".$data_preferences[$i]['iPreferenceId']."' AND eStatus!='Deleted'";
$dbVehicle = $obj->MySQLSelect($sql);

 // $sql = "select * from preferences where 1=1 $ssql $dri_ssql $ord LIMIT $start, $per_page";
 $sql = "select * from preferences where 1=1 LIMIT $start, $per_page";
		// die;

$data_preferences = $obj->MySQLSelect($sql);

$endRecord = count($data_preferences);
$var_filter = "";
foreach ($_REQUEST as $key=>$val)
{
    if($key != "tpages" && $key != 'page')
    $var_filter.= "&$key=".stripslashes($val);
}

$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages.$var_filter;
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME?> | Preferences</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once('global_files.php');?>
    </head>
    <!-- END  HEAD-->
    
    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- Main LOading -->
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php include_once('header.php'); ?>
            <?php include_once('left_menu.php'); ?>

            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div id="add-hide-show-div">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2>Preferences</h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php include('valid_msg.php'); ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
						<input type="hidden" name="iDriverId" value="<?php echo $iDriverId; ?>" >
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                              <tbody>
                                <tr>
                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>
                                    <td width="10%" class=" padding-right10"><select name="option" id="option" class="form-control">
                                          <option value="">All</option>
										  <option  value="concat(rd.vName,' ',rd.vLastName") <?php if ($option == "concat(rd.vName,'',rd.vLastName)") { echo "selected"; } ?> >Driver Name</option>
                                          <option  value="c.vCompany" <?php if ($option == "c.vCompany") { echo "selected"; } ?> >Company Name</option>
                                          <option value="rd.vEmail" <?php if ($option == 'rd.vEmail') {echo "selected"; } ?> >E-mail</option>
                                          <option value="rd.vPhone" <?php if ($option == 'rd.vPhone') {echo "selected"; } ?> >Mobile</option>
                                          <option value="rd.eStatus" <?php if ($option == 'rd.eStatus') {echo "selected"; } ?> >Status</option>
										  
                                    </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='driver.php'"/>
                                    </td>
                                    <td width="30%"><a class="add-btn" href="driver_action.php" style="text-align: center;">Add Preferences</a></td>
                                </tr>
                              </tbody>
                        </table>
                        
                      </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="admin-nir-export">
                                    <div class="changeStatus col-lg-12 option-box-left">
                                    <span class="col-lg-2 new-select001">
                                            <select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">
                                                    <option value="" >Select Action</option>
                                                    <option value='Active' <?php if ($option == 'Active') { echo "selected"; } ?> >Make Active</option>
                                                    <option value="Inactive" <?php if ($option == 'Inactive') {echo "selected"; } ?> >Make Inactive</option>
                                                    <option value="Deleted" <?php if ($option == 'Delete') {echo "selected"; } ?> >Make Delete</option>
                                            </select>
                                    </span>
                                    </div>
                                    <div class="panel-heading">
                                        <form name="_export_form" id="_export_form" method="post" >
                                            <button type="button" onclick="showExportTypes('driver')" >Export</button>
                                        </form>
                                   </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class='align-center' width="3%" class="align-center"><input type="checkbox" id="setAllCheck" ></th>
                                                        <th width="13%"><a href="javascript:void(0);" onClick="Redirect(1,<?php if($sortby == '1'){ echo $order; }else { ?>0<?php } ?>)">Name <?php if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
														
														<th width="18%"><a href="javascript:void(0);" onClick="Redirect(2,<?php if($sortby == '2'){ echo $order; }else { ?>0<?php } ?>)">Interested Image <?php if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
														
														<th width="18%"><a href="javascript:void(0);" onClick="Redirect(3,<?php if($sortby == '3'){ echo $order; }else { ?>0<?php } ?>)">Not Interested Image <?php if ($sortby == 3) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
														
														<th width="12%" class="align-center"><a href="javascript:void(0);" onClick="Redirect(6,<?php if($sortby == '6'){ echo $order; }else { ?>0<?php } ?>)">Status <?php if ($sortby == 6) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
														
														<th width="8%" class="align-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <? if(!empty($data_preferences)) {
													for ($i = 0; $i < count($data_preferences); $i++) { ?>
														<tr class="gradeA" >
														 <td align="center"><input type="checkbox" id="checkbox" name="checkbox[]" <?php echo $default; ?> value="<?php echo $data_preferences[$i]['iPreferenceId']; ?>" />&nbsp;</td>
															<td><?= $generalobjAdmin->clearName($data_preferences[$i]['vName']); ?></td>
															<td><?=$data_preferences[$i]['vPreferenceImage_YES']; ?></td>
															<td><?=$data_preferences[$i]['vPreferenceImage_NO'];?></td>
														  
															<td align="center">
																	<? if($data_preferences[$i]['eStatus'] == 'Active') {
																			$dis_img = "img/active-icon.png";
																		}else if($data_preferences[$i]['eStatus'] == 'Inactive'){
																			 $dis_img = "img/inactive-icon.png";
																		}else if($data_preferences[$i]['eStatus'] == 'Deleted'){
																			$dis_img = "img/delete-icon.png";
																		}?>
																	<img src="<?=$dis_img;?>" alt="image" data-toggle="tooltip" title="<?php echo $data_preferences[$i]['eStatus']; ?>">
															</td>
															
															<td align="center" class="action-btn001">
                                                                <div class="share-button openHoverAction-class" style="display: block;">
                                                                    <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                                    <div class="social show-moreOptions for-five openPops_<?= $data_preferences[$i]['iDriverId']; ?>">
                                                                        <ul>
                                                                            <li class="entypo-twitter" data-network="twitter"><a href="driver_action.php?id=<?=$data_preferences[$i]['iDriverId']; ?>" data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a></li>
                                                                            <?php if ($data_preferences[$i]['eDefault'] != 'Yes') { ?>
                                                                            <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onClick="changeStatus('<?php echo $data_preferences[$i]['iDriverId']; ?>','Inactive')"  data-toggle="tooltip" title="Make Active">
                                                                                <img src="img/active-icon.png" alt="<?php echo $data_preferences[$i]['eStatus']; ?>" >
                                                                            </a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="changeStatus('<?php echo $data_preferences[$i]['iDriverId']; ?>','Active')" data-toggle="tooltip" title="Make Inactive">
                                                                                <img src="img/inactive-icon.png" alt="<?php echo $data_preferences[$i]['eStatus']; ?>" >	
                                                                            </a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="changeStatusDelete('<?php echo $data_preferences[$i]['iDriverId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png" alt="Delete" >
                                                                            </a></li>
																		
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </td>
															
														</tr>
													<?php } }else { ?>
                                                        <tr class="gradeA">
                                                            <td colspan="10"> No Records Found.</td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </form>
                                            <?php include('pagination_n.php'); ?>
                                    </div>
                                </div> <!--TABLE-END-->
                            </div>
                        </div>
                    <div class="admin-notes">
					<h4>Notes:</h4>
                           <ul>
                                    <li>
                                            Driver module will list all drivers on this page.
                                    </li>
                                    <li>
                                            Administrator can Activate / Deactivate / Delete any driver. 
                                    </li>
                                    <li>
                                            Administrator can export data in XLS or PDF format.
                                    </li>
                                    <!--li>
                                            "Export by Search Data" will export only search result data in XLS or PDF format.
                                    </li-->
                            </ul> 
                    </div>
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
            <!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/driver.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
<input type="hidden" name="iDriverId" id="iMainId01" value="" >
<input type="hidden" name="iCompanyId" id="iCompanyId" value="<?php echo $iCompanyId; ?>" >
<input type="hidden" name="status" id="status01" value="" >
<input type="hidden" name="statusVal" id="statusVal" value="" >
<input type="hidden" name="option" value="<?php echo $option; ?>" >
<input type="hidden" name="keyword" value="<?php echo $keyword; ?>" >
<input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >
<input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >
<input type="hidden" name="method" id="method" value="" >
</form>
    <?php
    include_once('footer.php');
    ?>
        <script>
            
            $("#setAllCheck").on('click',function(){
                if($(this).prop("checked")) {
                    jQuery("#_list_form input[type=checkbox]").each(function() {
                        if($(this).attr('disabled') != 'disabled'){
                            this.checked = 'true';
                        }
                    });
                }else {
                    jQuery("#_list_form input[type=checkbox]").each(function() {
                        this.checked = '';
                    });
                }
            });
            
            $("#Search").on('click', function(){
                //$('html').addClass('loading');
                var action = $("#_list_form").attr('action');
               // alert(action);
                var formValus = $("#frmsearch").serialize();
//                alert(action+formValus);
                window.location.href = action+"?"+formValus;
            });
            
            $('.entypo-export').click(function(e){
                 e.stopPropagation();
                 var $this = $(this).parent().find('div');
                 $(".openHoverAction-class div").not($this).removeClass('active');
                 $this.toggleClass('active');
            });
            
            $(document).on("click", function(e) {
                if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {
                  $(".show-moreOptions").removeClass("active");
                }
            });
            
        </script>
    </body>
    <!-- END BODY-->
</html>