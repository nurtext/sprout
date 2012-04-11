<?php
/**
 * Router 
 *
 * Singleton router class to manage URL routes
 * 
 * @author Cedric Kastner <cedric@nur-text.de>
 * @version 1.0.0
 **/
class Router
{
	// Stores the smarty instance
	static public $smarty = NULL;
	
	// Instance variable for storing the Singleton
	static private $instance = NULL;
	
	// Default route that gets processed if no route is given
	static private $default_route = 'index';

	// Optional filename ending to simulate static documents
	static private $ending = '.html';

	// Default trigger variable for GET-Requests
	static private $trigger = 'route';
	
	// Sub-directory for view templates
	static private $view_dir = 'views/';
	
	// Sub-directory for markdown texts
	static private $md_dir = 'md/';
	
	// Stores the current route
	static private $current_route = '';
	
	// Stores the current template file we're working with
	static private $tpl = '';
	
	// Stores the current configuration file
	static private $conf = '';
	
	// Stores the current markdown file
	static private $md = '';
	
	// Static class, forbid constructing/cloning
	private function __construct(){}
	private function __clone(){}
	
	// Get the current instance or create one if it doesn't exist
	static public function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new self;
			
		}
		
		return self::$instance;
		
	}
	
	// Assign smarty object to class
	static public function setSmarty($smarty)
	{
		self::$smarty = $smarty;

		// Disable debugging and caching
		self::$smarty->debugging = FALSE;
		self::$smarty->caching   = FALSE;
		
	}

	// Overrides the default trigger variable which defines what route to follow
	static public function setTriggerVariable($trigger)
	{
		self::$trigger = $trigger;

	}
	
	// Overrides the default route
	static public function setDefaultRoute($route)
	{
		self::$default_route = $route;

	}
	
	// Overrides the default filename ending
	static public function setFilenameEnding($ending)
	{
		self::$ending = $ending;
		
	}

	// Overrides the default directory for view templates
	static public function setViewDirectory($dirname)
	{
		self::$view_dir = $dirname;
		
	}

	// Overrides the locale in case you're working with Smarty's date functions
	static public function setLocale($category = LC_ALL, $locale = 'en_US.utf8')
	{
		setlocale($category, $locale);
	}
	
	// Function to display a 404 error, used if the files for a route could not be found
	static public function error404($view = '404.tpl', $conf = '404.conf', $md = '404.md')
	{
		// Set correct header
		header('HTTP/1.0 404 Not Found');
		
		// Assign variables
		self::$smarty->assign('viewFile', self::$view_dir . $view);
		self::$smarty->assign('markdownFile', self::$md_dir . $md);
		self::$smarty->assign('currentRoute', self::$current_route);
		self::$smarty->assign('configFile', $conf);
		
		// Show 404
		self::$smarty->display('main.tpl');
		return;
		
	}
	
	// Let the routing begin
	static public function doRouting()
	{
		// Set the locale
		self::setLocale();

		// Check if we have a route and it's not empty
		if (isset($_GET[self::$trigger]) && !empty($_GET[self::$trigger]))
		{
			// Cleanup the route using our regex (only alphanumeric and url safe chars (.,-_/) are allowed)
			self::$current_route = preg_replace('/[^[:alnum:]\.\-\_\/]/', '', $_GET[self::$trigger]);
			
			// See if we have an filename ending (.html, etc.)
			if (self::$ending !== '')
			{
				// Calculate the offset for the length of the filename ending
				$offset = @strrpos(self::$current_route, self::$ending, -(strlen(self::$ending)));
				
				// Check if offset is present
				if ($offset !== FALSE)
				{
					// So we remove the ending using the calculated offset to include the files we need
					self::$tpl	= substr_replace(self::$current_route, '', $offset, strlen(self::$ending)) . '.tpl';
					self::$conf	= substr_replace(self::$current_route, '', $offset, strlen(self::$ending)) . '.conf';
					self::$md	= substr_replace(self::$current_route, '', $offset, strlen(self::$ending)) . '.md';
					
				}
				else
				{
					// Show 404
					self::error404();
					return;
					
				}
				
			}
			else
			{
				// We don't have a filename ending, thus we can assign the route's name to files we need
				self::$tpl  = self::$current_route . '.tpl';
				self::$conf = self::$current_route . '.conf';
				self::$md   = self::$current_route . '.md';
				
			}
			
			// Check for needed files we include later
			if (!file_exists('templates/' . self::$view_dir . self::$tpl) ||
				!is_readable('templates/' . self::$view_dir . self::$tpl) || 
				!is_file('templates/'     . self::$view_dir . self::$tpl))
			{
				// ...and if not show up the 404
				self::error404();
				return;
				
			}
			
		}
		else
		{
			// Set the current route to the default
			self::$current_route = self::$default_route . self::$ending;

			// Since we have no route, try to show up the default one
			self::$tpl  = self::$default_route . '.tpl';
			self::$conf = self::$default_route . '.conf';
			self::$md   = self::$default_route . '.md';
			
			// Again, check for needed files we include later
			if (!file_exists('templates/' . self::$view_dir . self::$tpl) ||
				!is_readable('templates/' . self::$view_dir . self::$tpl) ||
				!is_file('templates/' . self::$view_dir . self::$tpl))
			{
				// ...and if not shopw up the 404 (again)
				self::error404();
				return;
				
			}
			
		}
		
		// Assign all needed files to our main template
		self::$smarty->assign('viewFile',     self::$view_dir . self::$tpl);
		self::$smarty->assign('markdownFile', self::$md_dir . self::$md);
		self::$smarty->assign('currentRoute', self::$current_route);
		self::$smarty->assign('configFile',   self::$conf);
		
		// Show the main template
		self::$smarty->display('main.tpl');
		
	}
    
}