var $global_j;

var runUntil;

// ****  New Myinfotopia Script  ***
var extinvcode=3131;
var myinfo_opt=true;
var infotopia_app_domain = "";
if(typeof panelrun == 'undefined') { var panelrun=false; }

// Browser Detection [CB: 6/18/2013]
var N = navigator.appName, ua = navigator.userAgent, temp;
var M = ua.match(/(opera|chrome|safari|firefox|msie)\/?\s*(\.?\d+(\.\d+)*)/i);
if (M && (temp = ua.match(/version\/([\.\d]+)/i)) != null) M[2] = temp[1];
M = M? [M[1], M[2]]: [N, navigator.appVersion, '-?'];
var browserName = M[0];
var browserVersion = M[1];
var parts = browserVersion.split('.');
var browserVersionShort = parts[0] + '.' + parts[1];
var topBrowsers = ['Chrome 26.0','Chrome 27.0','Firefox 20.0','Firefox 21.0','MSIE 9.0','MSIE 10.0']; // Based on Verti
var isTopBrowser = false;
var browserFull = browserName + " " + browserVersionShort;
for (var i=0; i < topBrowsers.length; i++) {
  if (browserName + " " + browserVersionShort == topBrowsers[i]) {
    isTopBrowser = true;
  }
}

// Get Host
//var current_url = window.location.protocol + "://" + window.location.host + "/" + window.location.pathname;
var current_url = window.location;
var hostname = window.location.hostname.toLowerCase();
 
var topHost = hostname;
var s = getScriptElem();
var info_uid = getAdParameterByName('uid', s, 0);
var info_size = getAdParameterByName('size', s, 'all'); 
var info_partner = getAdParameterByName('pid', s, 1221823); 
var info_widgets = getAdParameterByName('widgets', s, 'none'); 
var info_layout = getAdParameterByName('layout', s, 'normal'); 
var info_subid = getAdParameterByName('subid', s); 
var info_delay = getAdParameterByName('delay', s);
var info_search = getAdParameterByName('search', s);
var info_url = getAdParameterByName('infourl', s, YB.feurl + "/info.html");
var serving_url = getAdParameterByName('servingurl', s);
var toolbar_url = getAdParameterByName('toolbarurl', s);

