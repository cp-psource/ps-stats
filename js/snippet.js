( function() {
	var cpstatsReq;
	try {
		cpstatsReq = new XMLHttpRequest();
		cpstatsReq.open( 'POST', cpstats_ajax.url, true );
		cpstatsReq.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded;' );
		cpstatsReq.send(
			'_ajax_nonce=' + cpstats_ajax.nonce +
			'&action=cpstats_track' +
			'&cpstats_referrer=' + encodeURIComponent( document.referrer ) +
			'&cpstats_target=' + encodeURIComponent( location.pathname + location.search )
		);
	} catch ( e ) {
	}
}() );
