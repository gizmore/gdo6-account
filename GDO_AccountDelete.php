<?php
namespace GDO\Account;

use GDO\Core\GDO;
use GDO\UI\GDT_Message;
use GDO\User\GDT_Username;
use GDO\User\GDO_User;
/**
 * An account deletion note.
 * @author gizmore
 */
final class GDO_AccountDelete extends GDO
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
	public static function insertNote(GDO_User $user, $note)
	{
		return self::blank([
			'accrm_username' => $user->getName(),
			'accrm_note' => $note,
		])->insert();
	}
}
