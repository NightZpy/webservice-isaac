<?php
class Database extends SQLite3
{
	function __construct($path)
	{
		$this->open($path);
	}
}

