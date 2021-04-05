import $ from "jquery";

export function fetchCommunes(element) {
    const value = element.value;
    const communeElt = $('.js-commune');
    const arrondissementElt = $('.js-arrondissement');
    const resultatElt = $('.js-resultat');
    const validateElt = $('.js-validate-btn');
    if (value) {
        $.ajax({
            url: `/async/communes/${element.value}/departement`,
            type: "POST",
            beforeSend: function () {
                communeElt.val(null);
                communeElt.html('<option value="">--</option>');
                arrondissementElt.val(null);
                arrondissementElt.html('<option value="">--</option>');
                resultatElt.addClass('d-none');
                validateElt.attr('disabled', 'disabled');
            },
            dataType: "json",
            data: {},
            async: true,
            error: function (error) {
                console.log(error);
            },
            success: function (data, status) {
                let html = '<option value="">-- sélectionner --</option> \n';
                data.forEach(function (commune) {
                    html += `<option value="${commune.id}">${commune.nom}</option>\n`;
                });
                $('.js-commune').html(html);
            }
        });
    } else {
        communeElt.val(null);
        communeElt.html('<option value="">--</option>');
        arrondissementElt.val(null);
        arrondissementElt.html('<option value="">--</option>');
        resultatElt.addClass('d-none');
        validateElt.attr('disabled', 'disabled');
    }
}

export function fetchArrondissements(element) {
    const value = element.value;
    const arrondissementElt = $('.js-arrondissement');
    const resultatElt = $('.js-resultat');
    const validateElt = $('.js-validate-btn');
    if (value) {
        $.ajax({
            url: `/async/arrondissements/${element.value}/commune`,
            type: "POST",
            beforeSend: function () {
                arrondissementElt.val(null);
                arrondissementElt.html('<option value="">--</option>');
                resultatElt.addClass('d-none');
                validateElt.attr('disabled', 'disabled');
            },
            dataType: "json",
            data: {},
            async: true,
            error: function (error) {
                console.log(error);
            },
            success: function (data, status) {
                console.log(data)
                let html = '<option value="">-- sélectionner --</option> \n';
                data.forEach(function (commune) {
                    html += `<option value="${commune.id}">${commune.nom}</option> \n`;
                });
                $('.js-arrondissement').html(html);
            }
        });
    } else {
        arrondissementElt.val(null);
        arrondissementElt.html('<option value="">--</option>');
        resultatElt.addClass('d-none');
        validateElt.attr('disabled', 'disabled');
    }
}

export function showResultat(element) {
    const value = element.value;
    const resultatElt = $('.js-resultat');
    const validateElt = $('.js-validate-btn');
    if (value) {
        resultatElt.removeClass('d-none');
        validateElt.removeAttr('disabled');
    } else {
        resultatElt.addClass('d-none');
        validateElt.attr('disabled', 'disabled');
    }
}
