<?php
namespace GDO\Account;

use GDO\DB\GDO;
use GDO\Type\GDT_Message;
use GDO\User\GDT_Username;
use GDO\User\GDO_User;
/**
 * An account deletion note.
 * @author gizmore
 */
final class AccountDelete extends GDO
{
	public function gdoCached() { return false; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDT_Username::make('accrm_username')->primary(),
			GDT_Message::make('accrm_note')->notNull(),
		);
	}

	##############
	### Static ###
	##############
	public static function insertNote(GDO_User $user, string $note)
	{
		return self::blank(['accrm_username' => $user->getName(), 'accrm_note' => $note])->insert();
	}
}