function getScriptElem() { 
  var scripts = document.getElementsByTagName('script'); 
  for(var i=0; i<scripts.length; i++) {  
    if(scripts[i].src.search("myinfotopia.js") > -1) { 
      return scripts[i];
    }
  }
  for(var i=0; i<scripts.length; i++) {  
    if(scripts[i].src.search("myinfotopia-opt.js") > -1) { 
      return scripts[i];
    }
  }
}
function getAdParameterByName(name, script, default_value) { 
  if(typeof default_value == 'undefined') {
    default_value = null;
  }
  var nameval = getParameterByName(name); 
  if(nameval) { 
    return nameval; 
  } else if(script) { 
    var nameval = getParameterByName(name, script.src); 
    if(nameval) { 
      return nameval; 
    }
  }
  return default_value; 
}
function getParameterByName(name, check_string) {
  check_string = typeof check_string !== 'undefined' ? check_string : window.location;
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(check_string);
  if(results == null) { return ""; } else { return decodeURIComponent(results[1].replace(/\+/g, " ")); }
}
(function() {
  //local override  
  var nrurl = (typeof is_yb_local !== "undefined") ? 'http://toolbar.local:3002' : YB.nrurl;
	infotopia_app_domain = nrurl;
  var nr_timing = Array();
function checkjQuery(){
  if (typeof jQuery === "undefined") {
    var script = document.createElement("SCRIPT");
    
    // Preserve previous non-ssl source for jQuery script, use google's for ssl
    if (window.location.protocol == 'https:') {
      script.src = '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js';
    } else {
      script.src = '//codeorigin.jquery.com/jquery-1.7.1.min.js';
    }
    script.type = 'text/javascript';
    document.body.appendChild(script);
   runUntil = setInterval(keepChecking, 100);
  } else {

  }
}
var checkReady = function(callback, bypass) { 
if (typeof jQuery == "function") { callback($); } else { window.setTimeout(function() { checkReady(callback); }, 100); } };
checkjQuery();
function keepChecking(){
    if (typeof jQuery !== "undefined") {
      clearTimeout(runUntil);
      $global_j = jQuery;
      initCheckReady();
    }
}
function initCheckReady(){
    checkReady(function($) {
      $global_j.get(nrurl+'/nr.php?app=Plugin&tr=Optimized&return=0', function(data) {
        nr_timing = data;
        var script = document.createElement('script'); 
        script.setAttribute('type',"text/javascript"); 
        script.innerHTML = nr_timing.head;
        document.body.appendChild(script);
      }, 'jsonp');   
      var runPanel = function() {
        if(panelrun == true) {
          return false;
        }
        panelrun=true;

        // Figure out domain stuff
    var s = getScriptElem();
    var scripthost = null;
        var aElem = $global_j('<a>');
    if (typeof(aElem.prop) != "undefined") {
      scripthost = aElem.prop('href', s.src).prop('hostname');
    } else {
      scripthost = YB.feurl;
    }
        urlParts = scripthost.split('.');
        if(urlParts.length > 2) {
          called_subdomain = urlParts[(urlParts.length-3)];
          called_domain = urlParts[(urlParts.length-2)];
          called_extension = urlParts[(urlParts.length-1)];       
          infotopia_domain = called_subdomain + '.' + called_domain + '.' + called_extension + '/';
        } else if(urlParts.length > 1) {
          called_domain = urlParts[(urlParts.length-2)];
          called_extension = urlParts[(urlParts.length-1)];
          infotopia_domain = called_domain + '.' + called_extension + '/';
        } else {
          called_domain = urlParts[0];        
          infotopia_domain = called_domain + '/';
        }     
        if (infotopia_domain.indexOf("http://") == -1) {
          infotopia_domain = "http://" + infotopia_domain;
        } 
        if(!serving_url || serving_url == 'undefined') { 
            serving_url = called_domain;
      
            if(called_extension) {
              serving_url += '.' + called_extension;
            }
        }
        if (window.location.protocol == "https:") { 
          infotopia_domain = infotopia_domain.replace("http://","https://"); 
        } 

        //local override
        serving_url = (typeof is_yb_local !== 'undefined') ? 'you-bar.com' : serving_url;
        //console.log(serving_url);

        info_url = unescape(info_url);
        if (!info_url || info_url == 'undefined' || info_url == "") info_url = YB.feurl + "/info.html"
        if (info_partner == "1213803") info_url = "http://pricepeep.net";
        if (info_partner == "1647136") info_url = "http://pricepeep.net";
        if (info_partner == "1571068") info_url = "http://www.wizebar.com/info.htm?logic=cpxyoubar";
        if (info_partner == "1598608") info_url = "http://www.tidynetwork.com";
        if (info_partner == "1638064") info_url = "http://www.we-care.com/why?src=cpx";
        if (info_partner == "1962469") info_url = YB.feurl + "/info2.html";
        if (info_partner == "1290280") info_url = YB.feurl + "/info2.html";
        if (info_partner == "1608229") info_url = YB.feurl + "/info2.html";
        if ((info_url.indexOf("http://") == -1) && (info_url.indexOf("https://") == -1)) {
          info_url = window.location.protocol + '//' + info_url;
        } 
        // Load the returned ad panel Script 

        //local override        
        //infotopia_domain = (typeof is_yb_local !== "undefined") ? 'http://toolbar.local:3001/' : infotopia_domain;
        infotopia_domain = (typeof is_yb_local !== "undefined") ? 'http://toolbar.local:3001/' : YB.feurl + "/";
        
        // AD THIS TO THE ABOVE CONDITION TO TEST WITH LOCALLY VIA PLUGIN:  || YB.feurl.match(/local/)
        info_uid = (typeof is_yb_local !== "undefined") ? "TEST" : info_uid;
		
		var js_url = nrurl + '/info_js.php?uid=' + info_uid + '&size=' + info_size + '&pid=' + info_partner + '&widgets=' + info_widgets;
        if(info_layout != 'normal') {          js_url += '&layout=' + info_layout;        }
        if(info_widgets == 'search') {         js_url += '&search=' + info_search;        }
        js_url += '&infourl=' + info_url + '&servingurl=' + serving_url + '&blisturl=' + topHost + '&opt=' + myinfo_opt;
        js_url += '&browser=' + browserFull; //[CB: 6/19/2013]
		js_url += '&current_url=' + current_url; //[CB: 6/19/2013]
		
		js_url += '&_=' + new Date().getTime() + Math.floor((Math.random()*100000)+1);
		
        var script = document.createElement('script'); 
        script.setAttribute('type','text/javascript'); 
        script.setAttribute('src',js_url); 
        document.body.appendChild(script); 
      }
      // Check Whitelist
      var hostParts = topHost.split('.');
      if (hostParts.length > 2) { 
          topHost = hostParts[1] + '.' + hostParts[2]; 
      }
      if(topHost == 'myinfotopia.com') {
          var a = document.createElement("a"); a.href=window.document.referrer;
          $global_j.get(YB.nrurl+'/wlt.php?domain='+a.hostname+'&uid='+info_uid+'&pid='+info_partner, function(data) {
            if(data.domain == 1) {
              runPanel();
            } else {
//              runRedirect(0,'&referrer='+a.hostname);
            }
          },'jsonp');
      } else {
          $global_j.get(YB.nrurl+'/wlt.php?domain='+window.location.hostname+'&uid='+info_uid+'&pid='+info_partner, function(data) {
              if(data.domain) { 
                  runPanel();
              } else if(topHost == 'myinfotopia.com' && window.document.referrer != 'js.myinfotopia.com') {
                  var a = document.createElement("a"); a.href=window.document.referrer;
                  $global_j.get(YB.nrurl+'/wlt.php?domain='+a.hostname+'&uid='+info_uid+'&pid='+info_partner, function(data) {
                    if(data.domain == 1) {
                      runPanel();
                    } else {
//                      runRedirect(0,'&referrer='+a.hostname);
                    }
                  },'jsonp');
              } else { 
//                runRedirect(0, '&referrer='+topHost); 

              }
          }, 'jsonp');
     }
  }); // end checkReady

}
//end initCheckReady
if (typeof jQuery !=="undefined"){
  $global_j = jQuery;
  //$global_j = jQuery.noConflict();
  initCheckReady();
}

})();
