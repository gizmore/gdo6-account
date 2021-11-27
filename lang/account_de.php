<?php
return array(
'module_account' => 'Nutzerkonto',
'btn_account' => 'Konto',
'link_account_form' => 'Ihr Konto',
'link_account_delete' => 'Konto Löschen',
'link_settings' => 'Einstellungen',
'prune' => 'Entfernen',
##########################################################
'cfg_adult_age' => 'Erwachsenenalter',
'cfg_tt_adult_age' => 'Geben Sie das Mindestalter an für Inhalt für Erwachsene.',
'cfg_account_changetime' => 'Demographie kann nur alle ... geändert werden',
'cfg_allow_real_name' => 'Erlauben den Realnamen zu ändern?',
'cfg_allow_guest_settings' => 'Erlaube Gästen das Konto anzupassen',
'cfg_allow_country_change' => 'Erlaube Nutzern ihr Land zu ändern?',
'cfg_allow_lang_change' => 'Erlaube Nutzern die Sprache zu ändern?',
'cfg_allow_birthday_change' => 'Erlaube Nutzern ihr Geburtsdatum zu ändern?',
'cfg_allow_gender_change' => 'Erlaube Nutzern das Geschlecht zu ändern?',
'cfg_allow_email_change' => 'Erlaube Nutzern die Email zu ändern?',
'cfg_feature_account_deletion' => 'Aktiviere Löschen von Konten?',
'cfg_feature_demographic_mail_confirm' => 'Email Änderungen per Email bestätigen?',
##########################################################
'box_content_account_settings' => 'Hier finden Sie alle Ihre Einstellungen.',
'ft_account_settings' => '%s Einstellungen',
'div_user_settings' => 'Persönliche %s-Einstellungen',
'div_variables' => 'Ihre %s Variablen', 
'msg_settings_saved' => 'Ihre Einstellungen im %s Modul wurden übernommen.<br/>%s',
##########################################################
'ft_account_form' => 'Konto',
'infobox_account_form' => 'Hinweis: Ihren &quot;Realnamen&quot; können Sie nur einmalig setzen und <b>er ist öffentlich sichtbar</b>.<br/>Ihre Demographischen Optionen können sie alle %s ändern.',
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
'msg_user_want_adult_on' => 'Sie sehen nun Inhalte für Erwachsene.',
'msg_mail_sent' => 'Ihnen wurde eine E-Mail mit Anweisungen zugesandt.',
'msg_demo_changed' => 'Ihre demographischen Einstellungen wurden geändert.',
'msg_email_fmt_now_html' => 'Ihr bevorzugtes E-Mail Format ist nun HTML.',
'msg_email_fmt_now_text' => 'Ihr bevorzugtes E-Mail Format ist nun PLAINTEXT.',
'err_demo_wait' => 'Bitte warten Sie %s bevor Sie ihre Einstellungen ändern.',
'email_fmt' =>'E-Mail Format',
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

If the information is correct, you can accept these settings by visiting this link.

%s

Otherwise, please ignore this E-Mail and try again anytime.

Kind Regards
The %2$s Team',
##########################################################
'confirm_account_prune' => 'Möchten Sie Ihr Konto wirklich vollständig entfernen? Dies lässt sich nicht rückgängig machen!',
'confirm_account_delete' => 'Möchten Sie Ihr Konto wirklich als gelöscht markieren? Dies ist nicht endgültig. Ein Mitarbeiter kann Ihr Konto später wieder reaktivieren.',
'msg_mail_changed' => 'Ihre Email wurde zu %s geändert.',
'mdescr_account_access' => 'Sehen Sie sich Ihre letzten Authentifizierungen an.',
# 6.11.0
'div_user_textual_settings' => 'Text Einstellungen',
);
