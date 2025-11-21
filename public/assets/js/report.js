    function print_table() {
	    document.getElementById("header").style.display = 'none';
	    document.getElementById("right").style.display = 'none';
        document.getElementById("main-footer").style.display = 'none';
	    document.getElementById("content-main").style.position= 'sticky';
	    document.getElementById("content-main").style.left= '0';
	    document.getElementById("content-main").style.width = '100%';
	    window.print();
	    document.getElementById("header").style.display = 'block';
	    document.getElementById("right").style.display = 'block';
        document.getElementById("main-footer").style.display = 'block';
	    document.getElementById("content-main").style.position= 'relative';
	    document.getElementById("content-main").style.width = '80%';
	}	