var all_scripts,script_index,last_script,queryString,URLparams;

//inject script tags to define YB object
function injectGlobalObject(callback) {
	var init_script = document.createElement('script');
	init_script.setAttribute('type','text/javascript');	
	//var you_bar = 'var YB = {};\n'   -   this code does not work for IE compatibility view
	//var you_bar_node = document.createTextNode(you_bar);   -   this code does not work for IE compatibility view
	init_script.text = 'var YB = {};\n';
	//init_script.appendChild(you_bar_node);  -   this code does not work for IE compatibility view
	document.body.appendChild(init_script);
	callback();
}

//Grab all params from the injected myinfotopia.js
function getParams(){
	all_scripts = document.getElementsByTagName('script');
	script_index = all_scripts.length - 2;
	last_script = all_scripts[script_index];
	queryString = last_script.src.replace(/^[^\?]+\??/,'');
}

function parseQuery (query, callback) {
	var Params = {};
	if (!query) return Params; // return empty object
	var Pairs = query.split(/[;&]/);
	for ( var i = 0; i < Pairs.length; i++ ) {
		if (Pairs[i].match(/config/)) {
			var KeyVal = Pairs[i].split('=');
	
			if (!KeyVal || KeyVal.length != 2) continue;
			var key = unescape(KeyVal[0]);
			var val = unescape(KeyVal[1]);
			val = val.replace(/\+/g, ' ');
			Params[key] = val;
		}
	}
	URLparams = Params;
  
  // Set defaults
  if (!URLparams.hasOwnProperty('config_nrurl')) {
    URLparams.config_nrurl = "//app.you-bar.com";
  }
  if (!URLparams.hasOwnProperty('config_feurl')) {
    URLparams.config_feurl = "//js.you-bar.com";
  }
  if (!URLparams.hasOwnProperty('config_app')) {
    URLparams.config_app = "default";
  }
  
	callback();
}
function writeObject(callback){
	//write out global object
	YB.nrurl = URLparams.config_nrurl;
	YB.feurl = URLparams.config_feurl;
	YB.app = URLparams.config_app;
	callback();
}

function loadOpt() {

	var script_src = 'myinfotopia-opt.js',
		script = document.createElement('script');
	script.setAttribute('type','text/javascript');	

	//local override
	if (typeof is_yb_local !== "undefined")
		script.setAttribute('src', 'http://toolbar.local:3001/' + script_src);
	else
		script.setAttribute('src', URLparams.config_feurl + '/' + script_src);

	document.body.appendChild(script);
}
injectGlobalObject(function(){
	getParams();
	parseQuery(queryString, function(){
		writeObject(function(){
			loadOpt();
		});
	});
});
