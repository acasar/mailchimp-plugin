<?php namespace Plugins\RainLab\MailChimp\Components;

use Validator;
use Modules\Cms\Classes\ComponentBase;
use October\Rain\Support\ValidationException;

class Signup extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => 'Signup Form',
            'description' => 'Sign up a new person to a mailing list.'
        ];
    }

    public function defineProperties()
    {
        return [
            'api-key' => [
                'description' => 'Get an API Key from http://admin.mailchimp.com/account/api/',
                'title' => 'MailChimp API Key',
                'type'=>'string'
            ],

            'list-id' => [
                'description' => 'In MailChimp account, select List > Tools and look for a List ID.',
                'title' => 'MailChimp List ID',
                'type' => 'string'
            ]
        ];
    }

    public function onSignup()
    {
        /*
         * Validate input
         */
        $data = post();

        $rules = [
            'email' => 'required|email|min:2|max:64',
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails())
            throw new ValidationException($validation);

        /*
         * Sign up to Mailchimp via the API
         */
        require_once(PATH_BASE . '/plugins/rainlab/mailchimp/vendor/MCAPI.class.php');

        $api = new \MCAPI($this->property('api-key'));

        $this->page['error'] = null;

        if ($api->listSubscribe($this->property('list-id'), post('email'), '') !== true)
            $this->page['error'] = $api->errorMessage;
    }

}