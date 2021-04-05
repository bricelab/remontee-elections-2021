/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import $ from 'jquery';
import 'bootstrap';
import {fetchArrondissements, fetchCommunes, showResultat} from './js/mixins';

// start the Stimulus application
// import './bootstrap';

$(document).ready(function () {
    $('.js-departement').change(function () {
        const self = this;
        fetchCommunes(self);
    });
    $('.js-commune').change(function () {
        const self = this;
        const update = !!window.updateResult;
        fetchArrondissements(self, update)
    });
    $('.js-arrondissement').change(function () {
        const self = this;
        showResultat(self)
    });
});
