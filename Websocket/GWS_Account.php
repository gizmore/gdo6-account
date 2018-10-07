<?php
namespace GDO\Account\Websocket;

use GDO\Websocket\Server\GWS_CommandForm;
use GDO\Account\Method\Form;
use GDO\Websocket\Server\GWS_Commands;

final class GWS_Account extends GWS_CommandForm
{
	public function getMethod() { return Form::make(); }
}

GWS_Commands::register(0x0121, new GWS_Account());