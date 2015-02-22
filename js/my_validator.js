function onlyNumbers(evt)
{
	var e = event || evt; // for trans-browser compatibility
	var charCode = e.which || e.keyCode;

	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;

	return true;

}

function numonly(root){
    var reet = root.value;    
    var arr1=reet.length;      
    var ruut = reet.charAt(arr1-1);   
        if (reet.length > 0){   
        var regex = /[0-9]|\./;   
            if (!ruut.match(regex)){   
            var reet = reet.slice(0, -1);   
            $(root).val(reet);   
            }   
        }  
 }

