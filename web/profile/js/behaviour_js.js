var Behaviour = {
	list : new Array,
	
	register : function(rule){
		Behaviour.list.push(rule);
		//alert(rule.name);
	},
	
	start : function(){
		Behaviour.addLoadEvent(function(){
			Behaviour.apply();
		});
	},
	
	apply : function(){
		for (i=0;rule=Behaviour.list[i];i++){
			for (selector in rule){
				list = document.getElementsBySelector(selector);
				
				if (!list){
					continue;
				}

				for (j=0;element=list[j];j++){
					rule[selector](element);
				}
			}
		}
	},
	
	addLoadEvent : function(toLoad){
		var oldonload = window.onload;
		
		if (typeof window.onload != 'function') {
			window.onload = toLoad;
		} else {
			window.onload = function() {
				oldonload();
				toLoad();
			}
		}
	}
}

Behaviour.start();

function getAllChildren(e)
{
	return e.all ? e.all : e.getElementsByTagName('*');
}

document.getElementsBySelector = function(selector)
{
	if (!document.getElementsByTagName)
		return new Array();

	var tokens = selector.split(' ');
	var currentContext = new Array(document);
	for (var i = 0; i < tokens.length; i++)
	{
		token = tokens[i].replace(/^\s+/,'').replace(/\s+$/,'');;
		if (token.indexOf('#') > -1)
		{
			var bits = token.split('#');
			var tagName = bits[0];
			var id = bits[1];
			var element = document.getElementById(id);

			if (tagName && element.nodeName.toLowerCase() != tagName)
				return new Array();

			currentContext = new Array(element);
			continue;
		}
		if (token.indexOf('.') > -1)
		{
			var bits = token.split('.');
			var tagName = bits[0];
			var className = bits[1];

			if (!tagName)
				tagName = '*';

			var found = new Array;
			var foundCount = 0;
			for (var h = 0; h < currentContext.length; h++)
			{
				var elements;

				if (tagName == '*')
					elements = getAllChildren(currentContext[h]);
				else
					elements = currentContext[h].getElementsByTagName(tagName);

				for (var j = 0; j < elements.length; j++)
					found[foundCount++] = elements[j];
			}

			currentContext = new Array;
			var currentContextIndex = 0;
			for (var k = 0; k < found.length; k++)
			{
				if (found[k].className && found[k].className.match(new RegExp('\\b'+className+'\\b')))
					currentContext[currentContextIndex++] = found[k];
			}
			continue;
		}
		if (token.match(/^(\w*)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/))
		{
			var tagName = RegExp.$1;
			var attrName = RegExp.$2;
			var attrOperator = RegExp.$3;
			var attrValue = RegExp.$4;

			if (!tagName)
				tagName = '*';

			var found = new Array;
			var foundCount = 0;
			for (var h = 0; h < currentContext.length; h++)
			{
				var elements;

				if (tagName == '*')
					elements = getAllChildren(currentContext[h]);
				else
					elements = currentContext[h].getElementsByTagName(tagName);

				for(var j = 0; j < elements.length; j++)
					found[foundCount++] = elements[j];
			}

			currentContext = new Array;
			var currentContextIndex = 0;
			var checkFunction;
			switch (attrOperator)
			{
				case '=':
					checkFunction = function(e) { return (e.getAttribute(attrName) == attrValue); };
					break;
				case '~':
					checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('\\b'+attrValue+'\\b'))); };
					break;
				case '|':
					checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('^'+attrValue+'-?'))); };
					break;
				case '^':
					checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) == 0); };
					break;
				case '$':
					checkFunction = function(e) { return (e.getAttribute(attrName).lastIndexOf(attrValue) == e.getAttribute(attrName).length - attrValue.length); };
					break;
				case '*':
					checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) > -1); };
					break;
				default :
					checkFunction = function(e) { return e.getAttribute(attrName); };
			}
			currentContext = new Array;
			var currentContextIndex = 0;
			for (var k = 0; k < found.length; k++)
			{
				if (checkFunction(found[k]))
					currentContext[currentContextIndex++] = found[k];
			}
			continue;
		}

		if (!currentContext[0])
			return;

		tagName = token;
		var found = new Array;
		var foundCount = 0;
		for (var h = 0; h < currentContext.length; h++)
		{
			var elements = currentContext[h].getElementsByTagName(tagName);
			for (var j = 0; j < elements.length; j++)
				found[foundCount++] = elements[j];
		}
		currentContext = found;
	}
	return currentContext;
}