var smile_data = '';
window.onload = function() {
	function receiveMessage(e){

		var origin = e.origin;
      
        // If request is from our domain then only process data
		if(origin.indexOf(window.location.host) >= 0)
		{
			var test_data = '',
				e_data = '';
			var e_data = e.data.replace(/\"/g,'');
			var pairs = e_data.split('&');
			var result = {};
			pairs.forEach(function(pair) {			
				pair = pair.split('=');
				if(result[pair[0]]){
					result[pair[0]] = result[pair[0]]+","+decodeURIComponent(pair[1]);
				} else {
					result[pair[0]] = decodeURIComponent(pair[1]);
				}
			});
			smile_data = result;
			jQuery(document).trigger('smile_data_received',[smile_data]);
		}
	}
	window.addEventListener('message',receiveMessage);
}