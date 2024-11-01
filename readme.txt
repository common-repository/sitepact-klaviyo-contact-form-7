=== Sitepact's Contact Form 7 Extension For Klaviyo ===
Contributors: ryonwhyte, sitepact
Donate link: https://paypal.me/ryonwhyte
Tags: contact form 7, klaviyo, klaviyo custom fields, newsletter, klaviyo wordpress
Requires at least: 6.2
Tested up to: 6.5.5
Stable tag: 3.0.1
Requires PHP: 7.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Integrate Contact Form 7 with Klaviyo. Automatically add form submissions to predetermined lists and fields in Klaviyo.

== Description ==

WordPress Extension for Klaviyo. Integrate Contact form 7 With Klaviyo. This plugin allows you to send form submissions into Klaviyo. This form data is added to profiles (leads, contacts, audience) in preconfigured lists. You can then use this for segmentation and other powerful marketing features within Klaviyo.
This extension allows you to send data to multiple lists and use multiple Klaviyo API keys.
This plugin will keep a log of all data sent to Klaviyo. From the log you can also resend requests.

<strong>Contact Form 7 Extension For Klaviyo Support</strong><br>
We will try our best to solve issues in the WordPress forums. However, you can also get one and one support directly at the [developer's website](https://sitepact.com/contact-form-7-klaviyo-integration/) over email or live chat (when available).

<strong>Bug Reports</strong><br>
Bug reports for the Contact Form 7 Klaviyo Extension are welcome at the WordPress developer's website.

= Key Features =

* FREE!
* Easy to use
* Support for Klaviyo regular fields
* Uses Klaviyo katest stable API
* Unlimited contact forms
* Use a different Klaviyo API Key per contact form
* Use a different Klaviyo mailing list per contact form
* Log all form submission.
* GDPR privacy checkbox â Add text and supporting privacy link
* Constantly updated

= Premium Features =

* Add Unlimited Custom Fields to Klaviyo
* Create Unsubscribe Form with Contact Form 7
* NEW Subscribe phone number for SMS marketing in Klaviyo
* Priority updates
* Feature requests
* Many more features coming soon
[Get the PRO version]( https://sitepact.com/contact-form-7-klaviyo-integration/)

= Requirements =

1. Contact Form 7
2. Klaviyo account

= How to Use =
1. Go to Contact Form 7 and edit the form you are configuring
2. Go to the "Klaviyo" Tab and enter the [private API key](https://help.klaviyo.com/hc/en-us/articles/7423954176283)
3. Check the âEnable Integrationâ box IF it is not automatically updated
4. Enter your Klaviyo API key and connect
5. Select the list where you want your vistors to be subscribed to
6. Map Fields
7. Save form and test.

== Frequently Asked Questions ==

Do you have questions or issues with Contact Form 7 Klaviyo Extension? Use these support channels appropriately.

= Where do I get support? =

Our team provides free support at the [developer's website](https://sitecare.sitepact.com/) or Live Chat if available. You may also request support in the WordPress forums, though directly at the developers website may be faster.

= Email/Phone not subscribed to list =

There are **4 main reasons** for this.
1. Single Opt-In in not enabled on the Klaviyo list
	*If single opt-in is not enabled on a klaviyo list then subscribers will need to click the confirmation link that klaviyo sends to their email address before they are subscribed to list. Here are some instruction to [enable single opt-in](https://help.klaviyo.com/hc/en-us/articles/115005251108)

2. GDPR option is enabled
	*If the GDPR option is enabled in the plugin and the user does not consent (tick checkbox) on the form, their details will not be sent to Klaviyo.

3. Phone number subscription
	*If you are subscribing phone numbers, please ensure numbers are sent in E.164 format. Eg. +12345678900. [More details here](https://help.klaviyo.com/hc/en-us/articles/360046055671-Accepted-phone-number-formats-for-SMS-in-Klaviyo)
    *You can enable the Telephone input mask feature to ensure phone numbers are collected in the correct format.

4. Incorrect API Key
	*Please ensure you are using the correct private API key. [More details here](https://help.klaviyo.com/hc/en-us/articles/115005062267-How-to-manage-your-account-s-API-keys)


= How do I Disable the opt-in email sent to contact on form submission =

You need to ask Klaviyo to disable double opt in on the list you are sending details to. Unfortunately, we cannot control that. They are available over live chat and can do this in 2 minutes.


== Changelog ==

= 1.0.0 =

* Release version

= 1.0.2 =

* Updated to Klaviyo new API
* Fixed Bugs and improved interface

= 1.0.3 =

* Removed warning messages

= 1.0.4 =

* Fixed GDPR checkbox bug

= 1.0.45 =

* Fixed deprecation warning

= 1.0.5 =

* Fixed log page 404 error

= 3.0.0 =

* Upgrade to Klaviyo V3 API (Breaking change) *
* Improved logs
* Improved validation
* Added ability to turn off logging for optimal performance.
* Improved UI
* Added ability to "Refresh Klaviyo List" or fetch updated lists
* Improved field mapping (Breaking improvement due to API changes)
* Improved GDPR shortcode addition and removal
* Added subscription source as an option
* Added telephone input mask feature - ensure phone numbers are entered in the correct E.164 format

= 3.0.1 =

* Improved plugin security - database, validationg, sanitizing and escaping.
* Tested and updated minimum PHP version.
* Updated minimum WP Version.
* Added nonce checks.
* Fixed logs view button.
* Fixed duplicate profile issue with Klaviyo subscription. Profiles will now be subscribed even if duplicated and profile will be updated with new information.


== Screenshots ==

1. Plugin configuration screen.
2. Adding fields on configuration screen.
3. GDPR configuration
4. Klaviyo Submission Logs