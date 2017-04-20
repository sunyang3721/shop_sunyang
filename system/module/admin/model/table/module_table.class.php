<?php
class module_table extends table
{
	public function fetch_by_identifier($identifier) {
		return $this->where(array('identifier' => $identifier))->find();
	}
}