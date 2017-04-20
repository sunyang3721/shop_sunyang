<?php
class appvar_table extends table{
	protected $_validate = array(
		array('title','require','',table::MUST_VALIDATE),
		array('variable','require','',table::MUST_VALIDATE),
	);
}