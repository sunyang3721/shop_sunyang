<?php
class comment_table extends table
{
	protected $_validate = array(

	);

	protected $_auto = array(
		array('datetime', TIMESTAMP, 1, 'string'),
		array('clientip', 'get_client_ip', 1, 'function'),
	);
}