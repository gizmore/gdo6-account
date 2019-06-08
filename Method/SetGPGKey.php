<?php
namespace GDO\Account\Method;

use GDO\Account\Module_Account;
use GDO\Core\Method;
use GDO\User\GDO_PublicKey;
use GDO\User\GDO_User;
use GDO\Util\Common;
/**
 * GPG Mail links here to finally save the GPG key.
 * @author gizmore
 * @since 3.0
 * @version 5.0
 * @see Mail
 */
final class SetGPGKey extends Method
{
	public function isEnabled() { return Module_Account::instance()->cfgFeatureGPGEngine(); }
	
	public function execute()
	{
		$user = GDO_User::table()->find(Common::getGetString('userid'));
		$tmpfile = GDO_PATH . 'temp/gpg/' . $user->getID();
		$file_content = file_get_contents($tmpfile);
		unlink($tmpfile);

		if (!($fingerprint = GDO_PublicKey::grabFingerprint($file_content)))
		{
			return $this->error('err_gpg_fail_fingerprinting');
		}
		
		if (Common::getGetString('token') !== $fingerprint)
		{
			return $this->error('err_gpg_token');
		}
		
		GDO_PublicKey::updateKey($user->getID(), $file_content);
		
		return $this->message('msg_gpg_key_added');
	}
}
