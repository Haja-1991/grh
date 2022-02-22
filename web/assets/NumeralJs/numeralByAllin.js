// load a locale
numeral.register('locale', 'fr', {
    delimiters: {
        thousands: ' ',
        decimal: '.'
    },
    abbreviations: {
        thousand: 'k',
        million: 'm',
        billion: 'b',
        trillion: 't'
    },
    ordinal : function (number) {
        return number === 1 ? 'er' : 'ème';
    },
    currency: {
        symbol: '€'
    }
});

// switch between locales
numeral.locale('fr');

var myFormat = '0,0.00';

function vraiFormat(option){
    var montant = $(option).val();

    if(montant !== ''){
        montant = numeral(montant);

        $(option).val(montant.format('0.00'));
    }
}

function affichageFormat(option) {
    var montant = $(option).val();

    if(montant === ""){
        $(option).val("");
        return true;
    }

    montant = numeral(montant);
    $(option).val(montant.format(myFormat));
}

function numeralFormat(input) {
    $(input).focusout(function () {
        affichageFormat(input);
    });

    $(input).init(function () {
        affichageFormat(input);
    });

    $(input).focusin(function () {
        vraiFormat(input);

        montant = $(input).val();
        if ( montant % 1 === 0 && montant !== "" ){
            montant = numeral(montant);
            $(input).val(montant.format('0'));

        }

    });

    inputForm = $(input).closest("form");

    inputForm.submit(function () {
        vraiFormat(input)
    });
}