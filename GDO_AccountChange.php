<?php
namespace GDO\Account;
use GDO\Core\GDO;
use GDO\DB\GDT_CreatedAt;
use GDO\Form\GDT_Enum;
use GDO\Type\GDT_Serialize;
use GDO\Type\GDT_Token;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
/**
 * A table to store tokens associated with a userid type and serialized data.
 * Used to change demographic options on a bunch via mail,
 * and to change the email.
 * Could be used in more stuff, like Recovery.
 * 
 * @author gizmore
 * @version 5.0
 * @since 3.0
 */
final class GDO_AccountChange extends GDO
{
	public function gdoCached() { return false; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDT_User::make('accchg_user')->primary(),
			GDT_Enum::make('accchg_type')->enumValues('email', 'email2', 'demo', 'demo_lock')->primary(),
			GDT_Token::make('accchg_token')->notNull(),
			GDT_Serialize::make('accchg_data'),
			GDT_CreatedAt::make('accchg_time'),
		);
	}
	
	##############
	### Getter ###
	##############
	/**
	 * @return GDO_User
	 */
	public function getUser() { return $this->getValue('accchg_user'); }
	public function getUserID() { return $this->getVar('accchg_user'); }
	public function getTimestamp() { return $this->getValue('accchg_time'); }
	public function getToken() { return $this->getVar('accchg_token'); }
	public function getData() { return $this->getValue('accchg_data'); }
	
	##############
	### Static ###
	##############
	/**
	 * @param string $userid
	 * @param string $type
	 * @param mixed $data
	 * @return self
	 */
	public static function addRow(string $userid, string $type, $data=null)
	{
		$row = self::blank(['accchg_user' => $userid, 'accchg_type' => $type]);
		$row->setValue('accchg_data', $data);
		return $row->replace();
	}
	
	/**
	 * @param string $userid
	 * @param string $type
	 * @param string $token
	 * @return self
	 */
	public static function getRow(string $userid, string $type, $token=true)
	{
		$condition = 'accchg_user=%s AND accchg_type=%s' . ($token===true?'':' AND accchg_token=%s');
		$condition = sprintf($condition, quote($userid), quote($type), quote($token));
		return self::table()->select()->where($condition)->exec()->fetchObject();
	}
}
