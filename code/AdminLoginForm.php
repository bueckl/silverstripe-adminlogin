<?php

/**
 * Class AdminLoginForm
 */
class AdminLoginForm extends MemberLoginForm
{

	public function __construct($controller, $name, $fields = null, $actions = null, $checkCurrentUser = true) {
		
		parent::__construct($controller, $name, $fields, $actions, $checkCurrentUser);
		

		// Force to Accept Terms
		if ( Cookie::get('showTerms') ) { 

			$this->Fields()->push( LiteralField::create('Hint',
				'<hr><strong>Bei der ersten Anmeldung im System akzeptieren Sie bitte unsere allgemeinen Nutzungsbedingungen!</strong>'
			));
		
			$TermsAccepted = CheckboxField::create('TermsAccepted');
			$TermsAccepted ->setTitle('Ich habe die <a data-modal-url="'.Controller::curr()->Link().'showTermsModal" class="fire-terms-modal">Nutzungsbedingungen</a> gelesen und akzeptiert.');
			
			$this->Fields()->push( $TermsAccepted );
			
		} else {
			$TermsAccepted = LiteralField::create("TermsAccepted", '<div><hr><a data-modal-url="'.Controller::curr()->Link().'showTermsModal" class="fire-terms-modal">Bitte lesen Sie unsere allgemeinen Nutzungsbedingungen</a>.</div>');
			
			$this->Actions()->push( $TermsAccepted );
			
		}
			
		
		if ($this->Actions()->fieldByName('forgotPassword')) {
			// replaceField won't work, since it's a dataless field
			$this->Actions()->removeByName('forgotPassword');
			$this->Actions()->push(new LiteralField(
				'forgotPassword',
				'<a href="AdminSecurity/lostpassword">'
				. _t('Member.BUTTONLOSTPASSWORD', "I've lost my password") . '</a>'
			));
		}
		
		
		Requirements::customScript(<<<JS
			(function() {
				var el = document.getElementById("AdminLoginForm_LoginForm_Email");
				if(el && el.focus) el.focus();
			})();
JS
        );
    }

    /**
     * @param array $data
     * @return SS_HTTPResponse
     */
    public function forgotPassword($data)
    {
        if($data['Email']) {
            /* @var $member Member */
            if ($member = Member::get()->where("Email = '".Convert::raw2sql($data['Email'])."'")->first()) {
                $token = $member->generateAutologinTokenAndStoreHash();
                $this->sendPasswordResetLinkEmail($member, $token);
            }

            return $this->controller->redirect('AdminSecurity/passwordsent/' . urlencode($data['Email']));
        }

        $this->sessionMessage(
            _t('Member.ENTEREMAIL', 'Please enter an email address to get a password reset link.'),
            'bad'
        );

        return $this->controller->redirect('AdminSecurity/lostpassword');
    }

    /**
     * @param Member $member
     * @param string $token
     */
    protected function sendPasswordResetLinkEmail($member, $token)
    {
        /* @var $email Member_ForgotPasswordEmail */
        $email = Member_ForgotPasswordEmail::create();
        $email->populateTemplate($member);
        $email->populateTemplate(array(
            'PasswordResetLink' => AdminSecurity::getPasswordResetLink($member, $token)
        ));
        $email->setTo($member->Email);
        $email->send();
    }

	/**
	 * Login form handler method
	 *
	 * This method is called when the user clicks on "Log in"
	 *
	 * @param array $data Submitted data
	 */
	public function dologin($data) {
		
		
		if($this->performLogin($data)) {
			
			if ($data['TermsAccepted'] == 1) {
				$member = Member::currentUser();
				$member->TermsAccepted = 1;
				$member->write();
				Cookie::set('showTerms', false);
			}
			
			if( Member::currentUser()->TermsAccepted == 0 ) {
				$member = Member::currentUser();
				$member->logout();
				// We show AcceptTerms fields on Construct
				Cookie::set('showTerms', true);
				return Controller::curr()->redirectBack();
			}
			
			$this->logInUserAndRedirect($data);

		} else {
			
			
			if(array_key_exists('Email', $data)){
				Session::set('SessionForms.MemberLoginForm.Email', $data['Email']);
				Session::set('SessionForms.MemberLoginForm.Remember', isset($data['Remember']));
			}

			if(isset($_REQUEST['BackURL'])) $backURL = $_REQUEST['BackURL'];
			else $backURL = null;

			if($backURL) Session::set('BackURL', $backURL);

			// Show the right tab on failed login
			$loginLink = Director::absoluteURL($this->controller->Link('login'));
			if($backURL) $loginLink .= '?BackURL=' . urlencode($backURL);
			$this->controller->redirect($loginLink . '#' . $this->FormName() .'_tab');
		}
	}



}
