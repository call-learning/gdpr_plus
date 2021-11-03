GDPR Plus : enhanced tool policies
--

[![Build Status](https://travis-ci.org/call-learning/moodle-tool_gdpr_plus.svg?branch=master)](https://travis-ci.org/call-learning/moodle-tool_gdpr_plus)

The aim of this plugin is to enhance the tool_policy plugin on the specific issue of user acceptation of the policy
through the "cookie" banner.

The original tool_policy banner, only allow to accept but we cannot deny authorisation to use some cookies.
Another aspect is that the cookie acceptance is not linked to any specific policy, so it is more a all-or-nothing kind of choce. 
Additionally, there is no way to display the banner back once accepted.

Recent changes in GDPR/RGPD has made the acceptance or refusal mandatory. Refusal means refusing
non-essential cookies (session cookies for example). For reference, this is the new framework:
https://www.cnil.fr/en/refusing-cookies-should-be-easy-accepting-them-results-second-campaign-orders-and-future-actions
(in French: https://www.cnil.fr/fr/nouvelles-regles-cookies-et-autres-traceurs-bilan-accompagnement-cnil-actions-a-venir)


Usage
--

Once the plugin has been installed, you need to set it as the main
policy handler in Site administration > Users > Privacy and policies > Policy settings

Once this is done, the banner should appear automatically at the bottom of the page.
It will still use the policies defined in the tool_policy parameters and depending on a policy setting
(authenticated user or not, mandatory policy or not), it will display the right policies in the list once 
you click on "Show settings".

An additional link will be automatically added at the bottom of the page ("Show policies") to
make the banner appear once again when the policies are accepted.

Note that the way we design it was to rely on policies acceptance and not "cookie" acceptance: it means
that if you want users to accept a given cookie (for example Google Analytics) you need
to define the relevant policy to be accepted. 
Once a policy has been accepted, it will send a message through javascript "grpd_policies_accepted", it is then
up to the theme developer to enable the related javascript (for example Google Analytics) depending on which policy has been accepted.

Features
--

* Additional link to go back to settings
* Messaging through javascript for acceptance
* Use Moodle session to store information about acceptance of cookie before even the user is logged in.
* Uses templates.
