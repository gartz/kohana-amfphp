<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Amfphp_AMFPHP extends Controller
{
	public function action_index()
	{
		$this->action_gateway();
	}
	
	public function action_gateway()
	{
		$service_type = $this->request->param('type');
		
		switch ($service_type)
		{
			case "xmlrpc":
				require_once Kohana::find_file('vendor', 'amfphp/xmlrpc');
				break;
			case "json":
				require_once Kohana::find_file('vendor', 'amfphp/json');
				break;
			case "amf":
			default: 
				require_once Kohana::find_file('vendor', 'amfphp/gateway');
		}
	}
	
	public function action_browser()
	{
		if (Kohana::config('amfphp.disable_browser'))
		{
			$this->request->redirect('/');
			return;
		}
		
		$this->response->body( View::factory('browser/index') );
	}
	
	public function action_asset()
	{
		$file = $this->request->param('filename');
		
		$file = explode(".", $file);
		
		$file = Kohana::find_file('assets', $file[0], $file[1]);
		
		if (!is_file($file))
		{
			throw new Kohana_Exception('Asset does not exist');
		}
		
		$this->request->headers('Content-Type', (string) File::mime($file));
        $this->request->headers('Content-length', (string) filesize($file));
		
		$this->response->send_headers();
		
		$content = @fopen($file, 'rb');
		if ($content)
		{
			fpassthru($content);
			exit;
		}
	}
}

