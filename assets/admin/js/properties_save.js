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


// setup an "add a propertyAttribute" link
let $addPropertyAttributeButton = $('<a class="add_property_attribute_link btn btn-success"><span class="oi oi-plus" aria-hidden="true"></span>Add an attribute</a>');
let $newLinkLi = $('<li></li>').attr({'class': 'text-right'}).append($addPropertyAttributeButton);

$(document).ready(function() {
    bindAddAttribute();




    // add the "add a propertyAttribute" anchor and li to the propertyAttributes ul
    //$collectionHolder.prepend($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    // $collectionHolder.data('index', $collectionHolder.find('select').length);

    // Make all the selectors readonly to avoid type change
    $('select.property_attribute_selector').attr({'disabled': true});

    // $addPropertyAttributeButton.on('click', function(e) {
    //     // add a new propertyAttribute form (see next code block)
    //     addPropertyAttributeForm($collectionHolder, $newLinkLi);
    //
    //     $collectionHolder.find('select.property_attribute_selector').each(function() {
    //         console.log('binding ok')
    //         $(this).off('change').on('change', function() {
    //             displayRightFieldType($(this));
    //         });
    //     });
    // });

    // add a delete link to all of the existing tag form li elements
    $('ul.property-attributes').find('li.property-attribute-container').each(function() {
        addPropertyAttributeFormDeleteLink($(this));
    });
});

function bindAddAttribute() {
    console.log('bind it')

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
                console.log($collectionHolder)
                // get the new index
                let index = $collectionHolder.data('index');

                console.log(index)

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
                $collectionHolder.attr('data-index', index+1);
                addPropertyAttributeFormDeleteLink($newFormLi);
            })
            .fail(function(jqXHR, textStatus) {
                $('.add_property_attribute_alert').html(jqXHR.responseJSON.response).fadeIn().delay(2000).fadeOut();
            });

        bindAddAttribute();
    });
}

// function displayRightFieldType($selector) {
//     const $optionSelected = $selector.find(":selected");
//
//     const $fieldType = $optionSelected.data('type');
//     const $selectorContainer = $selector.closest('.property-attribute-container');
//     const $index = $selectorContainer.data('index');
//     console.log(`#property_propertyAttributes_${$index}_value`)
//     const $field = $selectorContainer.find(`#property_propertyAttributes_${$index}_value`);
//
//     console.log($fieldType)
//     if ($fieldType === 'string') {
//         if ($field.is("textarea")) {
//             // do nothing
//         }
//         if ($field.is("input")) {
//             const $attr = getFieldAttributes($field);
//             $field.replaceWith(
//                 $('<textarea></textarea>').attr($attr)
//             );
//         }
//         if ($field.is("select")) {
//             const $attr = getFieldAttributes($field);
//             $field.replaceWith(
//                 $('<textarea></textarea>').attr($attr)
//             );
//         }
//     }
//
//     if ($fieldType === 'numeric') {
//         console.log($field)
//         if ($field.is("input")) {
//             console.log('previously input')
//             // do nothing, just be sure the type is correct
//             $field.attr({'type': 'number'}).removeAttr('step');
//         }
//
//         if ($field.is("textarea")) {
//             console.log('previously textarea')
//             const $attr = getFieldAttributes($field);
//             $field.replaceWith(
//                 $('<input>').attr($attr).attr({'type': 'number'}).removeAttr('step')
//             );
//         }
//         if ($field.is("select")) {
//             console.log('previously select')
//             const $attr = getFieldAttributes($field);
//             $field.replaceWith(
//                 $('<input>').attr($attr).attr({'type': 'number'}).removeAttr('step')
//             );
//         }
//     }
//
//     if ($fieldType === 'amount') {
//         if ($field.is("input")) {
//             // do nothing, just be sure the type is correct
//             $field.attr({'type': 'number', 'step': '0.01'});
//         }
//
//         if ($field.is("textarea")) {
//             const $attr = getFieldAttributes($field);
//             $field.replaceWith(
//                 $('<input>').attr($attr).attr({'type': 'number', 'step': '0.01'})
//             );
//         }
//         if ($field.is("select")) {
//             const $attr = getFieldAttributes($field);
//             $field.replaceWith(
//                 $('<input>').attr($attr).attr({'type': 'number', 'step': '0.01'})
//             );
//         }
//     }
//
//     if ($fieldType === 'boolean') {
//         if ($field.is("input") && $field.attr('type') === 'radio') {
//             //do nothing
//         } else {
//             // @todo: find what to do
//         }
//     }
// }

// function getFieldAttributes($field) {
//     return {
//         // attributes to conserve
//         'id': $field.attr('id'),
//         'name': $field.attr('name'),
//         'required': $field.attr('required'),
//         'class': $field.attr('class'),
//     }
// }
//
// function addPropertyAttributeForm($collectionHolder, $newLinkLi) {
//     // Get the data-prototype explained earlier
//     let prototype = $collectionHolder.data('prototype');
//
//     // get the new index
//     let index = $collectionHolder.data('index');
//
//     let newForm = prototype;
//     // You need this only if you didn't set 'label' => false in your propertyAttributes field in TaskType
//     // Replace '__name__label__' in the prototype's HTML to
//     // instead be a number based on how many items we have
//     // newForm = newForm.replace(/__name__label__/g, index);
//
//     // Replace '__name__' in the prototype's HTML to
//     // instead be a number based on how many items we have
//     newForm = newForm.replace(/__name__/g, index);
//
//     // increase the index with one for the next item
//     $collectionHolder.data('index', index + 1);
//
//     // Display the form in the page in an li, before the "Add a propertyAttribute" link li
//     let $newFormLi = $('<li></li>').attr({
//         'class': 'property-attribute-container',
//         'data-index': index
//     }).append(newForm);
//     $newLinkLi.after($newFormLi);
//
//     addPropertyAttributeFormDeleteLink($newFormLi);
// }

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
