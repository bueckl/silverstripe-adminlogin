<?php

/**
 * Class AdminLoginPage_Controller
 * 
 * Dummy Controller to prevent loading frontend css and javscript files
 *
 */
class AdminLoginPage_Controller extends ContentController
{
	
	private static $allowed_actions = array (
		'showTermsModal'
	);	
	
	public function showTermsModal() {
		
		$data = new ArrayData(array(
			'Terms' => SiteConfig::get()->First()->BackofficeTerms
		));
		
		return $data->renderWith(array('Terms'));
		
	}
	
	public function init() {

		parent::init();
		
		setlocale(LC_TIME, Controller::curr()->Locale);
		
		$ThemeDir =  $this->ThemeDir();
		Requirements::block(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::block(THIRDPARTY_DIR . '/jquery-validate/lib/jquery.form.js');
		Requirements::block(THIRDPARTY_DIR . '/jquery-validate/jquery.validate.pack.js');
		Requirements::block('comments/javascript/CommentsInterface.js');
		Requirements::block('comments/css/comments.css');
		Requirements::block('wishlist/css/wishlist.css');
		Requirements::block('wishlist/javascript/WishListPage.js');
		
		Requirements::set_combined_files_folder($ThemeDir.'/_requirements');
		Requirements::combine_files(
			'site.css',
			array(
				$ThemeDir.'/flexslider/flexslider.css',
				$ThemeDir.'/css/bootstrap-select.css',
				$ThemeDir.'/css/bootstrap.css'
			)
		);
		
		Requirements::combine_files(
			'site.js',
			array(
				$ThemeDir.'/javascript/libs/jquery-1.10.2.min.js',
				$ThemeDir.'/javascript/bootstrap/bootstrap.min.js',
				$ThemeDir.'/javascript/EnquiryForm.js',
				$ThemeDir.'/javascript/ModalEnquiryForm.js',
				$ThemeDir.'/javascript/InlineEnquiryForm.js',
				$ThemeDir.'/javascript/RecommendationForm.js',
				$ThemeDir.'/javascript/RecommendationModalForm.js',
				$ThemeDir.'/javascript/bootstrap/bootstrap-select.js',
				$ThemeDir.'/flexslider/jquery.flexslider.js',
				$ThemeDir.'/javascript/libs/jquery.easing.js',
				$ThemeDir.'/javascript/jquery.lazyload.js',
				$ThemeDir.'/javascript/jquery.matchHeight-min.js',
				$ThemeDir.'/javascript/jquery.autocomplete.js',
				$ThemeDir.'/javascript/velocity.js',
				$ThemeDir.'/javascript/pace.min.js',
				$ThemeDir.'/bower_components/parallax.js/parallax.js',
				$ThemeDir.'/javascript/main.js'
			),
			null,
			true,
			true
		);
		
	}
}
