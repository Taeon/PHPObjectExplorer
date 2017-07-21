PHPObjectExplorer = {
	Ready:function() {
	  if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
		this.Init();
	  } else {
		document.addEventListener('DOMContentLoaded', this.Init.bind(this));
	  }
  },
	Init:function(){
		var entities = document.querySelectorAll('.entity .title');
		for( var e = 0; e < entities.length; e++ ){
			entities[ e ].addEventListener('click', this.EntityClick.bind(this));
		}
	},
	EntityClick:function( event ){
		var entity = this.Closest( event.target, '.entity' );
		this.ToggleClass( entity, 'show' );
		var current = document.querySelector( '.entity.current' );
		if( current != null ){
			this.RemoveClass( current, 'current' );
		}
		this.AddClass( entity, 'current' );
	},
	// http://youmightnotneedjquery.com/
	ToggleClass:function( element, className ){
		if (element.classList) {
		  element.classList.toggle(className);
		} else {
		  var classes = element.className.split(' ');
		  var existingIndex = classes.indexOf(className);

		  if (existingIndex >= 0)
			classes.splice(existingIndex, 1);
		  else
			classes.push(className);

		  element.className = classes.join(' ');
		}
	},
	AddClass:function( element, className ){
		if (element.classList)
		  element.classList.add(className);
		else
		  element.className += ' ' + className;
	},
	RemoveClass:function( element, className ){
		if (element.classList)
		  element.classList.remove(className);
		else
		  element.className = element.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
	},
	// https://stackoverflow.com/questions/18663941/finding-closest-element-without-jquery
	Closest:function(el, selector) {
		var matchesFn;

		// find vendor prefix
		['matches','webkitMatchesSelector','mozMatchesSelector','msMatchesSelector','oMatchesSelector'].some(function(fn) {
			if (typeof document.body[fn] == 'function') {
				matchesFn = fn;
				return true;
			}
			return false;
		})

		var parent;

		// traverse parents
		while (el) {
			parent = el.parentElement;
			if (parent && parent[matchesFn](selector)) {
				return parent;
			}
			el = parent;
		}

		return null;
	}
};
