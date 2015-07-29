<?php
	include_once '../common.php';
	include_once '../libraries/unit.php';

    $template = json_decode(file_get_contents("php://input"),true);
	
	if(isset($template)){
		//echo '{"result":[{"id":"2","parent":"2","duration":"90","start":"5","type":"PLAYAFTER","classes":"layerShow","layerCSS":"","interaction":"QUIZ","items":[{"id":"9","parent":"2","y":"0.473198","x":"0.307692","height":"24","width":"192","expectedValue":"31,6","feedback":"","type":"2","caption":"CgkJCQkJCQkgI21hdGhzbGFuZyAzMSw2IAoJCQkJCQk=","classes":"transparentTextbox"}]},{"id":"3","parent":"2","duration":"90","start":"10","type":"PLAYAFTER","classes":"swipeFromLeft","layerCSS":"","interaction":"QUIZGAME","items":[{"id":"10","parent":"3","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCVp1csO8Y2sgenUgJDEzKzQyPTU1JC4gV2FzIGlzdCBkaWUgJDEzJCBkYXJpbj8gIyMgZWluIFN1bW1hbmQgIyMgZGllIFN1bW1lICMjIGRpZSBBZGRpdGlvbiAjIyBkaWUgUmVjaGVub3BlcmF0aW9uCgkJCQkJCQkKCQkJCQkJ","classes":""}]},{"id":"4","parent":"2","duration":"90","start":"15","type":"PLAYAFTER","classes":"swipeFromLeft","layerCSS":"","interaction":"QUIZGAME","items":[{"id":"11","parent":"4","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCSQxeyx9MiBcY2RvdCAzeyx9MDQ9XCw\/JCA8YnIgLz4gUmVjaG5lbiBTaWUgbmljaHQsIHNvbmRlcm4gc2NobGllw59lbiBTaWUgYWxsZSB1bm3DtmdsaWNoZW4gQW50d29ydGVuIGF1cy4gIyMgJDN7LH02NDgkICMjICQzNnssfTQ4JCAjIyAkM3ssfTY0NiQgIyMgJDB7LH0zNjQ4JAoJCQkJCQkJCgkJCQkJCQ==","classes":""},{"id":"12","parent":"4","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCVJlY2huZW4gU2llOiA8YnIgLz4kMHssfTIgXGNkb3QgMHssfTU9XCw\/JCAjIyAkMHssfTEkICMjICQweyx9MDEkICMjICQxJCAjIyAkMHssfTckCgkJCQkJCQkKCQkJCQkJ","classes":""},{"id":"13","parent":"4","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCVJlY2huZW4gU2llOjxiciAvPiQweyx9MiBcY2RvdCAweyx9NTE9XCw\/JCAjIyAkMHssfTEwMiQgIyMgJDF7LH0wMiQgIyMgJDB7LH0xMiQgIyMgJDEweyx9MiQKCQkJCQkJCQoJCQkJCQk=","classes":""}]},{"id":"5","parent":"2","duration":"90","start":"20","type":"PLAYAFTER","classes":"swipeFromLeft","layerCSS":"","interaction":"QUIZGAME","items":[{"id":"14","parent":"5","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCVdlbGNoZSBkZXIgZWluZ2VyYWhtdGVuIFphaGxlbiBpc3QgZWluIEZha3Rvcj8gIyMgJDIwXGNkb3QgXGZib3h7MTB9PTIwMCQgIyMgJDIwXGNkb3QgMTAgPSBcZmJveHsyMDB9JCAjIyAkMjA6IFxmYm94ezEwfT0yJCAjIyAkMjA6MTA9XGZib3h7Mn0kCgkJCQkJCQkKCQkJCQkJ","classes":""},{"id":"15","parent":"5","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCVdlbGNoZSBkZXIgZWluZ2VyYWhtdGVuIFphaGxlbiBpc3QgZWluIFN1bW1hbmQ\/ICMjICQyMCtcZmJveHsxMH0kICMjICQyMCsxMD1cZmJveHszMH0kICMjICQyMFxjZG90IFxmYm94ezEwfSQgIyMgJDIwLSBcZmJveHsxMH0kCgkJCQkJCQkKCQkJCQkJ","classes":""}]},{"id":"6","parent":"2","duration":"90","start":"267.229","type":"PLAYAFTER","classes":"layerShow","layerCSS":"","interaction":"QUIZ","items":[{"id":"16","parent":"6","y":"0.147937","x":"0.5568","height":"48","width":"120","expectedValue":"","feedback":"","type":"2","caption":"CgkJCQkJCQkgMCAKCQkJCQkJ","classes":"transparentTextbox"}]},{"id":"7","parent":"2","duration":"90","start":"345","type":"PLAYAFTER","classes":"layerShow","layerCSS":"","interaction":"QUIZ","items":[{"id":"17","parent":"7","y":"0.705548","x":"0.7552","height":"40","width":"64","expectedValue":"0","feedback":"","type":"2","caption":"CgkJCQkJCQkgMCAKCQkJCQkJ","classes":"transparentTextbox"}]},{"id":"8","parent":"2","duration":"90","start":"413","type":"PLAYAFTER","classes":"swipeFromLeft","layerCSS":"","interaction":"QUIZGAME","items":[{"id":"18","parent":"8","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCUltIEF1c2RydWNrICRccGkrNDIkIGlzdCBkaWUgWmFobCAkNDIkOiAjIyBlaW4gU3VtbWFuZCAjIyBkaWUgU3VtbWUgIyMgZGllIEFkZGl0aW9uICMjIGVpbiBGYWt0b3IKCQkJCQkJCQoJCQkJCQk=","classes":""},{"id":"19","parent":"8","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCVdpZSB2aWVsZSBTdGVsbGVuIG5hY2ggZGVtIEtvbW1hIGVyd2FydGV0IG1hbiBiZWltIEVyZ2VibmlzIHZvbiAkMTJ7LH0zNDUgXGNkb3QgMHssfTY3JD8gIyMgZsO8bmYgIyMgendlaSAjIyBkcmVpICMjIHNlY2hzCgkJCQkJCQkKCQkJCQkJ","classes":""},{"id":"20","parent":"8","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCUltIEF1c2RydWNrICRcZnJhY3thK2J9ezQyfSQgaXN0IGRlciBBdXNkcnVjayAkYStiJDogIyMgZGVyIFrDpGhsZXIgIyMgZWluIFByb2R1a3QgIyMgZGVyIFF1b3RpZW50ICMjIGRlciBOZW5uZXIKCQkJCQkJCQoJCQkJCQk=","classes":""},{"id":"21","parent":"8","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCUltIEF1c2RydWNrICRcZnJhY3tccGl9ezQyfSQgaXN0IGRpZSBaYWhsICRccGkkOiAjIyBkZXIgWsOkaGxlciAjIyBlaW4gUHJvZHVrdCAjIyBlaW4gRmFrdG9yICMjIGRlciBOZW5uZXIKCQkJCQkJCQoJCQkJCQk=","classes":""},{"id":"22","parent":"8","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCUbDvHIgd2VsY2hlIFphaGxlbiAkeCQgaXN0ICRcZnJhY3t4KzV9e3gtM31cY2RvdFxmcmFje3grM317eC01fSQgPGJyIC8+IDxzdHJvbmc+bmljaHQ8L3N0cm9uZz4gZGVmaW5pZXJ0PyAjIyBzb3dvaGwgJHggPSAzJDxiciAvPmFscyBhdWNoICR4ID0gNSQgIyMgJHggPSA4JCAjIyBzb3dvaGwgJHggPSAtMyQ8YnIgLz5hbHMgYXVjaCAkeCA9IC01JCAjIyAkeCA9IDMkCgkJCQkJCQkKCQkJCQkJ","classes":""},{"id":"23","parent":"8","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCUltIEF1c2RydWNrICQxM1xjZG90IDQyJCBpc3QgZGllIFphaGwgJDEzJDogIyMgZWluIEZha3RvciAjIyBlaW4gUHJvZHVrdCAjIyBkZXIgWsOkaGxlciAjIyBlaW4gU3VtbWFuZAoJCQkJCQkJCgkJCQkJCQ==","classes":""},{"id":"24","parent":"8","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCUltIEF1c2RydWNrICQxMys0Mj01NSQgaXN0IGRpZSBaYWhsICQ1NSQ6ICMjIGRpZSBTdW1tZSAjIyBlaW4gRmFrdG9yICMjIGRlciBaw6RobGVyICMjIGVpbiBTdW1tYW5kCgkJCQkJCQkKCQkJCQkJ","classes":""},{"id":"25","parent":"8","y":"0","x":"0","height":"0","width":"0","expectedValue":"","feedback":"","type":"","caption":"CgkJCQkJCQkKCQkJCQkJCUltIEF1c2RydWNrICQxM1xjZG90IDQyPTU0NiQgaXN0IGRpZSBaYWhsICQ1NDYkOiAjIyBkYXMgUHJvZHVrdCAjIyBlaW4gRmFrdG9yICMjIGRlciBaw6RobGVyICMjIGVpbiBTdW1tYW5kCgkJCQkJCQkKCQkJCQkJ","classes":""}]}]}';
		if(isset($template['deleted']) && $template['deleted']){
			deleteTemplate($template['id']);
		} else {
			createTemplate($template);
		}
	}
	fetchTemplates();

	
	function createTemplate($template){
		createLayer($template,TEMPLATE_ID());
	}
	
	function deleteTemplate($id){
		deleteLayer(intval($id));
	}
	
	function fetchTemplates(){
		echo '{"result":'.json_encode(getLayers(TEMPLATE_ID())).'}';
	}

	function TEMPLATE_ID(){return -1;}
?>