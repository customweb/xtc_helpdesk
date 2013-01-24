var tabInstances = new Array();

var Tab = Class.create(
{
	instance: false,
	prefix: false,
	element: false,
	registers: false,
		
	initialize: function(element, instance)
	{
		this.instance = instance;
		this.element = element;
		this.prefix = this.element.id.split(/_/)[1];
	
		// add onclick to all registers:
		var registers = element.select('ul li a');	
		for(var i = 0; i < registers.length; i++)
		{
			//registers[i].writeAttribute('onclick', 'return tabInstances['+this.instance+'].show(this);');
			//registers[i].writeAttribute('onclick', 'alert(\'ddd\');');
			registers[i].observe('click', (this.show).bind(this));
		}
		
	},
	
	show: function (event)
	{
		event.stop();
		var e = Event.element(event);
		var ereg = "tabs."+this.prefix+".=([^;]*)";
		var rs = document.cookie.match(ereg);
		var currentTab = rs[1];
		
		ereg = "tabs."+this.prefix+".=([^&]*)";
		var tab = e.href.match(ereg);
		var newTab = tab[1];
		
		// set cookie new:
		document.cookie = 'tabs['+this.prefix+']='+tab[1];

		// hide current tab:
		$(this.prefix+'_'+currentTab+'_content').removeClassName('Active');
		$(this.prefix+'_'+currentTab+'_link').removeClassName('Active');
		
		// show new tab:
		$(this.prefix+'_'+newTab+'_content').addClassName('Active');
		$(this.prefix+'_'+newTab+'_link').addClassName('Active');
				
		return false;
	}
	
}
);


function scanForTabs()
{
	var tabs = $$('.Tab');	
	for(var i = 0; i < tabs.length; i++)
	{
		tabInstances[i] = new Tab(tabs[i], i);
	}
}


document.observe('dom:loaded', function () { scanForTabs(); });