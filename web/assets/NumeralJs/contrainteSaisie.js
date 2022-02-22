function remove_last_input(elm) {
    var val = $(elm).val();
    var cursorPos = elm.selectionStart;
    $(elm).val(	val.substr(0,cursorPos-1) +			// before cursor - 1
        val.substr(cursorPos,val.length)	// after  cursor
    );
    elm.selectionStart = cursorPos-1;				// replace the cursor at the right place
    elm.selectionEnd   = cursorPos-1;
}

function script_saisie(){
    $('.only_floats').keyup(function(){
        if(!$(this).val().match(/^\-?[0-9]*[\.,]?[0-9]*$/))	// numbers[.,]numbers
            remove_last_input(this);
    });

    $('.only_phone_number').keyup(function(){
        if(!$(this).val().match(/^\+?[0-9 ]*$/))	// +numbers or space
            remove_last_input(this);
    });
}

script_saisie();