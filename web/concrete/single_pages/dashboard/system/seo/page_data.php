<?php  defined('C5_EXECUTE') or die('Access Denied');?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Bulk SEO'), t('Do All Your SEO-ing in One Place.'), false, false); 
$pageSelector = Loader::helper('form/page_selector');
?>
<style type="text/css">
		.rowHolder {
			width: 100%;
		}
	
		.rowHolder div {
			float: left;
			margin: 20px;
			min-height: 100px;
		}
		
		.rowHolder div.metaInput {
			float: none;
			margin: 0px;
			min-height: 0px;
		}
		.rowHolder div.headings {
			min-height: 50px;
		}
	
		.rowHolder.stripe {
			background: #eee;
		}
		
		.rowHolder.stripe .help-inline {
			color: #999;
		}
		
	</style>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#searchUnderParent').click(function(){
			$('#parentOptions').slideDown();
		});
	});
	</script>
<form action="<?=$this->action('view')?>">
	<div class="ccm-pane-options">
		<fieldset>
			<?php echo $form->text('keywords'); ?>
			<?php echo $form->checkbox('noTitle', 1, $titleCheck, array('style' => 'margin-left: 15px;'));  ?><span><?=t(' No Meta Title'); ?></span>
			<?php echo $form->checkbox('noDescription', 1, $descCheck, array('style' => 'margin-left: 15px;'));  ?><span><?=t(' No Meta Description'); ?></span>
			<?=$form->select('numResults', array(
				'10' => '10',
				'25' => '25',
				'50' => '50',
				'100' => '100',
				'500' => '500'
			), $searchRequest['numResults'], array('style' => 'width:65px; margin: 0px 10px 0px 15px;'))?><span><?=t(' # Per Page'); ?></span>
			<?php print $concrete_interface->submit('Search', $formID, $buttonAlign = 'right', 'searchSubmit'); ?><br />
			<a style="display: block; margin-top: 15px;" id="searchUnderParent" href="#"><?php echo t('Search Under Parent'); ?></a>
			<div id="parentOptions" style="display: <?php echo $parentDialogOpen ? 'block' : 'none'; ?>">
				<?php print $pageSelector->selectPage('cParentIDSearchField', 'ccm_selectSitemapNode');?>
				<span class="ccm-search-option" search-field="parent">
				<br/><strong><?=t('Search All Children?')?></strong><br/>
				<ul class="inputs-list">
					<li><label><?=$form->radio('cParentAll', 0, false)?> <span><?=t('No')?></span></label></li>
					<li><label><?=$form->radio('cParentAll', 1, false)?> <span><?=t('Yes')?></span></label></li>
				</ul>
			</div>
		</fieldset>	
	</div>
</form>

