<?php  defined('C5_EXECUTE') or die('Access Denied'); 


$ih = Loader::helper('concrete/interface'); 
?>


<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Batch SEO'), t('Do All Your SEO-ing in One Place.'), false, false);?>
<?php 

/* if ($_REQUEST['searchDialog'] == 1) {
	$searchDialog = true;
}

if (!isset($sitemap_select_mode)) {
	if (isset($_REQUEST['sitemap_select_mode'])) {
		$sitemap_select_mode = $_REQUEST['sitemap_select_mode'];
	}
}

if (!isset($sitemap_select_callback)) {
	if (isset($_REQUEST['sitemap_select_callback'])) {
		$sitemap_select_callback = $_REQUEST['sitemap_select_callback'];
	}
}

*/
if (isset($_REQUEST['searchInstance'])) {
	$searchInstance = $_REQUEST['searchInstance'];
}


?>

<?php 
$dh = Loader::helper('concrete/dashboard/sitemap');
if ($dh->canRead()) { ?>

<div class="ccm-pane-options" id="ccm-<?php echo $searchInstance?>-pane-options">

<?php 

$searchFields = array(
	'' => '** ' . t('Fields'),
	'keywords' => t('Full Page Index'),
	'date_added' => t('Date Added'),
	'last_modified' => t('Last Modified'),
	'date_public' => t('Public Date'),
	'owner' => t('Page Owner'),
	'num_children' => t('# Children'),
	'version_status' => t('Approved Version')
);

$searchFields['parent'] = t('Parent Page');

Loader::model('attribute/categories/collection');
$searchFieldAttributes = CollectionAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayHandle();
}

?>

