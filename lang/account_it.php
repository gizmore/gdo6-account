<?php
return array(
'module_account' => 'Conto',
'btn_account' => 'Conto',
'link_account_form' => 'Il suo conto',
'link_account_security' => 'Sicurezza',
'link_account_access' => 'Logs',
'link_account_delete' => 'Cancellare conto',
'link_settings' => 'Impostazioni',
##########################################################
'cfg_adult_age' => 'etá adulta',
'cfg_tt_adult_age' => 'inserisca l´etá per contenuti d´adulto',
'cfg_account_changetime' => 'Demografia puó essere solo cambiata ogni ...',
'cfg_allow_real_name' => 'Permetti di cambiare il nome reale?',
'cfg_allow_guest_settings' => 'Permetti a ospiti di adeguare il conto',
'cfg_allow_country_change' => 'Permetti al utente di cambiare la sua nazione?',
'cfg_allow_lang_change' => 'Permetti al utente di cambiare la lingua?',
'cfg_allow_birthday_change' => 'Permetti al utente di cambiare la data di nascita?',
'cfg_allow_gender_change' => 'Permetti al utente di cambiare il sesso?',
'cfg_allow_email_change' => 'Permetti al utente di cambiare l´e-Mail?',
'cfg_allow_email_fmt_change' => 'Permetti di cambiare il formato e-Mail?',
'cfg_feature_access_history' => 'Attivazione registrazione login?',
'cfg_feature_account_deletion' => 'Attivazione cancellazione dei conti?',
'cfg_feature_demographic_mail_confirm' => 'Modifiche mail confermare via mail?',
'cfg_user_allow_email' => 'Permetti a membri di inviarti mail?',
##########################################################
'box_content_account_settings' => 'qui trovate tutte le vostre impostazioni.', 
'ft_account_settings' => '%s Impostazioni',
'div_user_settings' => '%s-Impostazioni personali',
'div_variables' => 'Le vostre %s variabili',
'msg_settings_saved' => 'Le vostre impostazioni sono state riportate nel %s modulo.<br/>%s',
##########################################################
'ft_account_form' => 'Conto',
'infobox_account_form' => 'Avviso: Il vostro &quot;nome reale&quot; possiamo inserirlo solo una volta.<br/>La sua opzione demografica puó cambiarla ogni %s.',
'section_login' => 'Informazioni sul conto',
'section_email' => 'Impostazioni e-Mail',
'section_demographic' => 'Demografico',
'section_options' => 'Opzioni',
'user_hide_online' => 'Nascondere staus online?',
'user_want_adult' => 'Mostra contenuto per adulti?',
'user_show_birthdays' => 'Visualizza data di nascita?',
'msg_real_name_now' => 'Il suo "nome reale" adesso é %s.',
'msg_user_hide_online_on' => 'Il suo status online é adesso invisibile.',
'msg_user_show_birthdays_on' => 'Adesso avete attivato il communicato di il communicato di nascite.',
'msg_user_allow_email_on' => 'Lei permette ad altri di inviarLe e-Mail, senza svelare la sua e-Mail.',
'msg_user_want_adult_on' => 'Adesso Lei vedrá contenuti per adulti.',
'msg_mail_sent' => 'A Lei é stata inviata una Mail con istruzioni.',
'msg_demo_changed' => 'Le Sue impostazioni demografiche sono state cambiate.',
'msg_email_fmt_now_html' => 'Il Suo formato e-Mail é ora HTML.',
'msg_email_fmt_now_text' => 'Il Suo formato e-Mail é ora PLAINTEXT.',
'err_demo_wait' => 'Prego attenda %s prima di cambiare le sue impostazioni!',
'email_fmt' => 'Formato e-Mail',
##########################################################
'ft_account_security' => 'Opzioni di sicurezza',
'box_account_security' => 'Potete attivare la registrazione dell`IP per ottenere alerts via mail.',
'accset_record_ip' => 'Registrati con successo',
'accset_uawatch' => 'Avviso di cambio utente',
'accset_ipwatch' => 'Avviso di cambio IP',
'accset_ispwatch' => 'Avviso di cambio Provider',
##########################################################
'ft_account_delete' => 'Cancella account',
'box_info_deletion' =>  'Puó scegliere di disattivare il suo account e preservando la sua identitá su %s,
	o di troncare il suo account con tutte le informazioni associate.
	Se desidera, puó inviarci un messaggio con la raggione per cui desidera lasciarci.',
'btn_delete_account' => 'Spuren gelöscht',
'btn_prune_account' => 'Troncare account',
'msg_account_marked_deleted' => 'Il suo account é stato marcato da cancellare.',
'msg_account_pruned' => 'Il suo account é stato rimosso con sucesso dalla databanchi.',
##########################################################
'ft_change_mail' => 'Cambia e-Mail',
'err_email_retype' => 'Gentilmente ricontrolli la sua e-Mail, visto che non é stata riinserita correttamente.',
'btn_changemail' => 'Cambia e-Mail',
##########################################################
'mail_subj_account_deleted' => '[%s] %s Account cancellato',
'mail_body_account_deleted' => '
Ciao,

L´utente %s ha appena eseguito la seguente operatione sul suo account: %s.

Ha lasciato la seguente nota: (forse vuota)
---------------------------------------------------------
%s
---------------------------------------------------------
Cosdiali saluti,
il %s Script',
##########################################################
'mail_subj_chmail_a' => '[%s] cambia e-Mail',
'mail_body_chmail_a' => '
Caio,

Desideri cambiare la tua e-Mail sul %s sul tuo nuovo indirizzo: <b>%s</b>.
	
Nel caso desideri accettare questo cambiamento, segui il seguente link:
	
	%s

Gentili saluti,
Il suo %2$s Team',
##########################################################
'mail_subj_chmail_b' => '[%s] Confermi e-Mail',
'mail_body_chmail_b' => '
Ciao %s,

Vuole cambiare il suo e-Mail da %s a (%s).
	
Se desidera accettare il cambiamento, la preghiamo di seguire il link seguente:
	
%s
	
Cordiali saluti
Il suo %2$s Team.',
##########################################################
'mail_subj_demochange' => '[%s] Cambio demografico',
'mail_body_demochange' =>'
Salve %s,
	 	
Desidera cambiare le sue impostazioni demografiche su %s?
Verifichi se le seguenti impostazioni sono corrette,
visto che le puó cambiare solo ogni %s.
	
	Nazione: 			%s
	Lingua: 			%s	
	Sesso:				%s
	Data di nascita:	%s

 Se le informazioni sono corrette, accetti le impostazioni nel link seguente:
	 
	 %s
		
 Altrimenti igniori questa mail e riprovi quando vuole.
	 
 Cordiali saluti,
 Il suo %2$s Team.',
##########################################################
'mail_subj_account_alert' =>'[%s] Avviso di accesso',
'mail_body_account_alert' =>  '
Salve %s,

Abbiamo notato un accesso con una configurazione insolita sul suo account %s.
	
	UserAgent:		%s
	Indirizzo IP:	%s
	Hostname/ISP:	%s
		
Puó verificare i suoi accessi nel link seguente:
	
	%s
		
Puó disattivare l´avviso di accesso qui:
	
	%s
		
Saluti,
Il suo %2$s Team.',
##########################################################
'msg_change_allow_email' => 'Hai modificato se qualcuno è autorizzato a inviarti un\' email a %s.',
'table_account_access' => '%s accessi sono stati registrati per te.',
'confirm_account_prune' => 'Sei sicuro di voler rimuovere completamente il tuo account? Questo non può essere annullato!',
'confirm_account_delete' => 'Sei sicuro di voler contrassegnare il tuo account come eliminato? Questo non è definitivo. Un dipendente può riattivare il tuo account in un secondo momento.',
);
