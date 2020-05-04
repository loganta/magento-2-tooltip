/**
 * @category SM
 * @package SM_Coachmarks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'mage/url',
    'mage/translate'
], function ($, Component, confirmation, urlBuilder) {
    'use strict';

    return Component.extend({
        /**
         * init function
         */
        initialize: function (config) {
            var self = this;

            var topic = config.topic,
                topicId = '';
            //filter topic Id current allow running
            $.each(topic, function (i, topicCollect) {
                $.each(topicCollect.items, function (topicKey, topicVal) {
                    topicId = topicVal.topic_id;
                });
            });
            //call request action
            $.ajax(
                {
                    url: urlBuilder.build("coachmarks/customer/allowaction"),
                    type: "post",
                    dataType: "json",
                    data: {topicId: topicId},
                    showLoader: true,
                    success: function (result) {
                        if (!$.isEmptyObject(result)) {
                            var response = result.response;
                            /**get result response**/
                            if (response) {
                                self.__canAction(config)
                            }
                        }
                    }
                }
            );
        },

        /**
         * filter tooltip can find element real
         */
        __filterTooltip: function (tooltip) {
            //filter element target tooltip can action
            $.each(tooltip, function (i, tooltipCollect) {
                $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                    if (tooltipVal.tooltip_id !== '') {
                        if(tooltipVal.find_type === 'CLASS' && $(document).find("." + tooltipVal.class_element_html).length === 0){
                            tooltipCollect.items[tooltipKey] = null;
                        }

                        if(tooltipVal.find_type === 'ID' && $(document).find("#" + tooltipVal.id_element_html).length === 0){
                            tooltipCollect.items[tooltipKey] = null;
                        }
                    }
                });
            });

            return tooltip;
        },

        /**
         * __can Action
         */
        __canAction: function (config) {
            var self = this;
            var topic = config.topic,
                tooltip = '',
                topicId = '',
                toolTipActive = false,
                containerBody = $(document).find("[data-container=body]"),
                elTooltipActive = $("[toolTipActive=true]"),
                pageWrapper = $('.page-wrapper'),
                finishTooltip = false;

            /*filter tooltip can find element real*/
            tooltip = self.__filterTooltip(config.tooltip);
            /*run tooltip when page loaded*/
            if (containerBody && topic && tooltip) {
                $(document).ready(function () {
                    /**load topic available for page and make the first tooltip item of first topic is item active**/
                    self.__getTooltipAvailable(topic, tooltip, topicId, toolTipActive);
                    /**set position and fill value for tooltip active**/
                    if ($("[toolTipActive=true]").length > 0) {
                        /**add body modal marks**/
                        containerBody.append('<div class="coachmarks-modals-overlay" id="coachmarks-modals-overlay"></div>');
                        pageWrapper.after(self.templateTooltipDefault());

                        self.setPositionForTooltipActive(tooltip);
                        self.fillDataTopicTooltipActive(tooltip, toolTipActive = true, -1);
                        self.fillDataTooltipStepTopicActive(topic, tooltip, toolTipActive = true);
                    }
                    /**check first step to custom template show tooltip**/
                    var isSingleTooltipActive = self.__isSingleTooltipActive(tooltip),
                        stepFirstNext = 0;
                    if (isSingleTooltipActive) {
                        //close tooltip
                        self.__asFinishTooltipTemplate(stepFirstNext, false);
                        finishTooltip = true;
                    }
                });

                /**next step**/
                $('body').on('click', '#tooltip-bt-next', function (event) {
                    var currentTopicIdRunning = $("[toolTipActive=true]").attr('data-topicid'),
                        currentTooltipIdRunning = $("[toolTipActive=true]").attr('data-tooltipid'),
                        currentStepRunning = $("[toolTipActive=true]").attr('data-tooltip-step'),
                        stepNext = parseInt(currentStepRunning) + 1;
                    //push event GTM
                    self.__actionPushGTM(0, stepNext, "coachmark_click");
                    self.__actionPushGTM(0, stepNext, "coachmark_load");

                    var tooltipStepEnd = false,
                        currentTooltipIdNext = $('[data-tooltip-step=' + stepNext + ']'),
                        dataTopicIdTooltipNext = $('[data-tooltip-step=' + stepNext + ']').attr('data-topicid');

                    var elTooltipActiveNext = $("[toolTipActive=true]"),
                        containerBody = $(document).find("[data-container=body]"),
                        templateTooltipDefault = $('#coach-marks-topic');

                    /** can not find tooltip next step*/
                    if (currentTooltipIdNext.length === 0 && dataTopicIdTooltipNext === undefined) {
                        //close tooltip
                        self.__asFinishTooltipTemplate(stepNext, false);
                        finishTooltip = true;
                    }

                    if (currentTooltipIdNext.length > 0 && dataTopicIdTooltipNext !== undefined
                        && (parseInt(dataTopicIdTooltipNext) === parseInt(currentTopicIdRunning))) {
                        /**next step tooltip of current topic**/
                        $("[toolTipActive=true]").removeAttr("toolTipActive");
                        currentTooltipIdNext.attr("toolTipActive", true);

                        //set position and fill value for tooltip active
                        if (elTooltipActiveNext) {
                            self.setPositionForTooltipActive(tooltip);
                            self.fillDataTopicTooltipActive(tooltip, toolTipActive = true, currentTooltipIdRunning);
                            //close tooltip
                            self.__asFinishTooltipTemplate(stepNext, false);
                            finishTooltip = true;
                        }
                        //end tooltip step end
                        tooltipStepEnd = true;
                    } else {
                        /**next tooltip of next topic**/
                        var currentTopicIdRunning = $("[toolTipActive=true]").attr('data-topicid'),
                            currentTooltipIdRunning = $("[toolTipActive=true]").attr('data-tooltipid'),
                            currentTopicStepRunning = $("[toolTipActive=true]").attr('data-step-topic'),
                            stepTopicNext = parseInt(currentTopicStepRunning) + 1,
                            topicStepEnd = false,
                            topicStepIdNext = $('[data-step-topic=' + stepTopicNext + ']'),
                            elTooltipActiveNext = $("[toolTipActive=true]");
                        $("[toolTipActive=true]").removeAttr("toolTipActive");
                        topicStepIdNext.attr("toolTipActive", true);

                        //set position and fill value for tooltip active
                        if (elTooltipActiveNext && topicStepIdNext.length > 0) {
                            self.setPositionForTooltipActive(tooltip);
                            self.fillDataTopicTooltipActive(tooltip, toolTipActive = true, currentTooltipIdRunning);
                            //close tooltip
                            self.__asFinishTooltipTemplate(false, stepTopicNext);
                            finishTooltip = true;
                        } else {
                            //close tooltip
                            // self.__asFinishTooltipTemplate(false, stepTopicNext);
                            // finishTooltip = true;
                        }
                    }
                });
                /**finish**/
                //case tooltip position top
                $('.tooltip').on('click', '#tooltip-bt-finish', function (event) {
                    event.preventDefault();
                    self.__closeTooltip();
                    //push data to GTM
                    var currentStepRunning = $("[toolTipActive=true]").attr('data-tooltip-step'),
                        stepNext = parseInt(currentStepRunning) + 1;
                    self.__actionPushGTM(0, stepNext, 'coachmark_click');
                });
                //case tooltip position bottom
                $('.tooltip-bottom').on('click', '#tooltip-bt-finish', function (event) {
                    event.preventDefault();
                    self.__closeTooltip();
                    //push data to GTM
                    var currentStepRunning = $("[toolTipActive=true]").attr('data-tooltip-step'),
                        stepNext = parseInt(currentStepRunning) + 1;
                    self.__actionPushGTM(0, stepNext, "coachmark_click");
                });
            }
        },

        /**
         * as finish tooltip template
         */
        __asFinishTooltipTemplate: function (stepTooltipNext, stepTopicNext) {
            //case step topic
            if (stepTopicNext && $('[data-step-topic=' + parseInt(stepTopicNext + 1) + ']').length < 1) {
                $('#tooltip-bt-next').hide();
                $('#tooltip-bt-finish').show();
            }
            //case step tooltip
            if (stepTooltipNext !== '' && $('[data-tooltip-step=' + parseInt(stepTooltipNext + 1) + ']').length < 1) {
                $('#tooltip-bt-next').hide();
                $('#tooltip-bt-finish').show();
            }
        },

        /**
         * close to end tooltip
         */
        __closeTooltip: function () {
            var templateTooltipDefault = $('#coach-marks-topic'),
                topicId = $("[toolTipActive=true]").attr('data-topicid');
            if(topicId === undefined){
                topicId = $(".tooltip").attr('data-topicid');
            }
            $.ajax(
                {
                    url: urlBuilder.build("coachmarks/customer/saveattrcoachmark"),
                    type: "post",
                    dataType: "json",
                    data: {topicId: topicId},
                    showLoader: true,
                    success: function (result) {
                        if (!$.isEmptyObject(result)) {
                            var response = result.response;
                            /**remove attr html and modals overlay**/
                            if (response) {
                                $("[toolTipActive=true]").removeAttr("toolTipActive");
                                $('#coachmarks-modals-overlay').hide();
                                templateTooltipDefault.hide();
                            }
                        }
                    }
                }
            );
        },

        /**
         * set Position For Tooltip Active
         */
        setPositionForTooltipActive: function (tooltip) {
            var tooltipPosition = $("[toolTipActive=true]").offset(),
                coachMarksTopic = $('div.coach-marks-topic'),
                divTooltipTop = coachMarksTopic.find('.tooltip'),
                divTooltipBottom = coachMarksTopic.find('.tooltip-bottom'),
                tooltipTopPoint = coachMarksTopic.find('.tooltip-top-pointer'),
                tooltipBottomPoint = coachMarksTopic.find('.tooltip-bottom-pointer');

            if (tooltipPosition.top < 150) {
                var top = parseInt(tooltipPosition.top) + parseInt($("[toolTipActive=true]").outerHeight());
                //case show tooltip bottom position
                if (divTooltipTop.length > 0) {
                    divTooltipTop.addClass('tooltip-bottom');
                    divTooltipTop.removeClass('tooltip');
                    tooltipTopPoint.hide();
                    tooltipBottomPoint.show();
                }
                // control pointer for bottom position
                if(parseInt(tooltipPosition.left) < 200){
                    tooltipBottomPoint.css('left', '15%');
                }
                coachMarksTopic.css('top', top);
                if(parseInt(tooltipPosition.left + divTooltipBottom.width()) > parseInt($(window).width())){
                    coachMarksTopic.css('left', parseInt(tooltipPosition.left - divTooltipBottom.width()/2));
                    tooltipBottomPoint.css('left', '85%');
                }else{
                    coachMarksTopic.css('left', tooltipPosition.left);
                }
            } else {
                //case show tooltip top position
                if (divTooltipBottom.length > 0) {
                    divTooltipBottom.addClass('tooltip');
                    divTooltipBottom.removeClass('tooltip-bottom');
                    tooltipBottomPoint.hide();
                    tooltipTopPoint.show();
                }
                // control pointer for top position
                if(parseInt(tooltipPosition.left) < 200){
                    tooltipTopPoint.css('left', '15%');
                }
                coachMarksTopic.css('top', tooltipPosition.top);
                if(parseInt(tooltipPosition.left + divTooltipTop.width()) > parseInt($(window).width())){
                    coachMarksTopic.css('left', parseInt(tooltipPosition.left - divTooltipTop.width()/2));
                    tooltipTopPoint.css('left', '85%');
                }else{
                    coachMarksTopic.css('left', tooltipPosition.left);
                }
            }
        },

        /**
         * template Tooltip Default
         */
        templateTooltipDefault: function () {
            var html = '';
            html = '<div class="coach-marks" id="coach-marks">' +
                '<div class="coach-marks-topic" id="coach-marks-topic">' +
                '        <div class="tooltip">' +
                '            <div id="tooltip_content">' +
                '                <div id="tool-content"></div>' +
                '                <div id="tool-next"><button type="button" title="Next" class="action submit primary" id="tooltip-bt-next">Next</button></div>' +
                '                <div id="tool-finish"><button type="button" title="Finish" class="action submit primary" id="tooltip-bt-finish">Finish</button></div>' +
                '                <div id="tool-step"></div>' +
                '            </div>' +
                '            <div class="tooltip-top-pointer"></div>' +
                '            <div class="tooltip-bottom-pointer" style="display: none;"></div>' +
                '        </div>' +
                '        </div>' +
                '    </div>';

            return html;
        },

        /**
         * get Tooltips Available
         */
        __getTooltipAvailable: function (topic, tooltip, topicId, toolTipActive) {
            var self = this;
            //load topic available for page
            $.each(topic, function (i, topicCollect) {
                $.each(topicCollect.items, function (topicKey, topicVal) {
                    topicId = topicVal.topic_id;
                    $.each(tooltip, function (i, tooltipCollect) {
                        $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                            if (tooltipVal !== null && tooltipVal.topic_id === topicId) {
                                if (!toolTipActive) {
                                    if(tooltipVal.find_type === 'CLASS'){
                                        $('.' + tooltipVal.class_element_html).attr("toolTipActive", true);
                                        self.actionFillTooltipAttributeHTML(topicKey, tooltipKey, tooltipVal);
                                        toolTipActive = true;
                                    }else{
                                        $('#' + tooltipVal.id_element_html).attr("toolTipActive", true);
                                        self.actionFillTooltipAttributeHTML(topicKey, tooltipKey, tooltipVal);
                                        toolTipActive = true;
                                    }
                                } else {
                                    self.actionFillTooltipAttributeHTML(topicKey, tooltipKey, tooltipVal);
                                }
                            }
                        });
                    });
                });
            });
        },

        /**
         * action Fill ToolTip Attribute Element HTML
         */
        actionFillTooltipAttributeHTML: function (topicKey, tooltipKey, tooltipVal) {
            if(tooltipVal.find_type === 'CLASS'){
                //case find element setup tooltip tyle class
                $('.' + tooltipVal.class_element_html).attr("data-topicId", tooltipVal.topic_id);
                $('.' + tooltipVal.class_element_html).attr("data-step-topic", topicKey);
                $('.' + tooltipVal.class_element_html).attr("data-tooltipId", tooltipVal.tooltip_id);
                $('.' + tooltipVal.class_element_html).attr("data-tooltip-step", tooltipKey);
                $('.' + tooltipVal.class_element_html).attr("data-sortOrder", tooltipVal.sort_order);
                //check attribute class exist
                if ($('.' + tooltipVal.class_element_html).attr("class") !== '') {
                    $('.' + tooltipVal.class_element_html).addClass('tooltip');
                } else {
                    $('.' + tooltipVal.class_element_html).attr("class", 'tooltip');
                }
                $('.' + tooltipVal.class_element_html).attr("data-tooltip-content", '#tooltip_content');
            }else{
                //case find element setup tooltip tyle id
                $('#' + tooltipVal.id_element_html).attr("data-topicId", tooltipVal.topic_id);
                $('#' + tooltipVal.id_element_html).attr("data-step-topic", topicKey);
                $('#' + tooltipVal.id_element_html).attr("data-tooltipId", tooltipVal.tooltip_id);
                $('#' + tooltipVal.id_element_html).attr("data-tooltip-step", tooltipKey);
                $('#' + tooltipVal.id_element_html).attr("data-sortOrder", tooltipVal.sort_order);
                //check attribute class exist
                if ($('#' + tooltipVal.id_element_html).attr("class") !== '') {
                    $('#' + tooltipVal.id_element_html).addClass('tooltip');
                } else {
                    $('#' + tooltipVal.id_element_html).attr("class", 'tooltip');
                }
                $('#' + tooltipVal.id_element_html).attr("data-tooltip-content", '#tooltip_content');
            }
        },

        /**
         * fill Data Topic Tooltip Active
         */
        fillDataTopicTooltipActive: function (tooltip, toolTipActive, currentTooltipIdRunning) {
            var self = this;
            var tooltipActiveId = $("[toolTipActive=true]").attr('data-tooltipId'),
                tooltipActiveStep = $("[toolTipActive=true]").attr('data-tooltip-step');
            //load topic available for page
            $.each(tooltip, function (i, tooltipCollect) {
                $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                    if (tooltipVal !== null && tooltipVal.tooltip_id === tooltipActiveId && toolTipActive === true) {
                        self.__actionFillDataTooltipActive(tooltipKey, tooltipVal.content, tooltipVal.sort_order);
                    }
                });
            });

            //make high light for the active note
            if (currentTooltipIdRunning !== -1) {
                $('span.tooltip_step_' + parseInt(currentTooltipIdRunning - 1)).removeClass('make-note-highligh');
            }
            $('span.tooltip_step_' + parseInt(tooltipActiveStep - 1)).removeClass('make-note-highligh');
            $('span.tooltip_step_' + tooltipActiveStep).addClass('make-note-highligh');
        },

        /**
         * action Fill Data ToolTip Active
         */
        __actionFillDataTooltipActive: function (tooltipKey, tooltipValContent, tooltipValSortOrder) {
            var toolTipContent = $('#tool-content');

            toolTipContent.empty();
            toolTipContent.append(tooltipValContent);
        },

        /**
         * fill Data Tooltip Step Topic Active
         */
        fillDataTooltipStepTopicActive: function (topic, tooltip, toolTipActive) {
            var self = this;
            var html = '',
                toolTipStep = $('#tool-step'),
                topicId = '',
                firstStep = true,
                stepNext = false;

            $.each(topic, function (i, topicCollect) {
                $.each(topicCollect.items, function (topicKey, topicVal) {
                    topicId = topicVal.topic_id;
                    $.each(tooltip, function (i, tooltipCollect) {
                        $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                            if (tooltipVal !== null && tooltipVal.topic_id === topicId && toolTipActive) {
                                if (firstStep) {
                                    //make high light for the first tooltip active node
                                    html += '<span class="tooltip_step_' + tooltipKey + ' make-note-highligh">&nbsp;</span>';
                                    firstStep = false;
                                    self.__actionPushGTM(1, stepNext, "coachmark_load");
                                } else {
                                    html += '<span class="tooltip_step_' + tooltipKey + '">&nbsp;</span>';
                                }
                            }
                        });
                    });
                });
            });

            toolTipStep.prepend(html);
        },

        /**
         * __is Single Tooltip Active
         */
        __isSingleTooltipActive: function (tooltip) {
            var isSingle = false,
                countTooltip = 0;
            //load tooltip available for page
            $.each(tooltip, function (i, tooltipCollect) {
                $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                    if (tooltipVal !== null && tooltipVal.tooltip_id !== '') {
                        countTooltip = countTooltip + 1;
                    }
                });
            });

            if (countTooltip > 1) {
                return false;
            }

            return true;
        },

        /**
         * action push data google task manager
         */
        __actionPushGTM: function (coachmark_step, stepNext, event) {
            if (typeof dataLayerSourceObjects !== 'undefined' && dataLayerSourceObjects.customer.loginType !== "null") {
                var step = "Step 1";
                dataLayer = window.dataLayer || [];
                if(coachmark_step !== 1){
                    step = "Step" + stepNext;
                }
                dataLayer.push({
                    "event": event,
                    'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                    'userID': dataLayerSourceObjects.customer.userID,
                    'customerID': dataLayerSourceObjects.customer.customerID,
                    'customerType': dataLayerSourceObjects.customer.customerType,
                    'loyalty': dataLayerSourceObjects.customer.loyalty,
                    'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                    'loginType': dataLayerSourceObjects.customer.loginType,
                    'store_name': "Transmart Cempaka Putih",
                    'store_ID': "transmart_cempaka_putih",
                    "coachmark_step": step
                });
            }
        },

    })
});