<?php  $form = Loader::helper('form'); ?>
	<div id="ccm-<?php echo $searchInstance?>-search-field-base-elements" style="display: none">

		<span class="ccm-search-option"  search-field="keywords">
			<?php echo $form->text('keywords', $searchRequest['keywords'], array('style' => 'width: 120px'))?>
		</span>

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_public">
			<?php echo $form->text('date_public_from', array('style' => 'width: 86px'))?>
			<?php echo t('to')?>
			<?php echo $form->text('date_public_to', array('style' => 'width: 86px'))?>
		</span>
	
		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
			<?php echo $form->text('date_added_from', array('style' => 'width: 86px'))?>
			<?php echo t('to')?>
			<?php echo $form->text('date_added_to', array('style' => 'width: 86px'))?>
		</span>
	
		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="last_modified">
			<?php echo $form->text('last_modified_from', array('style' => 'width: 86px'))?>
			<?php echo t('to')?>
			<?php echo $form->text('last_modified_to', array('style' => 'width: 86px'))?>
		</span>
	
		<span class="ccm-search-option"  search-field="owner">
			<?php echo $form->text('owner', array('class'=>'span5'))?>
		</span>
	
		<span class="ccm-search-option"  search-field="version_status">
			<ul class="inputs-list">
				<li><label><?php echo $form->radio('cvIsApproved', 0, false)?> <span><?php echo t('Unapproved')?></label></li>
				<li><label><?php echo $form->radio('cvIsApproved', 1, false)?> <span><?php echo t('Approved')?></span></label></li>
			</ul>
		</span>
		
		<span class="ccm-search-option" search-field="parent">

		<?php  $ps = Loader::helper("form/page_selector");
		print $ps->selectPage('cParentIDSearchField');
		?>
	
		<br/><strong>
			<?php echo t('Search All Children?')?>
		</strong><br/>
		<ul class="inputs-list">
			<li><label><?php echo $form->radio('cParentAll', 0, false)?> <span><?php echo t('No')?></label></li>
			<li><label><?php echo $form->radio('cParentAll', 1, false)?> <span><?php echo t('Yes')?></span></label></li>
		</ul>

		</span>
		<span class="ccm-search-option"  search-field="num_children">
			<select name="cChildrenSelect">
				<option value="gt"<?php  if ($req['cChildrenSelect'] == 'gt') { ?> selected <?php  } ?>><?php echo t('More Than')?></option>
				<option value="eq" <?php  if ($req['cChildrenSelect'] == 'eq') { ?> selected <?php  } ?>><?php echo t('Equal To')?></option>
				<option value="lt"<?php  if ($req['cChildrenSelect'] == 'lt') { ?> selected <?php  } ?>><?php echo t('Fewer Than')?></option>
			</select>
			<input type="text" name="cChildren" value="<?php echo $req['cChildren']?>" />
		</span>
	
		<?php  foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<?php  } ?>
	</div>

	<form method="get" action="<?php echo $this->action('view'); ?>">

		<input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />

			<div class="ccm-pane-options-permanent-search">

			<input type="hidden" name="submit_search" value="1" />
			<?php 	
			print $form->hidden('ccm_order_dir', $searchRequest['ccm_order_dir']); 
			print $form->hidden('ccm_order_by', $searchRequest['ccm_order_by']); 
			if ($searchDialog) {
				print $form->hidden('searchDialog', true);
			}
			if ($sitemap_select_mode) {
			print $form->hidden('sitemap_select_mode', $sitemap_select_mode);
			}
			if ($sitemap_select_callback) {
				print $form->hidden('sitemap_select_callback', $sitemap_select_callback);
			}
			if ($sitemap_display_mode) {
				print $form->hidden('sitemap_display_mode', $sitemap_display_mode);
			}
			?>

			<div class="span4">
				<?php echo $form->label('cvName', t('Page Name'))?>
				<div class="input">
					<?php echo $form->text('cvName', $searchRequest['cvName'], array('style'=> 'width: 120px')); ?>
				</div>
			</div>

			<div class="span4">
				<?php echo $form->label('ctID', t('Page Type'))?>
				<div class="input">
					<?php  
					Loader::model('collection_types');
					$ctl = CollectionType::getList();
					$ctypes = array('' => t('** All'));
					foreach($ctl as $ct) {
						$ctypes[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();
					}
					
					print $form->select('ctID', $ctypes, $searchRequest['ctID'], array('style' => 'width:120px'))?>
				</div>
			</div>

			<div class="span5">
				<?php echo $form->label('numResults', t('# Per Page'))?>
				<div class="input">
					<?php echo $form->select('numResults', array(
						'10' => '10',
						'25' => '25',
						'50' => '50',
						'100' => '100',
						'500' => '500'
					), $searchRequest['numResults'], array('style' => 'width:65px'))?>
				</div>
				<?php echo $form->submit('ccm-search-pages', t('Search'), array('style' => 'margin-left: 10px'))?>
				<img src="<?php echo ASSETS_URL_IMAGES?>/loader_intelligent_search.gif" width="43" height="11" class="ccm-search-loading" id="ccm-<?php echo $searchInstance?>-search-loading" />
			</div>
		</div>
	
		<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-<?php  if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>open<?php  } else { ?>closed<?php  } ?>"><?php echo t('Advanced Search')?></a>
		<div class="clearfix ccm-pane-options-content" <?php  if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>style="display: block" <?php  } ?>>
			<br/>
			<table class="zebra-striped ccm-search-advanced-fields" id="ccm-<?php echo $searchInstance?>-search-advanced-fields">
			<tr>
				<th colspan="2" width="100%"><?php echo t('Additional Filters')?></th>
				<th style="text-align: right; white-space: nowrap"><a href="javascript:void(0)" id="ccm-<?php echo $searchInstance?>-search-add-option" class="ccm-advanced-search-add-field"><span class="ccm-menu-icon ccm-icon-view"></span><?php echo t('Add')?></a></th>
			</tr>
			<tr id="ccm-search-field-base">
				<td><?php echo $form->select('searchField', $searchFields);?></td>
				<td width="100%">
					<input type="hidden" value="" class="ccm-<?php echo $searchInstance?>-selected-field" name="selectedSearchField[]" />
					<div class="ccm-selected-field-content">
						<?php echo t('Select Search Field.')?>				
					</div>
				</td>
				<td>
					<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
				</td>
			</tr>
			<?php  
			$i = 1;
			if (is_array($searchRequest['selectedSearchField'])) { 
				foreach($searchRequest['selectedSearchField'] as $req) { 
					if ($req == '') {
						continue;
					}
					?>
					
			<tr class="ccm-search-field ccm-search-request-field-set" ccm-search-type="<?php echo $req?>" id="ccm-<?php echo $searchInstance?>-search-field-set<?php echo $i?>">
				<td><?php echo $form->select('searchField' . $i, $searchFields, $req); ?></td>
				<td width="100%"><input type="hidden" value="<?php echo $req?>" class="ccm-<?php echo $searchInstance?>-selected-field" name="selectedSearchField[]" />
				<div class="ccm-selected-field-content">
					<?php  if ($req == 'date_public') { ?>
						<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_public">
						<?php echo $form->text('date_public_from', $searchRequest['date_public_from'], array('style' => 'width: 86px'))?>
						<?php echo t('to')?>
						<?php echo $form->text('date_public_to', $searchRequest['date_public_to'], array('style' => 'width: 86px'))?>
						</span>
					<?php  } ?>
	
					<?php  if ($req == 'keywords') { ?>
						<span class="ccm-search-option"  search-field="keywords">
							<?php echo $form->text('keywords', $searchRequest['keywords'], array('style' => 'width: 120px'))?>
						</span>
					<?php  } ?>
	
					<?php  if ($req == 'date_added') { ?>
						<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
							<?php echo $form->text('date_added_from', $searchRequest['date_added_from'], array('style' => 'width: 86px'))?>
							<?php echo t('to')?>
							<?php echo $form->text('date_added_to', $searchRequest['date_added_to'], array('style' => 'width: 86px'))?>
						</span>
					<?php  } ?>
	
					<?php  if ($req == 'owner') { ?>
						<span class="ccm-search-option"  search-field="owner">
							<?php echo $form->text('owner', $searchRequest['owner'], array('class' => 'span5'))?>
						</span>
					<?php  } ?>
	
					<?php  if ($req == 'num_children') { ?>
						<span class="ccm-search-option"  search-field="num_children">
							<select name="cChildrenSelect">
								<option value="gt"<?php  if ($searchRequest['cChildrenSelect'] == 'gt') { ?> selected <?php  } ?>><?php echo t('More Than')?></option>
								<option value="eq" <?php  if ($searchRequest['cChildrenSelect'] == 'eq') { ?> selected <?php  } ?>><?php echo t('Equal To')?></option>
								<option value="lt"<?php  if ($searchRequest['cChildrenSelect'] == 'lt') { ?> selected <?php  } ?>><?php echo t('Fewer Than')?></option>
							</select>
							<input type=text name="cChildren" value="<?php echo $searchRequest['cChildren']?>">
						</span>
					<?php  } ?>
	
					<?php  if ($req == 'version_status') { ?>
						<span class="ccm-search-option"  search-field="version_status">
							<ul class="inputs-list">
								<li><label><?php echo $form->radio('_cvIsApproved', 0, $searchRequest['cvIsApproved'])?> <span><?php echo t('Unapproved')?></label></li>
								<li><label><?php echo $form->radio('_cvIsApproved', 1, $searchRequest['cvIsApproved'])?> <span><?php echo t('Approved')?></span></label></li>
							</ul>
						</span>
					<?php  } ?>
							
					<?php  if ((!$searchDialog) && $req == 'parent') { ?>
						<span class="ccm-search-option" search-field="parent">
		
							<?php  $ps = Loader::helper("form/page_selector");
							print $ps->selectPage('cParentIDSearchField', $searchRequest['cParentIDSearchField']);
							?>
							
							<br/><strong>
								<?php echo t('Search All Children?')?>
							</strong><br/>
			
							<ul class="inputs-list">
								<li><label><?php echo $form->radio('_cParentAll', 0, $searchRequest['cParentAll'])?> <span><?php echo t('No')?></label></li>
								<li><label><?php echo $form->radio('_cParentAll', 1, $searchRequest['cParentAll'])?> <span><?php echo t('Yes')?></span></label></li>
							</ul>
							
						</span>
					<?php  } ?>
							
					<?php  foreach($searchFieldAttributes as $sfa) { 
						if ($sfa->getAttributeKeyID() == $req) {
							$at = $sfa->getAttributeType();
							$at->controller->setRequestArray($searchRequest);
							$at->render('search', $sfa);
						}
					} ?>					
					</div>
						</td>
						<td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td>
						</tr>
					<?php  
						$i++;
					} 
					
					} ?>
			</table>
		</div>
	</form>	

		</div>
	
