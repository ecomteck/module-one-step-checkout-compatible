/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'MageWorx_MultiFees/js/view/shipping-fee',
    'Magento_Checkout/js/action/get-totals'
], function ($,ShippingFee,getTotalsAction) {
    'use strict';
    $(document).ajaxSuccess(function(event, xhr, settings) {
        if ( settings.url.indexOf("multifees/checkout/fee") >=0) {
            getTotalsAction(function(){});
        }
    });
    return ShippingFee.extend({
        /**
         * Update shipping fees by selected shipping method
         *
         * @param code
         * @returns {exports}
         */
        updateContent: function (code) {
            var content = this.reloadData(code),
                self = this,
                fieldset = self.getChild('mageworx-shipping-fee-form-fieldset');

            content.done(function (components) {
                fieldset = self.getChild('mageworx-shipping-fee-form-fieldset');
                // Before we do update elements in the from we are destroying the old ones
                if (fieldset.elems) {
                    fieldset.destroyChildren();
                }

                // When components are updated we should check is form should be visible
                // or not (empty elements or just hidden inputs)
                var visibleComponents = _.isEmpty(components) ?
                    [] :
                    components.filter(function (component) {
                        return component.isVisibleInputType;
                    }),
                    wholeFormVisibility = !_.isEmpty(visibleComponents);

                self.shouldShow(wholeFormVisibility);
                self.childCandidates = components;
                _.forEach(components, function (o, i) {
                    o.index = i;
                    components[i] = require(o.component)(_.extend(o, o.config));
                });
                fieldset.insertChild(components);
            });

            return this;
        }
    });
});

