<?php
return array(
'btn_account' => 'Konto',
'link_account_form' => 'Ihr Konto',
'link_account_encryption' => 'GPG',
'link_account_security' => 'Sicherheit',
'link_account_access' => 'Logs',
'link_account_delete' => 'Konto Löschen',
'link_settings' => 'Einstellungen',
##########################################################
'cfg_adult_age' => 'Adult age',
'cfg_tt_adult_age' => 'Specify what the min age is for adult content.',
'cfg_account_changetime' => 'Demographic change timeout',
'cfg_allow_real_name' => 'Allow change of Realname',
'cfg_allow_guest_settings' => 'Allow guests to change account',
'cfg_allow_country_change' => 'Allow change of country',
'cfg_allow_lang_change' => 'Allow change of language',
'cfg_allow_birthday_change' => 'Allow change of birthdate',
'cfg_allow_gender_change' => 'Allow change of gender',
'cfg_allow_email_change' => 'Allow change of email',
'cfg_allow_email_fmt_change' => 'Allow change of email format',
'cfg_feature_access_history' => 'Security: Enable login history',
'cfg_feature_account_deletion' => 'Allow account deletion',
'cfg_feature_gpg_engine' => 'Enable Email GPG Éngine',
'cfg_feature_demographic_mail_confirm' => 'Enable email confirmation',
'cfg_user_allow_email' => 'Mitglieder dürfen Dir eine Email zu senden?',
##########################################################
'box_content_account_settings' => 'Hier finden Sie alle Ihre Einstellungen.',
'ft_account_settings' => '%s Einstellungen',
'div_user_settings' => 'Persönliche %s-Einstellungen',
'div_variables' => 'Ihre %s Variablen', 
'msg_settings_saved' => 'Ihre Einstellungen im %s Modul wurden übernommen.<br/>%s',
##########################################################
'ft_account_form' => 'Konto',
'infobox_account_form' => 'Hinweis: Ihren &quot;Realnamen&quot; können Sie nur einmalig setzen.<br/>Ihre Demographischen Optionen können sie alle %s ändern.',
'section_login' => 'Kontoinformationen',
'section_email' => 'E-Mail Einstellungen',
'section_demographic' => 'Demographie',
'section_options' => 'Optionen',
'user_hide_online' => 'Online status verstecken?',
'user_want_adult' => 'Inhalt für Erwachsene anzeigen?',
'user_show_birthdays' => 'Geburtstage anzeigen?',
'msg_real_name_now' => 'Ihr "Realname" ist nun %s.',
'msg_user_hide_online_on' => 'Ihr Online Status ist nun unsichtbar.',
'msg_user_show_birthdays_on' => 'Sie haben nun Geburtstagsmeldungen aktiviert.',
'msg_user_allow_email_on' => 'Sie erlauben nun anderen Ihnen eine E-Mail zu schreiben, ohne Ihre Adresse preiszugeben.',
'msg_user_want_adult_on' => 'Sie sehen nun Inhalte für Erwachsene.',
'msg_mail_sent' => 'Ihnen wurde eine E-Mail mit Anweisungen zugesandt.',
'msg_demo_changed' => 'Ihre demographischen Einstellungen wurden geändert.',
'msg_email_fmt_now_html' => 'Ihr bevorzugtes E-Mail Format ist nun HTML.',
'msg_email_fmt_now_text' => 'Ihr bevorzugtes E-Mail Format ist nun PLAINTEXT.',
'err_demo_wait' => 'Bitte warten Sie %s bevor Sie ihre Einstellungen ändern.',
'email_fmt' =>'E-Mail Format',
##########################################################
'ft_account_encryption' => 'GPG Einstellungen',
'gpg_pubkey' => 'Public Key',
'gpg_file' => 'Public Key File',
'infob_gpg_upload' => 'Here you can upload a GPG key to enable email encryption.',
'err_gpg_fail_fingerprinting' => 'Fingerprinting your upload failed.',
'err_gpg_token' => 'Your GPG token is invalid.',
'msg_gpg_key_added' => 'Your GPG key has been imported and encryption of your E-Mails is enabled.',
##########################################################
'ft_account_security' => 'Security Options',
'box_account_security' => 'You can enable IP recording to get alerts via E-Mail.',
'accset_record_ip' => 'Record successful login IPs',
'accset_uawatch' => 'Alert on UserAgent change',
'accset_ipwatch' => 'Alert on IP change',
'accset_ispwatch' => 'Alert on Provider change',
##########################################################
'ft_account_delete' => 'Delete Account',
'box_info_deletion' => 'You can choose between disabling your account, and preserving your identity on %s,
Or completely prune your account and all information associated.
If you like, you can leave us a message with feedback on why you wanted to leave.',
'btn_delete_account' => 'Mark Deleted',
'btn_prune_account' => 'Prune Account',
'msg_account_marked_deleted' => 'Your account has been marked as deleted.',
'msg_account_pruned' => 'Your account has been wiped from the database.',
##########################################################
'ft_change_mail' => 'Change E-Mail',
'err_email_retype' => 'Please recheck your E-Mail, as you did not retype it correctly.',
'btn_changemail' => 'Change E-Mail',
##########################################################
'mail_subj_account_deleted' => '[%s] %s Account Deletion',
'mail_body_account_deleted' => '
Hello %s,

The user %s has just executed the following operation on his account: %s.

He has left the following note: (may be empty)
----------------------------------------------
%s
----------------------------------------------
Kind Regards
The %s Script',
##########################################################
'mail_subj_chmail_a' => '[%s] Change E-Mail',
'mail_body_chmail_a' => '
Hello %s,

You want to change your E-Mail on %s to your new Address: <b>%s</b>.

If you want to accept this change, please visit the following link.

%s

Kind Regards
The %2$s Team',
##########################################################
'mail_subj_chmail_b' => '[%s] Confirm E-Mail',
'mail_body_chmail_b' => '
Hello %s,

You want to change your E-Mail on %s to this one (%s).

If you want to accept the change, please visit the following link.

%s

Kind Regards,
The %2$s Team.',
##########################################################
'mail_subj_demochange' => '[%s] Change Demography',
'mail_body_demochange' => '
Hello %s,

You want to change your demographic settings on %s.
Please check if the following settings are correct,
because you can only change them once every %s.

Country: %s
Language: %s
Gender: %s
Date of Birth: %s

If the information is correct, you can accept these settings by visiting this link.

%s

Otherwise, please ignore this E-Mail and try again anytime.

Kind Regards
The %2$s Team',
##########################################################
'mail_subj_account_alert' => '[%s] Access Alert',
'mail_body_account_alert' => '
Hello %s,

There has been access to your %s account with an unusual configuration.

UserAgent: %s
IP Address: %s
Hostname/ISP: %s

You can check your access history here.

%s

You can toggle your access alerts here.

%s

Kind Regards,
The %2$s Team',
##########################################################
		
);