<div id="ccm-<?php echo $searchInstance?>-search-results" class="ccm-file-list">

<?php  if (!$searchDialog) { ?>

<div class="ccm-pane-body">

<?php  } ?>

<div id="ccm-list-wrapper"><a name="ccm-<?php echo $searchInstance?>-list-wrapper-anchor"></a>
		<?php  $form = Loader::helper('form'); ?>

<?php 

	$txt = Loader::helper('text');
	$form = Loader::helper('form');
	$keywords = $searchRequest['keywords'];
	$soargs = array();
	$soargs['searchInstance'] = $searchInstance;
	$soargs['sitemap_select_mode'] = $sitemap_select_mode;
	$soargs['sitemap_select_callback'] = $sitemap_select_callback;
	$soargs['searchDialog'] = $searchDialog;
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/pages/search_results_seo';
	
	if (count($pages) > 0) { ?>	
				
 	<style type="text/css">
		.rowHolder {
		width: 100%;
		}
	
		.rowHolder div {
			float: left;
			margin: 20px;
			min-height: 75px;
		}
		
		.rowHolder div.headings {
			min-height: 50px;
		}
		
		.rowHolder.stripe {
			background: #eee;
		}
	</style>
			
	<?php $i = 0;
		foreach($pages as $cobj) {
			$cpobj = new Permissions($cobj);
			 $i++;
			 $cID = $cobj->getCollectionID();
	?>
			
		<?php if ($i % 2) { $stripe = 'stripe'; } else { $stripe = ''; }; ?>
		<div id="ccm-<?php echo $searchInstance?>-list" class="ccm-results-list">
			<div class="rowHolder <?php echo $stripe;?> ccm-seoRow-<?php echo $cID;  ?>" style="float: left;">
				<form id="seoForm<?php echo $cID; ?>" action="<?php echo View::url('/dashboard/batch_seo/', 'saveRecord')?>" method="post" class="pageForm">
					<div class="headings">
						<?php echo $form->hidden('cID', $cID) ?>
							<strong><?php echo t('Page Name'); ?></strong>
							<br />
							<br />
							<?php echo $cobj->getCollectionName() ? $cobj->getCollectionName() : '';?>
						</div>
						
						<div class="headings">
							<strong><?php echo t('Page Type'); ?></strong>
							<br />
							<br />
							<?php echo $cobj->getCollectionTypeName() ? $cobj->getCollectionTypeName() : 'Single Page';?>
						</div>
						
						<div class="headings"><strong><?php echo t('Modified'); ?></strong>
						<br />
						<br />
						<?php echo $cobj->getCollectionDateLastModified() ? $cobj->getCollectionDateLastModified() : '';?>
						</div>
						
						<div class="headings"><strong><?php echo t('Path'); ?></strong>
						<br />
						<br />
						<?php  
						Page::rescanCollectionPath($cID);
						$path = Page::getCollectionPathFromID($cID);
						$tokens = explode('/', $path);
						$lastkey = array_pop(array_keys($tokens)) -1; 
						$tokens[$lastkey] = '<strong class="collectionPath">' . $tokens[$lastkey] . '</strong>';
						$untokens = implode('/', $tokens);
						echo $untokens; 
						?>
						</div>
						
						<div style="clear: left;"><strong><?php echo t('Meta Title'); ?></strong>
						<br />
						<br />
						<?php echo $form->text('meta_title', $cobj->getAttribute('meta_title'), array('title' => $cID));?>
						</div>
						
						<div><strong><?php echo t('Meta Description'); ?></strong>
						<br />
						<br />
						<?php echo $form->textarea('meta_description', $cobj->getAttribute('meta_description'), array('title' => $cID));?>
						</div>
						
						<div><strong><?php echo t('Meta Keywords'); ?></strong>
						<br />
						<br />
						<?php echo $form->textarea('meta_keywords', $cobj->getAttribute('meta_keywords'), array('title' => $cID));?>
						</div>
						
						<div><strong><?php echo t('Slug'); ?></strong>
						<br />
						<br />
						<?php echo $form->text('collection_handle', $cobj->getCollectionHandle(), array('title' => $cID, 'class' => 'collectionHandle'));?>
						</div>
						
						<div class="updateButton">
						<br />
						<br />
						<?php print $ih->submit('Save', $formID, $buttonAlign='right', 'seoSubmit', array('title' => $cID)); ?>
						</div>
						<div>
						<img style="float: left; display: none;" id="throbber<?php echo $cID ?>"  class="throbber<?php echo $cID ?>" src="<?php echo ASSETS_URL_IMAGES . '/throbber_white_32.gif' ?>" />
						</div>
						</form>
				</div>
				<div style="clear: left"></div>	
				<?php } ?>
	
		<?php  } else { ?>
			
			<div class="ccm-results-list-none"><?php echo t('No pages found.')?></div>
			
		<?php  } 
		print $ih->button('Update All', '#', $buttonAlign='right', $innerClass=null, $args = array('id'=>'allSeoSubmit')) ?>
		<script type="text/javascript">
		$(document).ready(function() {
			var options = { 
				url: '<?php echo $this->action("saveRecord") ?>', 
				dataType: 'json',
				success:function(res) {
					console.log(res);
						if(res.success) {
							var cID = res.cID;
							$('.throbber'+cID).hide();
							$('.ccm-seoRow-'+cID).animate({"background-color" : "#57A957" }, 500);
							$('.ccm-seoRow-'+cID).children('.pageForm').children('.updateButton').children('.seoSubmit').removeClass('success');
							$('.ccm-seoRow-'+cID+' .collectionPath').html(res.newPath);
							$('.ccm-seoRow-'+cID+' .collectionHandle').val(res.cHandle);
							if ($('.ccm-seoRow-'+cID).hasClass('stripe')){
								$('.ccm-seoRow-'+cID).animate({"background-color" : "#eee" }, 500);
							} else {
								$('.ccm-seoRow-'+cID).animate({"background-color" : "#ffff" }, 500);
							}
							
						} else {
							alert('An error occured while saving.');
						}
					}
				};
			$('.rowHolder input[type="text"], .rowHolder textarea' ).change(function() { var identifier =  $(this).attr('title'); $('.seoSubmit[title= ' + identifier + ']').addClass('success').addClass('valueChanged'); });
			$('.seoSubmit').click(function() { var iterator = $(this).attr('title'); $('#seoForm' + iterator).ajaxForm(options); $('#throbber'+iterator).show(); });
			$('#allSeoSubmit').click(function() { 
				$('.valueChanged').click();
			});
		});		
		</script>
	</div>
</div>
<?php $pageList->displaySummary(); ?>
<div class="ccm-pane-footer">
	<?php  	$pageList->displayPagingV2(false, false, $soargs); ?>
</div>
<?php  } else { ?>
<div class="ccm-pane-body">
	<p><?php echo t("You must have access to the dashboard sitemap to search pages.")?></p>
</div>	
<?php  } ?>
</div>
</div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>