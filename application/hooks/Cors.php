<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cors
{
	public function enable()
	{
		// Allow only specific origin
		header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
		header("Access-Control-Allow-Headers: Content-Type, Authorization");

		// Handle preflight (OPTIONS) request
		if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
			exit(0);
		}
	}
}
