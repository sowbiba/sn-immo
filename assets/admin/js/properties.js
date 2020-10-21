/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import './app';

import '../css/properties.scss';

const routes = require('../../../public/fos_js_routes.json');
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

const $ = require('jquery');
console.log('Hello Webpack Encore! Edit me in assets/admin/js/properties.js');

$(document).ready(function() {
    bindAddAttribute();

    // Make all the selectors readonly to avoid type change
    $('select.property_attribute_selector').attr({'disabled': true});

    // add a delete link to all of the existing tag form li elements
    $('ul.property-attributes').find('li.property-attribute-container').each(function() {
        addPropertyAttributeFormDeleteLink($(this));
    });
});

function bindAddAttribute() {
    // Bind add attribute link
    $('.add_property_attribute_link').off('click').on('click', function (){
        const $propertyId = $(this).data('property-id');
        const $attributeId = $(this).closest('.add_property_attribute_container').find('select#add_attribute_selector').val();

        const $url = Routing.generate('properties_attribute_form', {'id': $propertyId, 'attributeId': $attributeId});

        $.ajax({
            method: "GET",
            url: $url,
        })
            .done(function(data) {
                // Get the ul that holds the collection of propertyAttributes
                let $collectionHolder = $('ul.property-attributes');
                // get the new index
                let index = $collectionHolder.data('index');

                let $newFormLi = $('<li></li>').attr({
                    'class': 'property-attribute-container',
                    'data-index': index
                }).html(data.response);

                let $selector = $newFormLi.find('select#form_attribute');
                $selector.attr({
                    'id': `property_propertyAttributes_${index}_attribute`,
                    'class': 'property_attribute_selector form-control',
                    'name': `property[propertyAttributes][${index}][attribute]`
                }).after(
                    $('<input>').attr({
                        'type': 'hidden',
                        'name': `property[propertyAttributes][${index}][attribute]`,
                        'id': `input_property_propertyAttributes_${index}_attribute`,
                        'value': $selector.val()
                    })
                );
                $newFormLi.find('input#input_form_attribute').remove();

                $newFormLi.find('label[for="form_attribute"]').attr({
                    'for': `property_propertyAttributes_${index}_attribute`
                });

                $newFormLi.find('#form_value').attr({
                    'id': `property_propertyAttributes_${index}_value`,
                    'name': `property[propertyAttributes][${index}][value]`
                });

                $newFormLi.find('label[for="form_value"]').attr({
                    'for': `property_propertyAttributes_${index}_value`
                });

                $('.add_property_attribute_container').after($newFormLi);
                $collectionHolder.data({'index': index+1});
                addPropertyAttributeFormDeleteLink($newFormLi);
            })
            .fail(function(jqXHR, textStatus) {
                $('.add_property_attribute_alert').html(jqXHR.responseJSON.response).fadeIn().delay(2000).fadeOut();
            });
    });
}

function addPropertyAttributeFormDeleteLink($propertyAttributeFormLi) {
    let $removeFormButton = $('<a class="btn btn-danger" title="Delete this attribute"><span class="oi oi-trash" aria-hidden="true"></span></a>');
    $propertyAttributeFormLi.find('table tbody')
        .prepend($('<tr>')
                .append($('<td>').attr({'colspan': 2, 'class': 'text-right'})
                    .append($removeFormButton)
                )
        );

    $removeFormButton.on('click', function(e) {
        // remove the li for the tag form
        $propertyAttributeFormLi.remove();
    });

    $removeFormButton.hover(
        function() {
            $(this).closest('li.property-attribute-container').addClass('hovered');
        }, function() {
            $(this).closest('li.property-attribute-container').removeClass('hovered');
        }
    );
}
