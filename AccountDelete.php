<?php
namespace GDO\Account;

use GDO\DB\GDO;
use GDO\Type\GDO_Message;
use GDO\User\GDO_Username;
use GDO\User\User;
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
			GDO_Username::make('accrm_username')->primary(),
			GDO_Message::make('accrm_note')->notNull(),
		);
	}

	##############
	### Static ###
	##############
	public static function insertNote(User $user, string $note)
	{
		return self::blank(['accrm_username' => $user->getName(), 'accrm_note' => $note])->insert();
	}
}
