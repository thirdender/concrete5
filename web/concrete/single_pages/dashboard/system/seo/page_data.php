<?php  defined('C5_EXECUTE') or die('Access Denied');

$form = Loader::helper('form');
$ih = Loader::helper('concrete/interface');
$txt = Loader::helper('text');
?>

<?php echo Loader::helper('concrete/dashboard') -> getDashboardPaneHeaderWrapper(t('Batch SEO'), t('Do All Your SEO-ing in One Place.'), false, false); ?>
<div class="ccm-pane-body">
<?php 
$keywords = $searchRequest['keywords'];
/*
$soargs = array();
$soargs['searchInstance'] = $searchInstance;
$soargs['sitemap_select_mode'] = $sitemap_select_mode;
$soargs['sitemap_select_callback'] = $sitemap_select_callback;
$soargs['searchDialog'] = $searchDialog;
*/
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
			$stripe = ($i % 2?'stripe':'');
			?>
			<div class="ccm-results-list">
				<div class="rowHolder <?php echo $stripe; ?> ccm-seoRow-<?php echo $cID; ?>" style="float: left;">
					<form id="seoForm<?php echo $cID; ?>" action="<?php echo View::url('/dashboard/batch_seo/', 'saveRecord')?>" method="post" class="pageForm">
						<div class="headings">
							<?php echo $form->hidden('cID', $cID) ?>
								<strong><?php echo t('Page Name'); ?></strong>
								<br />
								<br />
								<?php echo $cobj -> getCollectionName() ? $cobj -> getCollectionName() : ''; ?>
							</div>
							
							<div class="headings">
								<strong><?php echo t('Page Type'); ?></strong>
								<br />
								<br />
								<?php echo $cobj -> getCollectionTypeName() ? $cobj -> getCollectionTypeName() : 'Single Page'; ?>
							</div>
							
							<div class="headings"><strong><?php echo t('Modified'); ?></strong>
							<br />
							<br />
							<?php echo $cobj -> getCollectionDateLastModified() ? $cobj -> getCollectionDateLastModified() : ''; ?>
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
							<?php echo $form -> text('meta_title', $cobj -> getAttribute('meta_title'), array('title' => $cID)); ?>
							</div>
							
							<div><strong><?php echo t('Meta Description'); ?></strong>
							<br />
							<br />
							<?php echo $form -> textarea('meta_description', $cobj -> getAttribute('meta_description'), array('title' => $cID)); ?>
							</div>
							
							<div><strong><?php echo t('Meta Keywords'); ?></strong>
							<br />
							<br />
							<?php echo $form -> textarea('meta_keywords', $cobj -> getAttribute('meta_keywords'), array('title' => $cID)); ?>
							</div>
							
							<div><strong><?php echo t('Slug'); ?></strong>
							<br />
							<br />
							<?php echo $form -> text('collection_handle', $cobj -> getCollectionHandle(), array('title' => $cID, 'class' => 'collectionHandle')); ?>
							</div>
							
							<div class="updateButton">
							<br />
							<br />
							<?php print $ih -> submit('Save', $formID, $buttonAlign = 'right', 'seoSubmit', array('title' => $cID)); ?>
							</div>
							<div>
							<img style="float: left; display: none;" id="throbber<?php echo $cID ?>"  class="throbber<?php echo $cID ?>" src="<?php echo ASSETS_URL_IMAGES . '/throbber_white_32.gif' ?>" />
							</div>
							</form>
				</div>
				<div style="clear: left"></div>	
		<?php } ?>
	
	<?php } else { ?>
		<div class="ccm-results-list-none"><?php echo t('No pages found.')?></div>
	<?php  }
	print $ih->button('Update All', '#', $buttonAlign='right', $innerClass=null, $args = array('id'=>'allSeoSubmit'));
 	?>
	<div style="clear: left"></div>
	<script type="text/javascript">
	$(document).ready(function() {
		var options = { 
			url: '<?php echo $this->action("saveRecord") ?>',
			dataType: 'json',
			success:function(res) {
				//console.log(res);
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
	});
	</script>
	<?php $pageList -> displaySummary(); ?>
	</div>
	<div class="ccm-pane-footer">
		<?php $pageList->displayPagingV2(); ?>
	</div>
<?php echo Loader::helper('concrete/dashboard') -> getDashboardPaneFooterWrapper(false); ?>