<div class="ccm-pane-body">
<?php
if (count($pages) > 0) {
	  $i = 0;
		foreach($pages as $cobj) {
			$cpobj = new Permissions($cobj);
			$i++;
			$cID = $cobj->getCollectionID();
			$stripe = ($i % 2?'stripe':'');
			?>
			<div class="ccm-results-list">
				<div class="rowHolder <?php echo $stripe; ?> ccm-seoRow-<?php echo $cID; ?>" style="float: left;">
					<form id="seoForm<?php echo $cID; ?>" action="<?php echo View::url('/dashboard/system/seo/page_data/', 'saveRecord')?>" method="post" class="pageForm">
						<div class="headings">
							<?php echo $form->hidden('cID', $cID) ?>
							<strong><?php echo t('Page Name'); ?></strong>
							<br />
							<br />
							<?php echo $cobj -> getCollectionName() ? $cobj->getCollectionName() : ''; ?>
						</div>
							
						<div class="headings">
							<strong><?php echo t('Page Type'); ?></strong>
							<br />
							<br />
							<?php echo $cobj->getCollectionTypeName() ? $cobj->getCollectionTypeName() : t('Single Page'); ?>
						</div>
							
						<div class="headings"><strong><?php echo t('Modified'); ?></strong>
							<br />
							<br />
							<?php echo $cobj->getCollectionDateLastModified() ? $cobj->getCollectionDateLastModified() : ''; ?>
						</div>
							
						<div class="headings"><strong><?php echo t('Path'); ?></strong>
							<br />
							<br />
							<?php
							Page::rescanCollectionPath($cID);
							$path = Page::getCollectionPathFromID($cID);
							$tokens = explode('/', $path);
							$lastkey = array_pop(array_keys($tokens)) - 1;
							$tokens[$lastkey] = '<strong class="collectionPath">' . $tokens[$lastkey] . '</strong>';
							$untokens = implode('/', $tokens);
							echo $untokens;
							?>
						</div>
							
						<div style="clear: left;"><strong><?php echo t('Meta Title'); ?></strong>
						<br />
						<br />
							<div class="metaInput">
								<?php $pageTitle = $cobj->getCollectionName();
								$pageTitle = htmlspecialchars($pageTitle, ENT_COMPAT, APP_CHARSET);
								$autoTitle = sprintf(PAGE_TITLE_FORMAT, SITE, $pageTitle);
								$titleInfo = array('title' => $cID);
								if(strlen($cobj->getAttribute('meta_title')) <= 0) {
									 $titleInfo[disabled] = 'disabled'; 
								}
								echo $form->text('meta_title', $cobj->getAttribute('meta_title') ? $cobj->getAttribute('meta_title') : $autoTitle, $titleInfo); 
								echo $titleInfo[disabled] ? '<br /><span class="help-inline">' . t('Default value. Click to edit.') . '</span>' : '' ?>
							</div>
						</div>
							
						
						<div><strong><?php echo t('Meta Description'); ?></strong>
						<br />
						<br />
							<div class="metaInput">
								<?php $pageDescription = $cobj->getCollectionDescription();
								$autoDesc = htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET);
								$descInfo = array('title' => $cID); 
								if(strlen($cobj -> getAttribute('meta_description')) <= 0) {
									$descInfo[disabled] = 'disabled'; 
								}
								echo $form->textarea('meta_description', $cobj->getAttribute('meta_description') ? $cobj->getAttribute('meta_description') : $autoDesc, $descInfo); 
								echo $descInfo[disabled] ? '<br /><span class="help-inline">' . t('Default value. Click to edit.') . '</span>' : '';
								 ?>
							</div>
						</div>
							
						<div>
							<strong><?php echo t('Meta Keywords'); ?></strong>
							<br />
							<br />
							<?php echo $form->textarea('meta_keywords', $cobj->getAttribute('meta_keywords'), array('title' => $cID)); ?>
						</div>
							
						<div>
							<strong><?php echo t('Slug'); ?></strong>
							<br />
							<br />
							<?php echo $form->text('collection_handle', $cobj->getCollectionHandle(), array('title' => $cID, 'class' => 'collectionHandle')); ?>
						</div>
							
						<div class="updateButton">
							<br />
							<br />
							<?php print $concrete_interface->submit('Save', $formID, $buttonAlign = 'right', 'seoSubmit', array('title' => $cID)); ?>
						</div>
						<div>
							<img style="float: left; display: none;" id="throbber<?php echo $cID ?>"  class="throbber<?php echo $cID ?>" src="<?php echo ASSETS_URL_IMAGES . '/throbber_white_32.gif' ?>" />
						</div>
					</form>
				</div>
			</div>
			<div style="clear: left"></div>	
		<?php } ?>
	<?php } else { ?>
		<div class="ccm-results-list-none"><?php echo t('No pages found.')?></div>
	<?php  }
	print $concrete_interface->button(t('Update All'), '#', $buttonAlign='right', $innerClass=null, $args = array('id'=>'allSeoSubmit'));
 	?>
	<div style="clear: left;"></div>
	<script type="text/javascript">
	$(document).ready(function() {
		var options = { 
			url: '<?php echo $this->action("saveRecord") ?>',
			dataType: 'json',
			success:function(res) {
				if(res.success) {
					var cID = res.cID;
					$('.throbber'+cID).hide();
					$('.ccm-seoRow-'+cID).animate({"background-color" : "#57A957" }, 500);
					$('.ccm-seoRow-'+cID).children('.pageForm').children('.updateButton').children('.seoSubmit').removeClass('success');
					$('.ccm-seoRow-'+cID+' .collectionPath').html(res.newPath);
					$('.ccm-seoRow-'+cID+' .collectionHandle').val(res.cHandle);
					if ($('.ccm-seoRow-'+cID).hasClass('stripe')) {
						$('.ccm-seoRow-'+cID).animate({"background-color" : "#eee" }, 500);
					} else {
						$('.ccm-seoRow-'+cID).animate({"background-color" : "#ffff" }, 500);
					}
				} else {
					alert('An error occured while saving.');
				}
			}
		};
		
		$('.rowHolder input[type="text"], .rowHolder textarea' ).change(function() { 
			var identifier =  $(this).attr('title');
			$('.seoSubmit[title= ' + identifier + ']').addClass('success').addClass('valueChanged'); 
		});
		
		$('.seoSubmit').click(function() { 
			var iterator = $(this).attr('title'); 
			$('#seoForm' + iterator).ajaxForm(options); 
			$('#throbber'+iterator).show();
		});
		
		$('#allSeoSubmit').click(function() {
			$('.valueChanged').click();
		});
		
		$('.metaInput').click(function(){
			$(this).children().removeAttr('disabled');
			$(this).children('.help-inline').hide();
		})
	});
	</script>
	<?php $pageList->displaySummary(); ?>
</div>
<div class="ccm-pane-footer">
	<?php $pageList->displayPagingV2(); ?>
</div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>